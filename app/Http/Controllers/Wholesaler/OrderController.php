<?php

namespace App\Http\Controllers\Wholesaler;

use App\Http\Controllers\Controller;
use App\Models\WholesaleOrder;
use App\Models\BusinessProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * Display incoming orders with tabs (pending and processing).
     */
    public function incoming()
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
        
        // PENDING ORDERS (Maagizo Mapya)
        $pendingOrders = WholesaleOrder::with(['retailer', 'items.product'])
            ->where('wholesaler_id', $wholesalerId)
            ->where('status', 'pending')
            ->latest('created_at')
            ->get()
            ->map(function ($order) {
                $totalItems = $order->items->sum('quantity');
                
                return [
                    'id' => $order->id,
                    'number' => $order->order_number,
                    'retailer' => $order->retailer->business_name ?? 'Duka',
                    'retailer_id' => $order->retailer_id,
                    'retailer_phone' => $order->retailer->user->phone_number ?? 'Haipo',
                    'address' => $order->delivery_address,
                    'total' => $order->total_amount,
                    'total_items' => $totalItems,
                    'payment_status' => $order->payment_status,
                    'payment_method' => $order->payment_method,
                    'transaction_reference' => $order->transaction_reference,
                    'created' => $order->created_at->diffForHumans(),
                    'items' => $order->items->map(function ($item) {
                        return [
                            'name' => $item->product->name ?? 'Bidhaa',
                            'qty' => $item->quantity,
                            'price' => $item->price_per_item,
                        ];
                    })->toArray(),
                ];
            });
        
        $pendingCount = $pendingOrders->count();
        
        // PROCESSING ORDERS (confirmed, processing, dispatched)
        $processingOrders = WholesaleOrder::with(['retailer', 'items.product'])
            ->where('wholesaler_id', $wholesalerId)
            ->whereIn('status', ['confirmed', 'processing', 'dispatched'])
            ->latest('created_at')
            ->get()
            ->map(function ($order) {
                $totalItems = $order->items->sum('quantity');
                $itemsList = $order->items->map(function($item) {
                    return ($item->product->name ?? 'Bidhaa') . ' x' . $item->quantity;
                })->implode(', ');
                
                return [
                    'id' => $order->id,
                    'number' => $order->order_number,
                    'retailer' => $order->retailer->business_name ?? 'Duka',
                    'retailer_phone' => $order->retailer->user->phone_number ?? 'Haipo',
                    'address' => $order->delivery_address,
                    'items' => $itemsList,
                    'total' => $order->total_amount,
                    'total_items' => $totalItems,
                    'status' => $order->status,
                    'payment_status' => $order->payment_status,
                    'payment_method' => $order->payment_method,
                    'transaction_reference' => $order->transaction_reference,
                    'created_at' => $order->created_at->format('d M Y, H:i'),
                    'created_diff' => $order->created_at->diffForHumans(),
                ];
            });
        
        $processingCount = $processingOrders->count();
        
        // RECENT DELIVERED ORDERS (5 za mwisho)
        $recentDeliveredOrders = WholesaleOrder::with(['retailer', 'items.product'])
            ->where('wholesaler_id', $wholesalerId)
            ->where('status', 'delivered')
            ->latest('delivered_at')
            ->take(5)
            ->get()
            ->map(function ($order) {
                $itemsList = $order->items->map(function($item) {
                    return ($item->product->name ?? 'Bidhaa') . ' x' . $item->quantity;
                })->implode(', ');
                
                return [
                    'id' => $order->id,
                    'number' => $order->order_number,
                    'retailer' => $order->retailer->business_name ?? 'Haijulikani',
                    'total' => $order->total_amount,
                    'delivered_at' => $order->delivered_at ? $order->delivered_at->format('d M Y, H:i') : 'N/A',
                    'items' => $itemsList,
                ];
            });
        
        return view('wholesaler.orders.incoming', compact(
            'pendingOrders',
            'pendingCount',
            'processingOrders',
            'processingCount',
            'recentDeliveredOrders',
            'businessProfile'
        ));
    }

    /**
     * Accept an order (moves to confirmed).
     */
    public function accept($id)
    {
        $user = Auth::user();
        
        $businessProfile = BusinessProfile::where('user_id', $user->id)
            ->where('business_type', 'wholesaler')
            ->first();
        
        if (!$businessProfile) {
            return redirect()->route('wholesaler.settings.profile')
                             ->with('error', 'Haujaweka maelezo ya ghala.');
        }
        
        $order = WholesaleOrder::with(['items.product'])
            ->where('wholesaler_id', $businessProfile->id)
            ->where('id', $id)
            ->where('status', 'pending')
            ->first();
        
        if (!$order) {
            return redirect()->route('wholesaler.orders.incoming')
                             ->with('error', 'Agizo halikupatikana au tayari limeshughulikiwa.');
        }

        if ($order->payment_method !== 'cash' &&
            (!$order->transaction_reference || $order->payment_status !== 'paid')) {
            return redirect()->route('wholesaler.orders.incoming')
                ->with('error', 'Huwezi kukubali agizo kabla retailer hajatumia reference ID na malipo kuthibitishwa.');
        }
        
        DB::beginTransaction();
        
        try {
            $order->status = 'confirmed';
            $order->save();
            
            Log::info('Wholesale order accepted', [
                'order_number' => $order->order_number,
                'wholesaler_id' => $businessProfile->id,
            ]);
            
            DB::commit();
            
            return redirect()->route('wholesaler.orders.incoming', '#active')
                             ->with('success', 'Agizo #' . $order->order_number . ' limekubaliwa!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Wholesale order acceptance failed', [
                'order_id' => $id,
                'error' => $e->getMessage(),
            ]);
            
            return redirect()->route('wholesaler.orders.incoming')
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
            ->where('business_type', 'wholesaler')
            ->first();
        
        if (!$businessProfile) {
            return redirect()->route('wholesaler.settings.profile')
                             ->with('error', 'Haujaweka maelezo ya ghala.');
        }
        
        $order = WholesaleOrder::where('wholesaler_id', $businessProfile->id)
            ->where('id', $id)
            ->where('status', 'pending')
            ->first();
        
        if (!$order) {
            return redirect()->route('wholesaler.orders.incoming')
                             ->with('error', 'Agizo halikupatikana au tayari limeshughulikiwa.');
        }
        
        $validated = $request->validate([
            'rejection_reason' => 'nullable|string|max:255',
        ]);
        
        DB::beginTransaction();
        
        try {
            $order->status = 'cancelled';
            $order->save();
            
            Log::info('Wholesale order rejected', [
                'order_number' => $order->order_number,
                'wholesaler_id' => $businessProfile->id,
                'reason' => $validated['rejection_reason'] ?? 'Not specified',
            ]);
            
            DB::commit();
            
            return redirect()->route('wholesaler.orders.incoming', '#pending')
                             ->with('success', 'Agizo #' . $order->order_number . ' limekataliwa.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('wholesaler.orders.incoming')
                             ->with('error', 'Imeshindikana kukataa agizo. Tafadhali jaribu tena.');
        }
    }

    public function dispatch(Request $request, $id)
{
    $user = Auth::user();
    
    $businessProfile = BusinessProfile::where('user_id', $user->id)
        ->where('business_type', 'wholesaler')
        ->first();
    
    if (!$businessProfile) {
        return redirect()->route('wholesaler.settings.profile')
                         ->with('error', 'Haujaweka maelezo ya ghala.');
    }
    
    // Inaruhusu confirmed na processing statuses
    $order = WholesaleOrder::where('wholesaler_id', $businessProfile->id)
        ->where('id', $id)
        ->whereIn('status', ['confirmed', 'processing'])
        ->first();
    
    if (!$order) {
        return redirect()->route('wholesaler.orders.incoming')
                         ->with('error', 'Agizo halikupatikana.');
    }
    
    $validated = $request->validate([
        'driver_name' => 'required|string|max:100',
        'vehicle_number' => 'required|string|max:50',
    ]);
    
    // Unganisha kwa dash
    $driverDetails = $validated['driver_name'] . ' - ' . $validated['vehicle_number'];
    
    $order->status = 'dispatched';
    $order->driver_details = $driverDetails;
    $order->save();
    
    Log::info('Wholesale order dispatched', [
        'order_number' => $order->order_number,
        'wholesaler_id' => $businessProfile->id,
        'driver_details' => $driverDetails,
    ]);
    
    return redirect()->route('wholesaler.orders.incoming', '#active')
                     ->with('success', 'Agizo #' . $order->order_number . ' limesafirishwa!');
}
    /**
     * Mark order as delivered (kamilisha).
     */
    public function deliver($id)
    {
        $user = Auth::user();
        
        $businessProfile = BusinessProfile::where('user_id', $user->id)
            ->where('business_type', 'wholesaler')
            ->first();
        
        if (!$businessProfile) {
            return redirect()->route('wholesaler.settings.profile')
                             ->with('error', 'Haujaweka maelezo ya ghala.');
        }
        
        $order = WholesaleOrder::where('wholesaler_id', $businessProfile->id)
            ->where('id', $id)
            ->where('status', 'dispatched')
            ->first();
        
        if (!$order) {
            return redirect()->route('wholesaler.orders.incoming')
                             ->with('error', 'Agizo halikupatikana.');
        }
        
        $order->status = 'delivered';
        $order->delivered_at = now();
        // Logistics completion must not confirm electronic payment. Cash confirmation is handled explicitly.
        $order->save();
        
        Log::info('Wholesale order delivered', [
            'order_number' => $order->order_number,
            'wholesaler_id' => $businessProfile->id,
        ]);
        
        return redirect()->route('wholesaler.orders.incoming', '#active')
                         ->with('success', 'Agizo #' . $order->order_number . ' limekamilika!');
    }

    /**
     * Display order history.
     */
    public function history(Request $request)
    {
        $user = Auth::user();
        
        $businessProfile = BusinessProfile::where('user_id', $user->id)
            ->where('business_type', 'wholesaler')
            ->first();
        
        if (!$businessProfile) {
            return redirect()->route('wholesaler.settings.shop');
        }
        
        $query = WholesaleOrder::with(['retailer.user', 'items.product'])
            ->where('wholesaler_id', $businessProfile->id)
            ->orderBy('created_at', 'desc');
        
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
        
        $totalSales = WholesaleOrder::where('wholesaler_id', $businessProfile->id)
            ->where('status', 'delivered')
            ->where('payment_status', 'paid')
            ->sum('total_amount');
        
        $totalOrders = WholesaleOrder::where('wholesaler_id', $businessProfile->id)
            ->where('status', 'delivered')
            ->count();
        
        $cancelledOrders = WholesaleOrder::where('wholesaler_id', $businessProfile->id)
            ->where('status', 'cancelled')
            ->count();
        
        return view('wholesaler.orders.history', compact(
            'orders', 
            'totalSales', 
            'totalOrders', 
            'cancelledOrders',
            'businessProfile'
        ));
    }
}
