<?php

namespace App\Http\Controllers\Consumer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\RetailOrder;
use App\Models\RetailOrderItem;
use App\Models\BusinessProfile;
use App\Models\DistancePricing;
use App\Models\Address;
use App\Models\Payment;
use App\Services\PaymentMethodService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class OrderController extends Controller
{
    /**
     * Return matching area suggestions based on user input.
     * Inachuja maeneo yenye wauzaji walio na bidhaa husika (kama imechaguliwa).
     */
    public function addressSuggestions(Request $request)
    {
        $query = trim($request->get('query', ''));
        $productId = $request->get('product_id');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $retailersQuery = BusinessProfile::where('business_type', 'retailer')
            ->where('is_open', true)
            ->where('can_deliver', true)
            ->whereNotNull('physical_address');

        if ($productId) {
            $retailersQuery->whereHas('inventory', function ($q) use ($productId) {
                $q->where('product_id', $productId)
                  ->where('is_active', true)
                  ->where('quantity', '>', 0);
            });
        }

        $retailers = $retailersQuery->get(['physical_address', 'shop_latitude', 'shop_longitude']);

        $areas = [];
        foreach ($retailers as $r) {
            $parts = explode(',', $r->physical_address);
            $firstPart = trim($parts[0]);
            $words = explode(' ', $firstPart);
            $areaName = $words[0] ?? $firstPart;
            $key = strtolower($areaName);

            if (!isset($areas[$key])) {
                $areas[$key] = [
                    'name' => $areaName,
                    'full_address' => $r->physical_address,
                    'latitude' => $r->shop_latitude,
                    'longitude' => $r->shop_longitude,
                ];
            }
        }

        $filtered = array_filter($areas, function ($area) use ($query) {
            return stripos($area['name'], $query) !== false;
        });

        return response()->json(array_values($filtered));
    }

    /**
     * Show the order creation form.
     */
    public function create($type = null)
    {
        $user = Auth::user();
        
        $products = Product::where('is_active', true)
            ->orderBy('name')
            ->orderBy('weight_kg')
            ->get();

        $newCylinders = $products->where('service_type', 'new_cylinder');
        $refillProducts = $products->where('service_type', 'refill_exchange');
        
        $serviceType = $type ?? 'refill';
        $isNew = $serviceType === 'new';
        
        $recentAddresses = RetailOrder::where('consumer_id', $user->id)
            ->whereNotNull('delivery_address')
            ->select('delivery_address', 'delivery_latitude', 'delivery_longitude')
            ->distinct()
            ->latest('created_at')
            ->limit(3)
            ->get();
        
        $distancePricing = DistancePricing::orderBy('from_km')->get();

        // Anwani za mtumiaji kutoka kwenye mipangilio yake
        $userAddresses = Address::where('user_id', $user->id)
            ->orderBy('is_default', 'desc')
            ->get();

        return view('consumer.order.create', compact(
            'products',
            'newCylinders',
            'refillProducts',
            'serviceType',
            'isNew',
            'recentAddresses',
            'distancePricing',
            'userAddresses'
        ));
    }

    /**
     * Store a newly created order (fallback method).
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:10',
            'delivery_address' => 'required|string|max:500',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'urgency' => 'required|in:normal,urgent',
            'payment_method' => ['required', Rule::in(PaymentMethodService::METHODS)],
            'service_type' => 'required|in:new_cylinder,refill_exchange',
        ]);

        DB::beginTransaction();
        
        try {
            $product = Product::where('is_active', true)->findOrFail($validated['product_id']);
            $serviceType = $validated['service_type'];
            
            $pricePerItem = $serviceType === 'new_cylinder' 
                ? $product->suggested_retail_price 
                : $product->suggested_wholesale_price;
            
            $nearestRetailer = $this->findNearestRetailerWithStock(
                $validated['latitude'],
                $validated['longitude'],
                $product->id,
                $validated['quantity']
            );
            
            if (!$nearestRetailer) {
                DB::rollBack();
                return back()->with('error', 'Samahani, hakuna muuzaji wa karibu mwenye bidhaa hii kwa sasa.')
                            ->withInput();
            }
            $allowedMethods = app(PaymentMethodService::class)->getAllowedMethods($nearestRetailer['business']);
            if (!in_array($validated['payment_method'], $allowedMethods, true)) {
                return back()->withErrors(['payment_method' => 'Njia uliyochagua haijawezeshwa na muuzaji huyu.'])->withInput();
            }
            
            $subtotal = $pricePerItem * $validated['quantity'];
            $isUrgent = $validated['urgency'] === 'urgent';
            $deliveryFee = 0;
            $urgencyFee = $isUrgent ? 3000 : 0;
            $totalAmount = $subtotal + $deliveryFee + $urgencyFee;
            
            $orderNumber = $this->generateOrderNumber();
            
            $order = RetailOrder::create([
                'order_number' => $orderNumber,
                'consumer_id' => $user->id,
                'retailer_id' => $nearestRetailer['business']->id,
                'delivery_address' => $validated['delivery_address'],
                'delivery_latitude' => $validated['latitude'],
                'delivery_longitude' => $validated['longitude'],
                'status' => 'pending',
                'urgency_level' => $validated['urgency'],
                'total_amount' => $totalAmount,
                'payment_method' => $validated['payment_method'] === 'cash' ? 'cash' : ($validated['payment_method'] === 'bank' ? 'card' : 'mobile_money'),
                'payment_method_provider' => $validated['payment_method'],
                'payment_status' => 'pending',
                'created_at' => now(),
            ]);
            
            RetailOrderItem::create([
                'retail_order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $validated['quantity'],
                'service_type_requested' => $serviceType,
                'price_per_item' => $pricePerItem,
            ]);

            if ($validated['payment_method'] !== 'cash') {
                Payment::create([
                    'order_type' => 'retail', 'order_id' => $order->id, 'payment_method' => $validated['payment_method'],
                    'provider' => 'manual', 'provider_reference' => 'GPOAR' . $order->id . strtoupper(str()->random(10)),
                    'status' => 'pending', 'amount' => $totalAmount,
                ]);
            }

            DB::commit();
            
            Log::info('New order created', [
                'order_number' => $orderNumber,
                'total_amount' => $totalAmount,
                'urgency' => $validated['urgency'],
                'subtotal' => $subtotal,
                'urgency_fee' => $urgencyFee,
            ]);
            
            return redirect()->route('consumer.order.tracking', ['id' => $order->id])
                             ->with('success', 'Agizo lako limepokelewa! ' . $this->paymentReminder($nearestRetailer['business'], $validated['payment_method']));
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order creation failed', ['error' => $e->getMessage()]);
            return back()->with('error', 'Imeshindikana kuweka agizo. Tafadhali jaribu tena.')->withInput();
        }
    }

    /**
     * Find retailers near the delivery location.
     */
    public function findRetailers(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:10',
            'delivery_address' => 'required|string|max:500',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'urgency' => 'required|in:normal,urgent',
            'service_type' => 'required|in:new_cylinder,refill_exchange',
        ]);
        
        $product = Product::where('is_active', true)->findOrFail($validated['product_id']);
        
        $retailers = BusinessProfile::where('business_type', 'retailer')
            ->where('is_open', true)
            ->where('can_deliver', true)
            ->whereHas('inventory', function ($query) use ($product, $validated) {
                $query->where('product_id', $product->id)
                      ->where('is_active', true)
                      ->where('quantity', '>=', $validated['quantity']);
            })
            ->get()
            ->map(function ($retailer) use ($validated, $product) {
                $distance = $this->calculateDistance(
                    $validated['latitude'],
                    $validated['longitude'],
                    $retailer->shop_latitude,
                    $retailer->shop_longitude
                );
                
                if ($distance > $retailer->service_radius_km) {
                    return null;
                }
                
                $actualDeliveryFee = $this->calculateDeliveryFee($distance);
                
                return [
                    'id' => $retailer->id,
                    'business_name' => $retailer->business_name,
                    'physical_address' => $retailer->physical_address,
                    'shop_latitude' => $retailer->shop_latitude,
                    'shop_longitude' => $retailer->shop_longitude,
                    'distance' => round($distance, 2),
                    'delivery_fee' => $actualDeliveryFee,
                    'estimated_delivery' => ceil(($distance / 30) * 60) + 15,
                ];
            })
            ->filter()
            ->sortBy('distance')
            ->values();
        
        Session::put('pending_order', $validated);
        Session::put('pending_product', $product->toArray());
        
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => $retailers->isNotEmpty(),
                'retailers' => $retailers,
                'product' => $product,
                'validated' => $validated,
            ]);
        }
        
        if ($retailers->isEmpty()) {
            return back()->with('warning', 'Hakuna muuzaji wa karibu mwenye bidhaa hii.');
        }
        
        return view('consumer.order.select-retailer', compact('retailers', 'product', 'validated'));
    }

    /**
     * Show retailer selection page (fallback).
     */
    public function selectRetailer()
    {
        return redirect()->route('consumer.order.create');
    }

    /** Return only the selected, available retailer's configured payment methods. */
    public function paymentMethods($retailerId, PaymentMethodService $paymentMethods)
    {
        $retailer = BusinessProfile::whereKey($retailerId)
            ->where('business_type', 'retailer')->where('is_open', true)->firstOrFail();

        return response()->json([
            'success' => true,
            'retailer' => ['id' => $retailer->id, 'name' => $retailer->business_name],
            'payment_methods' => array_map(fn ($method) => $paymentMethods->getMethodDetails($retailer, $method), $paymentMethods->getAllowedMethods($retailer)),
        ]);
    }

    /**
     * Place order with selected retailer.
     */
    public function placeOrder(Request $request, PaymentMethodService $paymentMethods)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            // The retailer is checked again below with its required business state.
            'retailer_id' => 'required|integer',
        ]);
        
        $pendingOrder = Session::get('pending_order');
        $productData = Session::get('pending_product');
        
        if (!$pendingOrder || !$productData) {
            return redirect()->route('consumer.order.create')
                             ->with('error', 'Muda wa kikao umeisha. Tafadhali jaribu tena.');
        }
        
        $product = (object) $productData;
        $retailer = BusinessProfile::whereKey($validated['retailer_id'])
            ->where('business_type', 'retailer')->where('is_open', true)->where('can_deliver', true)->firstOrFail();
        $allowedMethods = $paymentMethods->getAllowedMethods($retailer);
        $payment = $request->validate(['payment_method' => ['required', Rule::in($allowedMethods)]])['payment_method'];

        if (!$retailer->hasProductInStock($pendingOrder['product_id'], $pendingOrder['quantity']) ||
            $this->calculateDistance($pendingOrder['latitude'], $pendingOrder['longitude'], $retailer->shop_latitude, $retailer->shop_longitude) > $retailer->service_radius_km) {
            return back()->withErrors(['retailer_id' => 'Muuzaji huyu hawezi kuhudumia agizo hili kwa sasa.']);
        }
        
        DB::beginTransaction();
        
        try {
            $pricePerItem = $pendingOrder['service_type'] === 'new_cylinder' 
                ? $product->suggested_retail_price 
                : $product->suggested_wholesale_price;
            $subtotal = $pricePerItem * $pendingOrder['quantity'];
            
            $isUrgent = $pendingOrder['urgency'] === 'urgent';
            $deliveryFee = 0;
            $urgencyFee = $isUrgent ? 3000 : 0;
            $totalAmount = $subtotal + $deliveryFee + $urgencyFee;
            
            $orderNumber = $this->generateOrderNumber();
            
            $order = RetailOrder::create([
                'order_number' => $orderNumber,
                'consumer_id' => $user->id,
                'retailer_id' => $retailer->id,
                'delivery_address' => $pendingOrder['delivery_address'],
                'delivery_latitude' => $pendingOrder['latitude'],
                'delivery_longitude' => $pendingOrder['longitude'],
                'status' => 'pending',
                'urgency_level' => $pendingOrder['urgency'],
                'total_amount' => $totalAmount,
                // Preserve the existing enum; provider-specific value is additive.
                'payment_method' => $payment === 'cash' ? 'cash' : ($payment === 'bank' ? 'card' : 'mobile_money'),
                'payment_method_provider' => $payment,
                'payment_status' => 'pending',
                'created_at' => now(),
            ]);
            
            RetailOrderItem::create([
                'retail_order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $pendingOrder['quantity'],
                'service_type_requested' => $pendingOrder['service_type'],
                'price_per_item' => $pricePerItem,
            ]);

            if ($payment !== 'cash') {
                Payment::create([
                    'order_type' => 'retail', 'order_id' => $order->id, 'payment_method' => $payment,
                    'provider' => 'manual',
                    'provider_reference' => 'GPOAR' . $order->id . \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(10)),
                    'status' => 'pending', 'amount' => $totalAmount,
                ]);
            }
            
            DB::commit();
            
            Session::forget(['pending_order', 'pending_product']);
            
            Log::info('New order created via retailer selection', [
                'order_number' => $orderNumber,
                'retailer_id' => $retailer->id,
                'total_amount' => $totalAmount,
                'urgency' => $pendingOrder['urgency'],
                'subtotal' => $subtotal,
                'urgency_fee' => $urgencyFee,
            ]);
            
            return redirect()->route('consumer.order.tracking', ['id' => $order->id])
                             ->with('success', 'Agizo lako limepokelewa! ' . $retailer->business_name . ' atakufikishia bidhaa yako. ' . $this->paymentReminder($retailer, $payment));
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order placement failed', ['error' => $e->getMessage()]);
            return back()->with('error', 'Imeshindikana kuweka agizo. Tafadhali jaribu tena.');
        }
    }

    /**
     * Find the nearest retailer with sufficient stock.
     */
    private function findNearestRetailerWithStock($latitude, $longitude, $productId, $quantity)
    {
        $retailers = BusinessProfile::where('business_type', 'retailer')
            ->where('is_open', true)
            ->where('can_deliver', true)
            ->whereHas('inventory', function ($query) use ($productId, $quantity) {
                $query->where('product_id', $productId)
                      ->where('is_active', true)
                      ->where('quantity', '>=', $quantity);
            })
            ->get();
        
        if ($retailers->isEmpty()) {
            return null;
        }
        
        $nearestRetailer = null;
        $shortestDistance = PHP_FLOAT_MAX;
        
        foreach ($retailers as $retailer) {
            $distance = $this->calculateDistance(
                $latitude,
                $longitude,
                $retailer->shop_latitude,
                $retailer->shop_longitude
            );
            
            if ($distance <= $retailer->service_radius_km && $distance < $shortestDistance) {
                $shortestDistance = $distance;
                $inventory = $retailer->inventory->first();
                $nearestRetailer = [
                    'business' => $retailer,
                    'inventory' => $inventory,
                    'distance' => $distance,
                ];
            }
        }
        
        return $nearestRetailer;
    }

    /** Build a clear payment reminder without changing any stored order data. */
    private function paymentReminder(BusinessProfile $retailer, string $method): string
    {
        if ($method === 'cash') {
            return 'Ulichagua Pesa Taslimu; lipa kwa muuzaji wakati wa kupokea bidhaa.';
        }

        $details = app(PaymentMethodService::class)->getMethodDetails($retailer, $method);
        if (!empty($details['account'])) {
            return "Tafadhali lipa kwa {$details['label']} kwenye namba {$details['account']}.";
        }
        if (!empty($details['account_number'])) {
            return "Tafadhali lipa kupitia {$details['bank_name']} kwenye akaunti {$details['account_number']} ({$details['account_name']}).";
        }

        return 'Tafadhali angalia maelekezo ya malipo kwenye ukurasa wa kufuatilia agizo.';
    }

    /**
     * Calculate distance between two points using Haversine formula.
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371;
        
        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);
        
        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;
        
        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        
        return $angle * $earthRadius;
    }

    /**
     * Calculate delivery fee based on distance (for display only).
     */
    private function calculateDeliveryFee($distance)
    {
        $pricing = DistancePricing::where('from_km', '<=', $distance)
            ->where('to_km', '>=', $distance)
            ->first();
        
        if ($pricing) {
            return $pricing->delivery_fee;
        }
        
        $maxPricing = DistancePricing::orderBy('to_km', 'desc')->first();
        if ($maxPricing && $distance > $maxPricing->to_km) {
            return $maxPricing->delivery_fee + (($distance - $maxPricing->to_km) * 1000);
        }
        
        return 5000;
    }

    /**
     * Generate unique order number.
     */
    private function generateOrderNumber()
    {
        $prefix = 'GPOA-';
        $date = date('Ymd');
        
        do {
            $suffix = str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);
            $orderNumber = $prefix . $date . '-' . $suffix;
        } while (RetailOrder::where('order_number', $orderNumber)->exists());
        
        return $orderNumber;
    }

    /**
     * Track an order.
     */
    public function tracking($id = null)
    {
        $user = Auth::user();
        
        if (!$id) {
            $order = RetailOrder::with(['items.product', 'retailer.user'])
                ->where('consumer_id', $user->id)
                ->whereNotIn('status', ['delivered', 'cancelled'])
                ->latest('created_at')
                ->first();
                
            if (!$order) {
                return redirect()->route('consumer.dashboard')
                                 ->with('info', 'Huna agizo linaloendelea kwa sasa.');
            }
        } else {
            $order = RetailOrder::with(['items.product', 'retailer.user'])
                ->where('consumer_id', $user->id)
                ->find($id);
                
            if (!$order) {
                return redirect()->route('consumer.dashboard')
                                 ->with('error', 'Agizo halikupatikana.');
            }
        }
        
        $messages = \App\Models\Message::where('order_id', $order->id)
            ->with(['sender', 'receiver'])
            ->orderBy('created_at')
            ->get();
        
        $estimatedDelivery = null;
        if ($order->retailer && !in_array($order->status, ['delivered', 'cancelled'])) {
            $estimatedDelivery = $this->calculateEstimatedDelivery($order);
        }

        $manualPayment = Payment::where('order_type', 'retail')
            ->where('order_id', $order->id)
            ->latest('id')
            ->first();
        
        return view('consumer.order.tracking', compact('order', 'messages', 'estimatedDelivery', 'manualPayment'));
    }

    /**
     * Calculate estimated delivery time.
     */
    private function calculateEstimatedDelivery($order)
    {
        if (!$order->retailer) {
            return now()->addMinutes(45);
        }
        
        $distance = $this->calculateDistance(
            $order->delivery_latitude,
            $order->delivery_longitude,
            $order->retailer->shop_latitude,
            $order->retailer->shop_longitude
        );
        
        $timeInMinutes = ceil(($distance / 30) * 60) + 15;
        
        if ($order->urgency_level === 'urgent') {
            $timeInMinutes = max(20, ceil($timeInMinutes * 0.6));
        }
        
        return now()->addMinutes($timeInMinutes);
    }

    /**
     * Show order history.
     */
    public function history(Request $request)
    {
        $user = Auth::user();
        
        $query = RetailOrder::with(['items.product', 'retailer'])
            ->where('consumer_id', $user->id)
            ->orderBy('created_at', 'desc');
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('search')) {
            $query->where('order_number', 'like', '%' . $request->search . '%');
        }
        
        $orders = $query->paginate(10)->withQueryString();
        
        $stats = [
            'total_orders' => RetailOrder::where('consumer_id', $user->id)->count(),
            'total_spent' => RetailOrder::where('consumer_id', $user->id)
                ->where('payment_status', 'paid')
                ->sum('total_amount'),
            'active_orders' => RetailOrder::where('consumer_id', $user->id)
                ->whereNotIn('status', ['delivered', 'cancelled'])
                ->count(),
        ];
        
        return view('consumer.order.history', compact('orders', 'stats'));
    }

    /**
     * Show order details.
     */
    public function details($id)
    {
        $user = Auth::user();
        
        $order = RetailOrder::with(['items.product', 'retailer.user'])
            ->where('consumer_id', $user->id)
            ->findOrFail($id);
        
        return view('consumer.order.details', compact('order'));
    }

    /**
     * Reorder a previous order.
     */
    public function reorder($id)
    {
        $user = Auth::user();
        
        $order = RetailOrder::with(['items.product'])
            ->where('consumer_id', $user->id)
            ->findOrFail($id);
        
        $firstItem = $order->items->first();
        $serviceType = $firstItem ? $firstItem->service_type_requested : 'refill_exchange';
        $urlType = $serviceType === 'new_cylinder' ? 'new' : 'refill';
        
        return redirect()->route('consumer.order.create', ['type' => $urlType])
                         ->with('reorder_data', $order);
    }

    /**
     * Download order receipt as PDF.
     */
    public function receipt($id)
    {
        $user = Auth::user();
        
        $order = RetailOrder::with(['items.product', 'retailer.user', 'consumer'])
            ->where('consumer_id', $user->id)
            ->findOrFail($id);

        if ($order->payment_status !== 'paid') {
            return redirect()->route('consumer.history')
                ->with('warning', 'Risiti itapatikana baada ya malipo kuthibitishwa kuwa Paid.');
        }
        
        return view('consumer.order.receipt', compact('order'));
    }

    /**
     * Cancel an order.
     */
    public function cancel($id)
    {
        $user = Auth::user();
        
        $order = RetailOrder::where('consumer_id', $user->id)
            ->whereIn('status', ['pending', 'accepted'])
            ->findOrFail($id);
        
        DB::beginTransaction();
        
        try {
            $order->status = 'cancelled';
            $order->save();
            
            DB::commit();
            
            Log::info('Order cancelled', ['order_number' => $order->order_number]);
            
            return redirect()->route('consumer.history')
                             ->with('success', 'Agizo lako limefutwa kwa ufanisi.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order cancellation failed', ['error' => $e->getMessage()]);
            return back()->with('error', 'Imeshindikana kufuta agizo. Tafadhali jaribu tena.');
        }
    }
}
