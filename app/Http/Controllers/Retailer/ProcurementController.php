<?php

namespace App\Http\Controllers\Retailer;

use App\Http\Controllers\Controller;
use App\Models\BusinessProfile;
use App\Models\Product;
use App\Models\WholesaleOrder;
use App\Models\WholesaleOrderItem;
use App\Models\Payment;
use App\Services\PaymentMethodService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProcurementController extends Controller
{
    /**
     * Browse wholesalers and their products with filters.
     */
    public function browse(Request $request)
    {
        $user = Auth::user();
        
        $businessProfile = BusinessProfile::where('user_id', $user->id)
            ->where('business_type', 'retailer')
            ->first();
        
        if (!$businessProfile) {
            return redirect()->route('retailer.settings.shop')
                             ->with('warning', 'Tafadhali kamilisha maelezo ya duka lako kwanza.');
        }
        
        // Get all products for filter dropdown
        $allProducts = Product::active()->orderBy('name')->get();
        
        // Get all wholesalers for filter dropdown
        $wholesalersList = BusinessProfile::where('business_type', 'wholesaler')
            ->where('is_open', true)
            ->get();
        
        // Build query for wholesalers with their products
        $query = BusinessProfile::with(['user', 'inventory.product'])
            ->where('business_type', 'wholesaler')
            ->where('is_open', true);
        
        // Apply filters
        if ($request->filled('wholesaler_id')) {
            $query->where('id', $request->wholesaler_id);
        }
        
        $wholesalers = $query->get()
            ->map(function ($wholesaler) use ($request) {
                // Get unique products from inventory with filters
                $products = $wholesaler->inventory
                    ->filter(function ($item) {
                        return $item->is_active && $item->quantity > 0;
                    })
                    ->filter(function ($item) use ($request) {
                        // Filter by product_id
                        if ($request->filled('product_id')) {
                            return $item->product_id == $request->product_id;
                        }
                        return true;
                    })
                    ->filter(function ($item) use ($request) {
                        // Filter by service_type
                        if ($request->filled('service_type')) {
                            return $item->product->service_type == $request->service_type;
                        }
                        return true;
                    })
                    ->map(function ($item) {
                        return [
                            'id' => $item->product_id,
                            'name' => $item->product->name ?? 'Bidhaa',
                            'wholesale_price' => $item->product->suggested_wholesale_price ?? 0,
                            'retail_price' => $item->product->suggested_retail_price ?? 0,
                            'available_qty' => $item->quantity,
                        ];
                    })
                    ->unique('id')
                    ->values();
                
                return [
                    'id' => $wholesaler->id,
                    'name' => $wholesaler->business_name,
                    'phone' => $wholesaler->user->phone_number ?? null,
                    'location' => $wholesaler->physical_address,
                    'products' => $products,
                ];
            })
            ->filter(function ($wholesaler) {
                return count($wholesaler['products']) > 0;
            });
        
        // Get cart summary for display
        $cartItems = session()->get('procurement_cart', []);
        $cartCount = count($cartItems);
        $cartTotal = 0;
        foreach($cartItems as $item) {
            $cartTotal += $item['quantity'] * $item['price'];
        }
        
        return view('retailer.procurement.browse', compact(
            'wholesalers', 
            'businessProfile', 
            'allProducts', 
            'wholesalersList',
            'cartCount',
            'cartTotal',
            'cartItems'
        ));
    }

    /**
     * Add item to cart (session-based).
     * Minimum order quantity is 5.
     */
    public function add(Request $request)
    {
        $request->validate([
            'wholesaler_id' => 'required|exists:business_profiles,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:5',
        ], [
            'quantity.min' => 'Kiwango cha chini cha kuagiza ni 5.'
        ]);
        
        $product = Product::findOrFail($request->product_id);
        $wholesaler = BusinessProfile::findOrFail($request->wholesaler_id);
        
        $cart = session()->get('procurement_cart', []);
        
        // Check if item already in cart from same wholesaler
        $key = $request->wholesaler_id . '-' . $request->product_id;
        
        if (isset($cart[$key])) {
            $cart[$key]['quantity'] += $request->quantity;
        } else {
            $cart[$key] = [
                'wholesaler_id' => $request->wholesaler_id,
                'wholesaler_name' => $wholesaler->business_name,
                'product_id' => $request->product_id,
                'product_name' => $product->name,
                'quantity' => $request->quantity,
                'price' => $product->suggested_wholesale_price,
                'retail_price' => $product->suggested_retail_price,
            ];
        }
        
        session()->put('procurement_cart', $cart);
        
        return redirect()->route('retailer.procurement.browse')
                         ->with('success', 'Bidhaa imeongezwa kwenye kikapu.');
    }

    /**
     * Update cart item quantity.
     */
    public function updateCart(Request $request, $key)
    {
        $request->validate([
            'quantity' => 'required|integer|min:5',
        ]);
        
        $cart = session()->get('procurement_cart', []);
        
        if (isset($cart[$key])) {
            $cart[$key]['quantity'] = $request->quantity;
            session()->put('procurement_cart', $cart);
        }
        
        return redirect()->route('retailer.procurement.cart')
                         ->with('success', 'Kikapu kimesasishwa.');
    }

    /**
     * Remove item from cart.
     */
    public function removeFromCart($key)
    {
        $cart = session()->get('procurement_cart', []);
        unset($cart[$key]);
        session()->put('procurement_cart', $cart);
        
        return redirect()->route('retailer.procurement.cart')
                         ->with('success', 'Bidhaa imeondolewa kwenye kikapu.');
    }

    /**
     * View cart.
     */
    public function cart(PaymentMethodService $paymentMethods)
    {
        $cartItems = session()->get('procurement_cart', []);
        
        $subtotal = 0;
        foreach ($cartItems as $item) {
            $subtotal += $item['quantity'] * $item['price'];
        }
        
        // Delivery fee - fixed for wholesale orders
        $deliveryFee = collect($cartItems)->pluck('wholesaler_id')->unique()->count() * 15000;
        $total = $subtotal + $deliveryFee;
        
        $wholesalerPaymentMethods = collect($cartItems)->pluck('wholesaler_id')->unique()->mapWithKeys(function ($id) use ($paymentMethods) {
            $wholesaler = BusinessProfile::whereKey($id)->where('business_type', 'wholesaler')->first();
            return [$id => $wholesaler ? array_map(
                fn ($method) => $paymentMethods->getMethodDetails($wholesaler, $method),
                $paymentMethods->getAllowedMethods($wholesaler)
            ) : []];
        })->all();

        return view('retailer.procurement.cart', compact('cartItems', 'subtotal', 'deliveryFee', 'total', 'wholesalerPaymentMethods'));
    }

    public function paymentMethods($wholesalerId, PaymentMethodService $paymentMethods)
    {
        $wholesaler = BusinessProfile::whereKey($wholesalerId)
            ->where('business_type', 'wholesaler')->where('is_open', true)->firstOrFail();

        return response()->json([
            'success' => true,
            'wholesaler' => ['id' => $wholesaler->id, 'name' => $wholesaler->business_name],
            'payment_methods' => array_map(fn ($method) => $paymentMethods->getMethodDetails($wholesaler, $method), $paymentMethods->getAllowedMethods($wholesaler)),
        ]);
    }

    /**
     * Checkout and create wholesale order.
     */
    public function checkout(Request $request, PaymentMethodService $paymentMethods)
    {
        $user = Auth::user();
        
        $businessProfile = BusinessProfile::where('user_id', $user->id)
            ->where('business_type', 'retailer')
            ->first();
        
        if (!$businessProfile) {
            return redirect()->route('retailer.settings.shop')
                             ->with('error', 'Haujaweka maelezo ya duka.');
        }
        
        $cartItems = session()->get('procurement_cart', []);
        
        if (empty($cartItems)) {
            return redirect()->route('retailer.procurement.cart')
                             ->with('error', 'Kikapu ni tupu.');
        }
        
        DB::beginTransaction();
        
        try {
            // Group items by wholesaler
            $grouped = [];
            foreach ($cartItems as $key => $item) {
                $wholesalerId = $item['wholesaler_id'];
                if (!isset($grouped[$wholesalerId])) {
                    $grouped[$wholesalerId] = [
                        'wholesaler_name' => $item['wholesaler_name'],
                        'items' => [],
                        'total' => 0,
                        'delivery_fee' => 15000,
                    ];
                }
                $grouped[$wholesalerId]['items'][] = $item;
                $grouped[$wholesalerId]['total'] += $item['quantity'] * $item['price'];
            }
            
            $orderNumbers = [];
            $paymentInstructions = [];
            
            // Create orders for each wholesaler
            foreach ($grouped as $wholesalerId => $data) {
                $wholesaler = BusinessProfile::whereKey($wholesalerId)
                    ->where('business_type', 'wholesaler')->where('is_open', true)->firstOrFail();
                $method = data_get($request->input('payment_methods', []), $wholesalerId);
                $allowedMethods = $paymentMethods->getAllowedMethods($wholesaler);
                if (!in_array($method, $allowedMethods, true)) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'payment_methods.' . $wholesalerId => 'Njia hii ya malipo haikubaliki na muuzaji huyu.',
                    ]);
                }
                $paymentDetails = $paymentMethods->getMethodDetails($wholesaler, $method);
                $paymentInstructions[] = match ($method) {
                    'cash' => $wholesaler->business_name . ': Pesa Taslimu',
                    'bank' => $wholesaler->business_name . ': ' . ($paymentDetails['bank_name'] ?: 'Benki') . ' — ' . ($paymentDetails['account_number'] ?: 'Haijawekwa'),
                    default => $wholesaler->business_name . ': ' . $paymentDetails['label'] . ' — Lipa Namba ' . ($paymentDetails['account'] ?: 'Haijawekwa'),
                };
                $orderNumber = 'WHO-' . date('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
                $orderNumbers[] = $orderNumber;
                
                $order = WholesaleOrder::create([
                    'order_number' => $orderNumber,
                    'retailer_id' => $businessProfile->id,
                    'wholesaler_id' => $wholesalerId,
                    'total_amount' => $data['total'] + $data['delivery_fee'],
                    'status' => 'pending',
                    'payment_method' => $method,
                    'payment_status' => 'pending',
                    'delivery_address' => $businessProfile->physical_address,
                    'created_at' => now(),
                ]);
                
                foreach ($data['items'] as $item) {
                    WholesaleOrderItem::create([
                        'wholesale_order_id' => $order->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'price_per_item' => $item['price'],
                    ]);
                }
                if ($method !== 'cash') {
                    Payment::create([
                        'order_type' => 'wholesale', 'order_id' => $order->id, 'payment_method' => $method,
                        'provider' => 'manual',
                        'provider_reference' => 'GPOAW' . $order->id . \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(10)),
                        'status' => 'pending', 'amount' => $data['total'] + $data['delivery_fee'],
                    ]);
                }
            }
            
            // Clear cart
            session()->forget('procurement_cart');
            
            DB::commit();
            
            $orderNumbersStr = implode(', ', $orderNumbers);
            
            // Build success message with payment instructions
            $message = "Agizo lako la jumla limepokelewa! Namba za agizo: {$orderNumbersStr}. ";
            
            $message .= 'Taarifa za malipo: ' . implode(' | ', $paymentInstructions) . '. Malipo yasiyo ya taslimu yatathibitishwa na wholesaler.';
            
            return redirect()->route('retailer.procurement.wholesale_orders')
                             ->with('success', $message);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Imeshindikana kuweka agizo. Tafadhali jaribu tena.');
        }
    }

    /**
     * View wholesale orders history.
     */
    public function wholesaleOrders()
    {
        $user = Auth::user();
        
        $businessProfile = BusinessProfile::where('user_id', $user->id)
            ->where('business_type', 'retailer')
            ->first();
        
        if (!$businessProfile) {
            return redirect()->route('retailer.settings.shop');
        }
        
        $orders = WholesaleOrder::with(['wholesaler', 'items.product'])
            ->where('retailer_id', $businessProfile->id)
            ->latest('created_at')
            ->paginate(15);
        
        return view('retailer.procurement.wholesale_orders', compact('orders'));
    }
}
