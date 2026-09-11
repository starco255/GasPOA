<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\DistancePricing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PricingController extends Controller
{
    /**
     * Show pricing management page.
     */
    public function index()
    {
        $products = Product::with('category')
            ->orderBy('name')
            ->orderBy('weight_kg')
            ->get();
        
        $distances = DistancePricing::orderBy('from_km')->get();
        
        $brands = Category::where('is_active', true)
            ->orderBy('name')
            ->pluck('name')
            ->toArray();
        
        return view('admin.pricing.index', compact('products', 'distances', 'brands'));
    }
    
    /**
     * Update product prices.
     */
    public function updateProducts(Request $request)
    {
        $validated = $request->validate([
            'wholesale' => 'required|array',
            'wholesale.*' => 'required|numeric|min:0',
            'retail' => 'required|array',
            'retail.*' => 'required|numeric|min:0',
        ]);
        
        $updatedCount = 0;
        
        foreach ($validated['wholesale'] as $productId => $wholesalePrice) {
            $product = Product::find($productId);
            if ($product) {
                $product->suggested_wholesale_price = $wholesalePrice;
                $product->suggested_retail_price = $validated['retail'][$productId] ?? $product->suggested_retail_price;
                $product->save();
                $updatedCount++;
            }
        }
        
        Log::info('Product prices updated', [
            'admin_id' => auth()->id(),
            'updated_count' => $updatedCount,
        ]);
        
        return redirect()->route('admin.pricing.index')
                         ->with('success', "Bei za bidhaa {$updatedCount} zimesasishwa!");
    }
    
    /**
     * Update delivery pricing.
     */
    public function updateDelivery(Request $request)
    {
        $validated = $request->validate([
            'from' => 'required|array',
            'from.*' => 'required|numeric|min:0',
            'to' => 'required|array',
            'to.*' => 'required|numeric|min:0',
            'fee' => 'required|array',
            'fee.*' => 'required|numeric|min:0',
        ]);
        
        DB::beginTransaction();
        
        try {
            DistancePricing::truncate();
            
            $count = count($validated['from']);
            for ($i = 0; $i < $count; $i++) {
                if (empty($validated['from'][$i]) && empty($validated['to'][$i]) && empty($validated['fee'][$i])) {
                    continue;
                }
                
                DistancePricing::create([
                    'from_km' => $validated['from'][$i],
                    'to_km' => $validated['to'][$i],
                    'delivery_fee' => $validated['fee'][$i],
                    'currency' => 'TZS',
                ]);
            }
            
            DB::commit();
            
            Log::info('Delivery pricing updated', [
                'admin_id' => auth()->id(),
                'entries' => $count,
            ]);
            
            return redirect()->route('admin.pricing.index')
                             ->with('success', "Gharama za usafirishaji zimesasishwa!");
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to update delivery pricing', [
                'error' => $e->getMessage(),
            ]);
            
            return redirect()->route('admin.pricing.index')
                             ->with('error', 'Imeshindikana kuhifadhi. Tafadhali jaribu tena.');
        }
    }

    /**
     * Store a new product from Admin Panel.
     */
    public function storeProduct(Request $request)
    {
        $validated = $request->validate([
            'service_type' => 'required|in:new_cylinder,refill_exchange',
            'brand_name' => 'required|string|max:50',
            'weight_kg' => 'required|numeric|min:1',
            'suggested_wholesale_price' => 'required|numeric|min:0',
            'suggested_retail_price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);
        
        $category = Category::firstOrCreate(
            ['name' => $validated['brand_name']],
            [
                'description' => 'Bidhaa za gesi za chapa ya ' . $validated['brand_name'],
                'is_active' => true
            ]
        );
        
        $suffix = $validated['service_type'] === 'refill_exchange' ? ' Refill' : '';
        $productName = $validated['brand_name'] . ' ' . $validated['weight_kg'] . 'kg' . $suffix;
        
        $existingProduct = Product::where('name', $productName)
            ->where('service_type', $validated['service_type'])
            ->first();
        
        if ($existingProduct) {
            $existingProduct->update([
                'category_id' => $category->id,
                'suggested_retail_price' => $validated['suggested_retail_price'],
                'suggested_wholesale_price' => $validated['suggested_wholesale_price'],
                'weight_kg' => $validated['weight_kg'],
                'description' => $validated['description'] ?? $existingProduct->description,
                'is_active' => true,
            ]);
            
            return redirect()->route('admin.pricing.index')
                             ->with('success', 'Bidhaa "' . $productName . '" tayari ilikuwepo. Bei zimesasishwa!');
        }
        
        Product::create([
            'category_id' => $category->id,
            'name' => $productName,
            'description' => $validated['description'] ?? null,
            'service_type' => $validated['service_type'],
            'suggested_retail_price' => $validated['suggested_retail_price'],
            'suggested_wholesale_price' => $validated['suggested_wholesale_price'],
            'weight_kg' => $validated['weight_kg'],
            'is_active' => true,
        ]);
        
        Log::info('Admin created new product', [
            'product_name' => $productName,
            'admin_id' => auth()->id(),
        ]);
        
        return redirect()->route('admin.pricing.index')
                         ->with('success', 'Bidhaa "' . $productName . '" imesajiliwa kikamilifu!');
    }

    /**
     * Store a new category/brand from Admin Panel.
     */
    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:categories,name',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'Tafadhali weka jina la chapa.',
            'name.unique' => 'Chapa hii tayari imesajiliwa. Tafadhali tumia jina lingine.',
        ]);
        
        $category = Category::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? 'Bidhaa za gesi za chapa ya ' . $validated['name'],
            'is_active' => $request->has('is_active'),
        ]);
        
        Log::info('Admin created new category/brand', [
            'admin_id' => auth()->id(),
            'category_name' => $category->name,
        ]);
        
        return redirect()->route('admin.pricing.index')
                         ->with('success', 'Chapa "' . $category->name . '" imesajiliwa kikamilifu!');
    }

    /**
     * Display all products with tabs (Categories & Products).
     */
    public function productsIndex()
    {
        $categories = Category::withCount('products')
            ->orderBy('name')
            ->get();
        
        $products = Product::with('category')
            ->orderBy('name')
            ->get();
        
        $brands = Category::where('is_active', true)
            ->orderBy('name')
            ->pluck('name')
            ->toArray();
        
        return view('admin.products.index', compact('categories', 'products', 'brands'));
    }

    /**
     * Show edit product form.
     */
    public function editProduct($id)
    {
        $product = Product::with('category')->findOrFail($id);
        $brands = Category::where('is_active', true)->orderBy('name')->pluck('name')->toArray();
        
        return view('admin.products.edit', compact('product', 'brands'));
    }

    /**
     * Update a product.
     */
    public function updateProduct(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        
        $validated = $request->validate([
            'service_type' => 'required|in:new_cylinder,refill_exchange',
            'brand_name' => 'required|string|max:50',
            'weight_kg' => 'required|numeric|min:1',
            'suggested_wholesale_price' => 'required|numeric|min:0',
            'suggested_retail_price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        
        $category = Category::firstOrCreate(
            ['name' => $validated['brand_name']],
            ['description' => 'Bidhaa za gesi za chapa ya ' . $validated['brand_name'], 'is_active' => true]
        );
        
        $suffix = $validated['service_type'] === 'refill_exchange' ? ' Refill' : '';
        $productName = $validated['brand_name'] . ' ' . $validated['weight_kg'] . 'kg' . $suffix;
        
        $product->update([
            'category_id' => $category->id,
            'name' => $productName,
            'description' => $validated['description'],
            'service_type' => $validated['service_type'],
            'suggested_retail_price' => $validated['suggested_retail_price'],
            'suggested_wholesale_price' => $validated['suggested_wholesale_price'],
            'weight_kg' => $validated['weight_kg'],
            'is_active' => $request->has('is_active'),
        ]);
        
        return redirect()->route('admin.products.index')
                         ->with('success', 'Bidhaa "' . $productName . '" imesasishwa!');
    }

    /**
     * Delete a product (soft delete).
     */
    public function destroyProduct($id)
    {
        $product = Product::findOrFail($id);
        $productName = $product->name;
        
        $product->update([
            'is_active' => false,
            'deleted_at' => now(),
        ]);
        
        Log::info('Admin deleted product', [
            'admin_id' => auth()->id(),
            'product_id' => $id,
            'product_name' => $productName,
        ]);
        
        return redirect()->route('admin.products.index')
                         ->with('success', 'Bidhaa "' . $productName . '" imefutwa!');
    }

    /**
     * ✅ Delete a category/brand (Admin)
     */
    public function destroyCategory($id)
    {
        $category = Category::findOrFail($id);
        $categoryName = $category->name;
        
        // Check if category has products
        $productCount = Product::where('category_id', $id)->count();
        
        if ($productCount > 0) {
            return redirect()->route('admin.products.index')
                             ->with('error', 'Haiwezekani kufuta chapa "' . $categoryName . '" kwa sababu ina bidhaa ' . $productCount . ' zilizosajiliwa. Futa bidhaa zote za chapa hii kwanza.');
        }
        
        $category->delete();
        
        Log::info('Admin deleted category/brand', [
            'admin_id' => auth()->id(),
            'category_id' => $id,
            'category_name' => $categoryName,
        ]);
        
        return redirect()->route('admin.products.index')
                         ->with('success', 'Chapa "' . $categoryName . '" imefutwa kikamilifu!');
    }

    /**
 * Show edit category form.
 */
public function editCategory($id)
{
    $category = Category::findOrFail($id);
    
    return view('admin.categories.edit', compact('category'));
}

/**
 * Update a category.
 */
public function updateCategory(Request $request, $id)
{
    $category = Category::findOrFail($id);
    
    $validated = $request->validate([
        'name' => 'required|string|max:50|unique:categories,name,' . $id,
        'description' => 'nullable|string',
        'is_active' => 'boolean',
    ], [
        'name.required' => 'Tafadhali weka jina la chapa.',
        'name.unique' => 'Chapa hii tayari imesajiliwa. Tafadhali tumia jina lingine.',
    ]);
    
    $category->update([
        'name' => $validated['name'],
        'description' => $validated['description'],
        'is_active' => $request->has('is_active'),
    ]);
    
    Log::info('Admin updated category/brand', [
        'admin_id' => auth()->id(),
        'category_id' => $category->id,
        'category_name' => $category->name,
    ]);
    
    return redirect()->route('admin.products.index', '#categories')
                     ->with('success', 'Chapa "' . $category->name . '" imesasishwa!');
}
}