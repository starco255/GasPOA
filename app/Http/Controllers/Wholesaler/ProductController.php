<?php

namespace App\Http\Controllers\Wholesaler;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\BusinessProfile;
use App\Models\WholesaleOrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    /**
     * Display all products with stock levels.
     */
    public function index()
    {
        $user = Auth::user();
        
        $businessProfile = BusinessProfile::where('user_id', $user->id)
            ->where('business_type', 'wholesaler')
            ->first();
        
        if (!$businessProfile) {
            return redirect()->route('wholesaler.settings.profile')
                             ->with('warning', 'Tafadhali kamilisha maelezo ya ghala lako kwanza.');
        }
        
        $wholesalerId = $businessProfile->id;
        
        // Get inventory with product details
        $inventory = Inventory::with('product.category')
            ->where('business_profile_id', $wholesalerId)
            ->where('is_active', true)
            ->get();
        
        $products = $inventory->map(function ($item) {
            $product = $item->product;
            return [
                'id' => $product->id,
                'name' => $product->name,
                'service_type' => $product->service_type,
                'stock' => $item->quantity,
                'wholesale' => $product->suggested_wholesale_price,
                'msrp' => $product->suggested_retail_price,
                'updated' => $item->last_updated ? $item->last_updated->diffForHumans() : 'Leo',
                'is_active' => $product->is_active ?? true,
            ];
        });
        
        $totalStock = $inventory->sum('quantity');
        $lowStockCount = $inventory->where('quantity', '<', 20)->count();
        $stockValue = $inventory->sum(function($item) {
            return $item->quantity * ($item->product->suggested_wholesale_price ?? 0);
        });
        
        // Get unique brands from categories for dropdown
        $brands = Category::active()->pluck('name')->unique()->values()->toArray();
        
        // ✅ GET UNIQUE WEIGHTS FROM PRODUCTS TABLE
        $availableWeights = Product::where('service_type', 'new_cylinder')
            ->where('is_active', true)
            ->whereNotNull('weight_kg')
            ->pluck('weight_kg')
            ->unique()
            ->sort()
            ->values()
            ->toArray();
        
        // ✅ GET ALL ACTIVE PRODUCTS FOR PRICE LOOKUP (used by JavaScript)
        $allProducts = Product::where('service_type', 'new_cylinder')
            ->where('is_active', true)
            ->get([
                'id', 
                'name', 
                'weight_kg', 
                'suggested_wholesale_price', 
                'suggested_retail_price',
                'category_id'
            ]);
        
        // ✅ MAP PRODUCTS WITH CATEGORY NAME FOR EASIER LOOKUP
        $allProducts = $allProducts->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'weight_kg' => (float) $product->weight_kg,
                'suggested_wholesale_price' => (float) $product->suggested_wholesale_price,
                'suggested_retail_price' => (float) $product->suggested_retail_price,
                'category_name' => $product->category->name ?? '',
            ];
        });
        
        return view('wholesaler.products.index', compact(
            'products',
            'totalStock',
            'lowStockCount',
            'stockValue',
            'brands',
            'businessProfile',
            'availableWeights',
            'allProducts'
        ));
    }
    
    /**
     * Store a new product with initial stock.
     * Wholesaler adds NEW CYLINDERS only. PRICES COME FROM ADMIN.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        $businessProfile = BusinessProfile::where('user_id', $user->id)
            ->where('business_type', 'wholesaler')
            ->first();
        
        if (!$businessProfile) {
            return redirect()->route('wholesaler.settings.profile')
                             ->with('error', 'Haujaweka maelezo ya ghala.');
        }
        
        // Wholesaler DOES NOT set prices - Admin does
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'brand_name' => 'required|string|max:50',
            'weight_kg' => 'required|numeric|min:0.1',
            'quantity' => 'required|integer|min:0',
            'description' => 'nullable|string',
            // Prices are optional here - Admin sets them via Admin Panel
            'wholesale_price' => 'nullable|numeric|min:0',
            'retail_price' => 'nullable|numeric|min:0',
        ], [
            'brand_name.required' => 'Tafadhali weka au chagua jina la chapa.',
            'weight_kg.required' => 'Tafadhali weka uzito wa mtungi.',
            'weight_kg.numeric' => 'Uzito lazima uwe namba.',
            'weight_kg.min' => 'Uzito lazima uwe angalau 0.1 kg.',
        ]);
        
        // ✅ USE ADMIN PRICES - FALLBACK TO AVERAGE IF NONE PROVIDED
        $wholesalePrice = !empty($validated['wholesale_price']) 
            ? (int) $validated['wholesale_price'] 
            : (Product::where('service_type', 'new_cylinder')->avg('suggested_wholesale_price') ?? 0);
        
        $retailPrice = !empty($validated['retail_price']) 
            ? (int) $validated['retail_price'] 
            : (Product::where('service_type', 'new_cylinder')->avg('suggested_retail_price') ?? 0);
        
        $weight = (float) $validated['weight_kg'];
        
        DB::beginTransaction();
        
        try {
            // Find or create category based on brand name
            $category = Category::firstOrCreate(
                ['name' => $validated['brand_name']],
                [
                    'description' => 'Bidhaa za gesi za chapa ya ' . $validated['brand_name'],
                    'is_active' => true
                ]
            );
            
            // Build product name from brand and weight
            $productName = $validated['name'] 
                ?: $validated['brand_name'] . ' ' . $weight . 'kg';
            
            // Check if product already exists (same name and service_type = new_cylinder)
            $product = Product::where('name', $productName)
                ->where('service_type', 'new_cylinder')
                ->first();
            
            if (!$product) {
                // ✅ Create new product with Admin prices
                $product = Product::create([
                    'category_id' => $category->id,
                    'name' => $productName,
                    'description' => $validated['description'] ?? null,
                    'service_type' => 'new_cylinder',
                    'suggested_retail_price' => $retailPrice,
                    'suggested_wholesale_price' => $wholesalePrice,
                    'weight_kg' => $weight,
                    'is_active' => true,
                ]);
            } else {
                // ✅ Update existing product prices if provided by Admin
                if (!empty($validated['wholesale_price']) || !empty($validated['retail_price'])) {
                    $product->update([
                        'suggested_retail_price' => $retailPrice,
                        'suggested_wholesale_price' => $wholesalePrice,
                        'weight_kg' => $weight,
                        'is_active' => true,
                    ]);
                }
            }
            
            // Add to inventory
            $inventory = Inventory::where('business_profile_id', $businessProfile->id)
                ->where('product_id', $product->id)
                ->first();
            
            if ($inventory) {
                $inventory->quantity += (int) $validated['quantity'];
                $inventory->last_updated = now();
                $inventory->is_active = true;
                $inventory->save();
            } else {
                Inventory::create([
                    'business_profile_id' => $businessProfile->id,
                    'product_id' => $product->id,
                    'quantity' => (int) $validated['quantity'],
                    'is_active' => true,
                    'last_updated' => now(),
                ]);
            }
            
            DB::commit();
            
            Log::info('Product added by wholesaler', [
                'product_name' => $productName,
                'wholesaler_id' => $businessProfile->id,
                'wholesale_price' => $wholesalePrice,
                'retail_price' => $retailPrice,
            ]);
            
            return redirect()->route('wholesaler.products.index')
                             ->with('success', 'Bidhaa "' . $productName . '" imeongezwa kikamilifu! Bei zinatumika kutoka kwa Admin.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Product store failed', [
                'error' => $e->getMessage(),
                'user_id' => $user->id
            ]);
            
            return back()->with('error', 'Imeshindikana kuongeza bidhaa. Tafadhali jaribu tena.')
                        ->withInput();
        }
    }
    
    /**
     * Add stock to existing product.
     */
    public function addStock(Request $request, $id)
    {
        $user = Auth::user();
        
        $businessProfile = BusinessProfile::where('user_id', $user->id)
            ->where('business_type', 'wholesaler')
            ->first();
        
        if (!$businessProfile) {
            return redirect()->route('wholesaler.products.index')
                             ->with('error', 'Haujaweka maelezo ya ghala.');
        }
        
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ], [
            'quantity.min' => 'Idadi ya kuongeza lazima iwe angalau 1.'
        ]);
        
        $inventory = Inventory::where('business_profile_id', $businessProfile->id)
            ->where('product_id', $id)
            ->first();
        
        $product = Product::find($id);
        $productName = $product->name ?? 'Bidhaa';
        
        if ($inventory) {
            $inventory->quantity += (int) $request->quantity;
            $inventory->last_updated = now();
            $inventory->save();
            $message = 'Stock imeongezwa kwa "' . $productName . '"! Sasa ina ' . number_format($inventory->quantity) . ' mitungi.';
        } else {
            $inventory = Inventory::create([
                'business_profile_id' => $businessProfile->id,
                'product_id' => $id,
                'quantity' => (int) $request->quantity,
                'is_active' => true,
                'last_updated' => now(),
            ]);
            $message = 'Bidhaa "' . $productName . '" imeongezwa kwenye ghala na stock ' . number_format($request->quantity) . '.';
        }
        
        return redirect()->route('wholesaler.products.index')
                         ->with('success', $message);
    }
    
    /**
     * Toggle product active status.
     */
    public function toggle($id)
    {
        $product = Product::findOrFail($id);
        $product->is_active = !$product->is_active;
        $product->save();
        
        $status = $product->is_active ? 'imewashwa' : 'imezimwa';
        
        return redirect()->route('wholesaler.products.index')
                         ->with('success', 'Bidhaa "' . $product->name . '" ' . $status . ' kikamilifu!');
    }
}