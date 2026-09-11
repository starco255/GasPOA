<?php

namespace App\Http\Controllers\Retailer;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\BusinessProfile;
use App\Models\WholesaleOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    /**
     * Display retailer's inventory with real data.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get retailer's business profile
        $businessProfile = BusinessProfile::where('user_id', $user->id)
            ->where('business_type', 'retailer')
            ->first();
        
        if (!$businessProfile) {
            return redirect()->route('retailer.settings.shop')
                             ->with('warning', 'Tafadhali kamilisha maelezo ya duka lako kwanza.');
        }
        
        // Fetch inventory with product details
        $inventory = Inventory::with('product.category')
            ->where('business_profile_id', $businessProfile->id)
            ->where('is_active', true)
            ->orderBy('last_updated', 'desc')
            ->get()
            ->map(function ($item) {
                $product = $item->product;
                
                return [
                    'id' => $item->id,
                    'product_id' => $product->id,
                    'image' => $product->image_url ?? 'product.png',
                    'name' => $product->name,
                    'type' => $product->service_type,
                    'qty' => $item->quantity,
                    'retail' => $item->price_override ?? $product->suggested_retail_price,
                    'wholesale' => $product->suggested_wholesale_price,
                    'updated' => $item->last_updated ? $item->last_updated->diffForHumans() : 'Hivi karibuni',
                    'is_low_stock' => $item->quantity <= 5,
                ];
            });
        
        // Get low stock count for alert
        $lowStockCount = $inventory->where('is_low_stock', true)->count();
        
        // Get recent wholesale orders
        $recentWholesaleOrders = WholesaleOrder::with('wholesaler')
            ->where('retailer_id', $businessProfile->id)
            ->latest('created_at')
            ->limit(3)
            ->get();
        
        return view('retailer.inventory.index', compact(
            'inventory', 
            'businessProfile', 
            'lowStockCount',
            'recentWholesaleOrders'
        ));
    }

    /**
     * Show form to add or update inventory.
     */
    public function update($id = null)
    {
        $user = Auth::user();
        
        // Get retailer's business profile
        $businessProfile = BusinessProfile::where('user_id', $user->id)
            ->where('business_type', 'retailer')
            ->first();
        
        if (!$businessProfile) {
            return redirect()->route('retailer.settings.shop')
                             ->with('warning', 'Tafadhali kamilisha maelezo ya duka lako kwanza.');
        }
        
        $inventory = null;
        if ($id) {
            $inventory = Inventory::with('product')
                ->where('business_profile_id', $businessProfile->id)
                ->findOrFail($id);
        }
        
        // Get all active products for dropdown
        $products = Product::active()
            ->orderBy('name')
            ->orderBy('weight_kg')
            ->get();
        
        return view('retailer.inventory.update', compact('inventory', 'products', 'businessProfile'));
    }

    /**
     * Store or update inventory.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        $businessProfile = BusinessProfile::where('user_id', $user->id)
            ->where('business_type', 'retailer')
            ->first();
        
        if (!$businessProfile) {
            return redirect()->route('retailer.settings.shop')
                             ->with('error', 'Haujaweka maelezo ya duka.');
        }
        
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'service_type' => 'required|in:new_cylinder,refill_exchange',
            'quantity' => 'required|integer|min:0',
            'price_override' => 'nullable|numeric|min:0',
        ]);
        
        // Check if inventory already exists for this product
        $inventory = Inventory::where('business_profile_id', $businessProfile->id)
            ->where('product_id', $validated['product_id'])
            ->first();
        
        if ($inventory) {
            // Update existing
            $inventory->update([
                'quantity' => $validated['quantity'],
                'price_override' => $validated['price_override'] ?: null,
                'last_updated' => now(),
            ]);
            
            $message = 'Hisa imesasishwa!';
        } else {
            // Create new
            Inventory::create([
                'business_profile_id' => $businessProfile->id,
                'product_id' => $validated['product_id'],
                'quantity' => $validated['quantity'],
                'price_override' => $validated['price_override'] ?: null,
                'is_active' => true,
                'last_updated' => now(),
            ]);
            
            $message = 'Hisa imeongezwa!';
        }
        
        return redirect()->route('retailer.inventory.index')
                         ->with('success', $message);
    }

    /**
     * Delete inventory item.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        
        $businessProfile = BusinessProfile::where('user_id', $user->id)
            ->where('business_type', 'retailer')
            ->first();
        
        if (!$businessProfile) {
            return redirect()->route('retailer.settings.shop')
                             ->with('error', 'Duka halijapatikana.');
        }
        
        $inventory = Inventory::where('business_profile_id', $businessProfile->id)
            ->findOrFail($id);
        
        // Soft delete - just mark as inactive
        $inventory->update(['is_active' => false]);
        
        return redirect()->route('retailer.inventory.index')
                         ->with('success', 'Hisa imefutwa.');
    }
}