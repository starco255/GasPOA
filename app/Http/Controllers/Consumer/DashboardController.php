<?php

namespace App\Http\Controllers\Consumer;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\RetailOrder;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display consumer dashboard with real data from database.
     */
    public function index()
    {
        $user = Auth::user();
        
        // 1. Jumla ya Maagizo Yote ya Mtumiaji
        $totalOrders = RetailOrder::where('consumer_id', $user->id)->count();
        
        // 2. Maagizo ya Mwaka Huu
        $ordersThisYear = RetailOrder::where('consumer_id', $user->id)
            ->whereYear('created_at', date('Y'))
            ->count();
        
        // 3. Agizo la Mwisho (Recent Order)
        $lastOrder = RetailOrder::where('consumer_id', $user->id)
            ->latest('created_at')
            ->first();
        
        $lastOrderNumber = $lastOrder ? $lastOrder->order_number : 'Hakuna';
        
        // 4. Jumla ya Matumizi (Paid Orders)
        $totalSpent = RetailOrder::where('consumer_id', $user->id)
            ->where('payment_status', 'paid')
            ->sum('total_amount');
        
        // 5. Agizo Linaloendelea (Active Order) - Status si delivered wala cancelled
        $activeOrder = RetailOrder::with(['items.product', 'retailer.user'])
            ->where('consumer_id', $user->id)
            ->whereNotIn('status', ['delivered', 'cancelled'])
            ->latest('created_at')
            ->first();

        $activePayment = $activeOrder
            ? Payment::where('order_type', 'retail')->where('order_id', $activeOrder->id)->latest('id')->first()
            : null;
        $activePaymentStatus = $activePayment?->status;
        $activePaymentProvider = $activePayment?->provider;
        
        // 6. Maagizo ya Hivi Karibuni (5 ya mwisho)
        $recentOrders = RetailOrder::with(['items.product'])
            ->where('consumer_id', $user->id)
            ->latest('created_at')
            ->limit(5)
            ->get()
            ->map(function ($order) {
                // Kusanya majina ya bidhaa kwa ajili ya kuonyesha
                $serviceName = $order->items->map(function ($item) {
                    return $item->product->name ?? 'Bidhaa';
                })->join(', ');
                
                return [
                    'id' => $order->id,
                    'number' => $order->order_number,
                    'date' => $order->created_at ? $order->created_at->format('d M Y') : 'Hivi karibuni',
                    'service' => $serviceName ?: 'Huduma',
                    'total' => $order->total_amount ?? 0,
                    'status' => $order->status ?? 'pending',
                ];
            });

        return view('consumer.dashboard', compact(
            'totalOrders',
            'ordersThisYear',
            'lastOrderNumber',
            'totalSpent',
            'activeOrder',
            'activePaymentStatus',
            'activePaymentProvider',
            'recentOrders'
        ));
    }
}
