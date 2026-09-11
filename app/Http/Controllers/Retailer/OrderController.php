<?php

namespace App\Http\Controllers\Retailer;

use App\Http\Controllers\Controller;
use App\Models\RetailOrder;
use App\Models\BusinessProfile;
use App\Models\Inventory;
use App\Models\WholesaleOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{

    /**
     * Display wholesale orders (placed by this retailer).
     */
    public function wholesaleOrders()
    {
        $user = Auth::user();
        $businessProfile = BusinessProfile::where('user_id', $user->id)
            ->where('business_type', 'retailer')
            ->first();
        if (!$businessProfile) {
            return redirect()->route('retailer.settings.shop')
                ->with('warning', 'Weka maelezo ya duka kwanza.');
        }

        $orders = WholesaleOrder::with(['wholesaler', 'items.product'])
            ->where('retailer_id', $businessProfile->id)
            ->latest('created_at')
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'number' => $order->order_number,
                    'wholesaler' => $order->wholesaler->business_name ?? 'Haijulikani',
                    'total' => $order->total_amount,
                    'status' => $order->status,
                    'created_at' => $order->created_at->format('d M Y, H:i'),
                    'items' => $order->items->map(fn($item) => $item->product->name . ' x' . $item->quantity)->join(', '),
                ];
            });

        return view('retailer.orders.wholesale_orders', compact('orders'));
    }

    /**
     * Track a specific wholesale order.
     */
    public function wholesaleTracking($id)
    {
        $user = Auth::user();
        $businessProfile = BusinessProfile::where('user_id', $user->id)
            ->where('business_type', 'retailer')
            ->first();
        if (!$businessProfile) abort(403);

        // ✅ Hakikisha 'retailer' ipo kwenye with()
        $order = WholesaleOrder::with(['wholesaler', 'items.product', 'retailer'])
            ->where('retailer_id', $businessProfile->id)
            ->findOrFail($id);

        // Get driver details saved during dispatch
        $driverDetails = $order->driver_details ?? 'Haijajazwa';

        // Wholesaler location
        $wholesalerLat = $order->wholesaler->shop_latitude ?? -6.8192;
        $wholesalerLng = $order->wholesaler->shop_longitude ?? 39.2695;

        // ✅ Retailer (duka lako) location – HII SASA INAFIKA KWENYE VIEW
        $retailerLat = $businessProfile->shop_latitude ?? -6.792354;
        $retailerLng = $businessProfile->shop_longitude ?? 39.208328;
        $retailerAddress = $businessProfile->physical_address ?? 'Haijabainishwa';

        return view('retailer.orders.wholesale_tracking', compact(
            'order', 'driverDetails',
            'wholesalerLat', 'wholesalerLng',
            'retailerLat', 'retailerLng', 'retailerAddress'
        ));
    }


    /**
     * Display incoming orders (pending acceptance) AND active orders.
     */
    public function incoming()
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

        $retailerId = $businessProfile->id;

        // PENDING ORDERS (Maagizo Mapya)
        $orders = $this->getPendingOrders($retailerId, $businessProfile);
        $totalPending = $orders->count();
        $pendingTotalItems = $orders->sum('quantity');
        $pendingTotalValue = $orders->sum('total');

        // ACTIVE ORDERS (Yanayoendelea)
        $activeOrders = $this->getActiveOrders($retailerId, $businessProfile);

        // Calculate total items for active orders
        $activeTotalItems = 0;
        foreach ($activeOrders as $order) {
            foreach ($order['items'] as $item) {
                $activeTotalItems += $item['quantity'] ?? 1;
            }
        }

        return view('retailer.orders.incoming', compact(
            'orders',
            'totalPending',
            'pendingTotalItems',
            'pendingTotalValue',
            'activeOrders',
            'activeTotalItems',
            'businessProfile'
        ));
    }

    /**
     * Get pending orders for retailer.
     */
    private function getPendingOrders($retailerId, $businessProfile)
    {
        return RetailOrder::with(['items.product', 'consumer'])
            ->where('retailer_id', $retailerId)
            ->where('status', 'pending')
            ->latest('created_at')
            ->get()
            ->map(function ($order) use ($businessProfile, $retailerId) {
                // Calculate actual distance
                $distance = $this->calculateDistance(
                    $order->delivery_latitude,
                    $order->delivery_longitude,
                    $businessProfile->shop_latitude,
                    $businessProfile->shop_longitude
                );

                // DELIVERY FEE: TZS 0 for normal, TZS 3,000 for urgent
                $deliveryFee = 0;
                $urgencyFee = ($order->urgency_level === 'urgent') ? 3000 : 0;
                $isUrgent = $order->urgency_level === 'urgent';

                // Muda wa kufika: Dakika 20 kwa kawaida, Dakika 15 kwa haraka
                $estimatedDeliveryTime = $isUrgent ? 'Dakika 15 (Haraka)' : 'Dakika 20';

                // Get items with proper calculations
                $items = $order->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'product_id' => $item->product_id,
                        'name' => $item->product->name ?? 'Bidhaa',
                        'quantity' => $item->quantity,
                        'price' => $item->price_per_item,
                        'service_type' => $item->service_type_requested,
                        'subtotal' => $item->price_per_item * $item->quantity,
                    ];
                });

                // Calculate product subtotal
                $productSubtotal = $items->sum('subtotal');

                // Calculate total
                $calculatedTotal = $productSubtotal + $urgencyFee;

                // If database total is different, update it
                if (abs($order->total_amount - $calculatedTotal) > 1) {
                    $order->total_amount = $calculatedTotal;
                    $order->save();
                }

                // Check stock availability
                $firstItem = $items->first();
                $productId = $firstItem['product_id'] ?? null;
                $stockAvailable = $this->checkStockAvailability($retailerId, $productId, $items->sum('quantity'));

                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'consumer_id' => $order->consumer_id,
                    'customer' => $order->consumer->full_name ?? 'Mteja',
                    'customer_phone' => $order->consumer->phone_number ?? 'Haipo',
                    'customer_email' => $order->consumer->email,
                    'items' => $items,
                    'service' => $firstItem['name'] ?? 'Huduma',
                    'service_type' => $firstItem['service_type'] ?? 'refill_exchange',
                    'quantity' => $items->sum('quantity'),
                    'product_subtotal' => $productSubtotal,
                    'delivery_fee' => $deliveryFee,
                    'urgency_fee' => $urgencyFee,
                    'is_urgent' => $isUrgent,
                    'urgency_level' => $order->urgency_level,
                    'total' => $order->total_amount,
                    'address' => $order->delivery_address,
                    'latitude' => $order->delivery_latitude,
                    'longitude' => $order->delivery_longitude,
                    'distance' => round($distance, 2),
                    'payment_method' => $order->payment_method,
                    'payment_method_provider' => $order->payment_method_provider,
                    'payment_status' => $order->payment_status,
                    'transaction_reference' => $order->transaction_reference,
                    'created_at' => $order->created_at,
                    'created_at_human' => $order->created_at->diffForHumans(),
                    'created_at_formatted' => $order->created_at->format('d M Y, H:i'),
                    'stock_available' => $stockAvailable,
                    'estimated_delivery_time' => $estimatedDeliveryTime,
                ];
            });
    }

    /**
     * Get active orders for retailer.
     */
    private function getActiveOrders($retailerId, $businessProfile)
    {
        return RetailOrder::with(['items.product', 'consumer'])
            ->where('retailer_id', $retailerId)
            ->whereIn('status', ['accepted', 'picked_up', 'out_for_delivery'])
            ->latest('created_at')
            ->get()
            ->map(function ($order) use ($businessProfile) {
                $distance = $this->calculateDistance(
                    $order->delivery_latitude,
                    $order->delivery_longitude,
                    $businessProfile->shop_latitude,
                    $businessProfile->shop_longitude
                );

                $estimatedDelivery = $this->calculateEstimatedDelivery($order, $distance);

                $items = $order->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->product->name ?? 'Bidhaa',
                        'quantity' => $item->quantity,
                        'price' => $item->price_per_item,
                        'service_type' => $item->service_type_requested,
                    ];
                });

                $totalQuantity = $items->sum('quantity');
                $serviceName = $items->first()['name'] ?? 'Bidhaa';

                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer' => $order->consumer->full_name ?? 'Mteja',
                    'customer_phone' => $order->consumer->phone_number ?? 'Haipo',
                    'items' => $items,
                    'service' => $serviceName . ($totalQuantity > 1 ? ' x' . $totalQuantity : ''),
                    'total' => $order->total_amount,
                    'address' => $order->delivery_address,
                    'status' => $order->status,
                    'distance' => round($distance, 2),
                    'estimated_delivery' => $estimatedDelivery,
                    'is_urgent' => $order->urgency_level === 'urgent',
                    'created_at' => $order->created_at->diffForHumans(),
                    'accepted_at' => $order->assigned_at ? $order->assigned_at->diffForHumans() : null,
                ];
            });
    }

    /**
     * Accept an order.
     */
    public function accept($id)
    {
        $user = Auth::user();

        $businessProfile = BusinessProfile::where('user_id', $user->id)
            ->where('business_type', 'retailer')
            ->first();

        if (!$businessProfile) {
            return redirect()->route('retailer.settings.shop')
                ->with('error', 'Haujaweka maelezo ya duka.');
        }

        $order = RetailOrder::with(['items.product'])
            ->where('retailer_id', $businessProfile->id)
            ->where('id', $id)
            ->where('status', 'pending')
            ->first();

        if (!$order) {
            return redirect()->route('retailer.orders.incoming')
                ->with('error', 'Agizo halikupatikana au tayari limeshughulikiwa.');
        }

        if (($order->payment_method_provider ?? $order->payment_method) !== 'cash' &&
            (!$order->transaction_reference || $order->payment_status !== 'paid')) {
            return redirect()->route('retailer.orders.incoming')
                ->with('error', 'Huwezi kukubali agizo kabla mteja hajatumia reference ID na malipo kuthibitishwa.');
        }

        // Check if stock is available
        $hasStock = $this->checkOrderStock($order, $businessProfile->id);

        if (!$hasStock) {
            return redirect()->route('retailer.orders.incoming')
                ->with('error', 'Huna stock ya kutosha kwa agizo hili. Tafadhali ongeza hisa kwanza.');
        }

        DB::beginTransaction();

        try {
            $order->status = 'accepted';
            $order->assigned_at = now();
            $order->save();

            Log::info('Order accepted', [
                'order_number' => $order->order_number,
                'retailer_id' => $businessProfile->id,
                'retailer_name' => $businessProfile->business_name,
            ]);

            DB::commit();

            return redirect()->route('retailer.orders.incoming', '#active')
                ->with('success', 'Agizo #' . $order->order_number . ' limekubaliwa!');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Order acceptance failed', [
                'order_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('retailer.orders.incoming')
                ->with('error', 'Imeshindikana kukubali agizo. Tafadhali jaribu tena.');
        }
    }

    /**
     * Reject an order.
     */
    public function reject(Request $request, $id)
    {
        $user = Auth::user();

        $businessProfile = BusinessProfile::where('user_id', $user->id)
            ->where('business_type', 'retailer')
            ->first();

        if (!$businessProfile) {
            return redirect()->route('retailer.settings.shop')
                ->with('error', 'Haujaweka maelezo ya duka.');
        }

        $order = RetailOrder::where('retailer_id', $businessProfile->id)
            ->where('id', $id)
            ->where('status', 'pending')
            ->first();

        if (!$order) {
            return redirect()->route('retailer.orders.incoming')
                ->with('error', 'Agizo halikupatikana au tayari limeshughulikiwa.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            $order->status = 'cancelled';
            $order->save();

            Log::info('Order rejected', [
                'order_number' => $order->order_number,
                'retailer_id' => $businessProfile->id,
                'reason' => $validated['rejection_reason'] ?? 'Not specified',
            ]);

            DB::commit();

            return redirect()->route('retailer.orders.incoming', '#pending')
                ->with('success', 'Agizo #' . $order->order_number . ' limekataliwa.');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Order rejection failed', [
                'order_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('retailer.orders.incoming')
                ->with('error', 'Imeshindikana kukataa agizo. Tafadhali jaribu tena.');
        }
    }

    /**
     * Display active orders (separate page).
     */
    public function active()
    {
        $user = Auth::user();

        $businessProfile = BusinessProfile::where('user_id', $user->id)
            ->where('business_type', 'retailer')
            ->first();

        if (!$businessProfile) {
            return redirect()->route('retailer.settings.shop')
                ->with('warning', 'Tafadhali kamilisha maelezo ya duka lako kwanza.');
        }

        $activeOrders = $this->getActiveOrders($businessProfile->id, $businessProfile);

        return view('retailer.orders.active', compact('activeOrders', 'businessProfile'));
    }

    /**
     * Mark order as picked up.
     */
    public function pickup($id)
    {
        $user = Auth::user();

        $businessProfile = BusinessProfile::where('user_id', $user->id)
            ->where('business_type', 'retailer')
            ->first();

        if (!$businessProfile) {
            return redirect()->route('retailer.orders.incoming')
                ->with('error', 'Duka halijapatikana.');
        }

        $order = RetailOrder::where('retailer_id', $businessProfile->id)
            ->where('id', $id)
            ->where('status', 'accepted')
            ->first();

        if (!$order) {
            return redirect()->route('retailer.orders.incoming')
                ->with('error', 'Agizo halikupatikana.');
        }

        $order->status = 'picked_up';
        $order->save();

        Log::info('Order picked up', [
            'order_number' => $order->order_number,
            'retailer_id' => $businessProfile->id,
        ]);

        return redirect()->route('retailer.orders.incoming', '#active')
            ->with('success', 'Umesha chukua stock! Mteja ataarifiwa.');
    }

    /**
     * Mark order as delivered.
     */
    public function deliver(Request $request, $id)
    {
        $user = Auth::user();

        $businessProfile = BusinessProfile::where('user_id', $user->id)
            ->where('business_type', 'retailer')
            ->first();

        if (!$businessProfile) {
            return redirect()->route('retailer.orders.incoming')
                ->with('error', 'Duka halijapatikana.');
        }

        $order = RetailOrder::with(['items'])
            ->where('retailer_id', $businessProfile->id)
            ->where('id', $id)
            ->whereIn('status', ['picked_up', 'out_for_delivery'])
            ->first();

        if (!$order) {
            return redirect()->route('retailer.orders.incoming')
                ->with('error', 'Agizo halikupatikana.');
        }

        DB::beginTransaction();

        try {
            $order->status = 'delivered';
            $order->delivered_at = now();
            if (($order->payment_method_provider ?? $order->payment_method) === 'cash' && $request->boolean('cash_received')) {
                $order->payment_status = 'paid';
            }
            $order->save();

            // Decrease inventory
            $this->decreaseInventory($order, $businessProfile->id);

            Log::info('Order delivered', [
                'order_number' => $order->order_number,
                'retailer_id' => $businessProfile->id,
                'total' => $order->total_amount,
            ]);

            DB::commit();

            return redirect()->route('retailer.orders.history')
                ->with('success', 'Agizo limekamilika! Asante kwa huduma.');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Order delivery failed', [
                'order_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('retailer.orders.incoming')
                ->with('error', 'Imeshindikana kukamilisha agizo.');
        }
    }

    /**
     * Display order history.
     */
    public function history(Request $request)
    {
        $user = Auth::user();

        $businessProfile = BusinessProfile::where('user_id', $user->id)
            ->where('business_type', 'retailer')
            ->first();

        if (!$businessProfile) {
            return redirect()->route('retailer.settings.shop');
        }

        $query = RetailOrder::with(['items.product', 'consumer'])
            ->where('retailer_id', $businessProfile->id)
            ->whereIn('status', ['delivered', 'cancelled'])
            ->orderBy('created_at', 'desc');

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->paginate(15)->withQueryString();

        // Statistics
        $totalSales = RetailOrder::where('retailer_id', $businessProfile->id)
            ->where('status', 'delivered')
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        $totalOrders = RetailOrder::where('retailer_id', $businessProfile->id)
            ->where('status', 'delivered')
            ->count();

        $cancelledOrders = RetailOrder::where('retailer_id', $businessProfile->id)
            ->where('status', 'cancelled')
            ->count();

        return view('retailer.orders.history', compact(
            'orders',
            'totalSales',
            'totalOrders',
            'cancelledOrders',
            'businessProfile'
        ));
    }

    /**
     * Calculate distance between two points using Haversine formula.
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        if (!$lat1 || !$lon1 || !$lat2 || !$lon2) {
            return 0;
        }

        $earthRadius = 6371; // km

        $latFrom = deg2rad((float) $lat1);
        $lonFrom = deg2rad((float) $lon1);
        $latTo = deg2rad((float) $lat2);
        $lonTo = deg2rad((float) $lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return $angle * $earthRadius;
    }

    /**
     * Calculate estimated delivery time.
     */
    private function calculateEstimatedDelivery($order, $distance)
    {
        // Kwa kawaida: Dakika 20
        // Kwa haraka: Dakika 15
        $baseTime = 20; // Dakika

        if ($order->urgency_level === 'urgent') {
            $baseTime = 15;
        }

        // Ongeza dakika 2 kwa kila km zaidi ya 3km
        if ($distance > 3) {
            $extraKm = ceil($distance - 3);
            $baseTime += ($extraKm * 2);
        }

        return now()->addMinutes($baseTime);
    }

    /**
     * Check stock availability by product ID.
     */
    private function checkStockAvailability($retailerId, $productId, $quantity)
    {
        if (!$productId) {
            return false;
        }

        $inventory = Inventory::where('business_profile_id', $retailerId)
            ->where('product_id', $productId)
            ->where('is_active', true)
            ->first();

        return $inventory && $inventory->quantity >= $quantity;
    }

    /**
     * Check if order can be fulfilled with current stock.
     */
    private function checkOrderStock($order, $retailerId)
    {
        foreach ($order->items as $item) {
            $inventory = Inventory::where('business_profile_id', $retailerId)
                ->where('product_id', $item->product_id)
                ->where('is_active', true)
                ->first();

            if (!$inventory || $inventory->quantity < $item->quantity) {
                return false;
            }
        }
        return true;
    }

    /**
     * Decrease inventory after delivery.
     */
    private function decreaseInventory($order, $retailerId)
    {
        foreach ($order->items as $item) {
            $inventory = Inventory::where('business_profile_id', $retailerId)
                ->where('product_id', $item->product_id)
                ->first();

            if ($inventory) {
                $inventory->quantity = max(0, $inventory->quantity - $item->quantity);
                $inventory->last_updated = now();
                $inventory->save();
            }
        }
    }
}
