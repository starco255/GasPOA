<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\RetailOrder;
use App\Models\BusinessProfile;
use App\Models\Payment;
use App\Models\Payout;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    /**
     * Display admin dashboard with real data.
     */
    public function index()
    {
        // =====================================================
        // 1. STATISTICS CARDS
        // =====================================================
        
        // Jumla ya Watumiaji
        $totalUsers = User::count();
        $usersThisMonth = User::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $usersLastMonth = User::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();
        $userGrowth = $usersLastMonth > 0 
            ? round((($usersThisMonth - $usersLastMonth) / $usersLastMonth) * 100) 
            : 0;
        
        // Haya ni mauzo yaliyothibitishwa, si mapato ya tume ya GasPOA.
        // Tume ya jukwaa husomwa pekee kutoka payouts ili isikadiriwe.
        $totalPaidSales = RetailOrder::where('payment_status', 'paid')
            ->sum('total_amount');
        $revenueThisWeek = RetailOrder::where('payment_status', 'paid')
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->sum('total_amount');
        $revenueLastWeek = RetailOrder::where('payment_status', 'paid')
            ->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])
            ->sum('total_amount');
        $revenueGrowth = $revenueLastWeek > 0 
            ? round((($revenueThisWeek - $revenueLastWeek) / $revenueLastWeek) * 100) 
            : 0;

        $totalPlatformRevenue = Schema::hasTable('payouts') ? Payout::sum('commission_amount') : 0;
        $pendingPayouts = Schema::hasTable('payouts') ? Payout::where('status', 'pending')->count() : 0;

        $totalPayments = Schema::hasTable('payments') ? Payment::count() : 0;
        $successfulPayments = Schema::hasTable('payments') ? Payment::where('status', 'success')->count() : 0;
        $paymentSuccessRate = $totalPayments > 0
            ? round(($successfulPayments / $totalPayments) * 100)
            : 0;
        
        // Zinazosubiri Uidhinishaji
        $pendingVerifications = BusinessProfile::where('is_open', false)
            ->orWhereNull('is_open')
            ->count();
        
        // Maagizo Leo
        $ordersToday = RetailOrder::whereDate('created_at', today())->count();
        $ordersActive = RetailOrder::whereIn('status', ['pending', 'accepted', 'picked_up', 'out_for_delivery'])->count();
        
        // =====================================================
        // 2. BIASHARA ZILIZOSAJILIWA (MPYA)
        // =====================================================
        $totalBusinesses = BusinessProfile::whereIn('business_type', ['retailer', 'wholesaler'])->count();
        $totalRetailers = BusinessProfile::where('business_type', 'retailer')->count();
        $totalWholesalers = BusinessProfile::where('business_type', 'wholesaler')->count();
        
        // Bidhaa Zilizopo
        $totalProducts = Product::where('is_active', true)->count();
        
        // =====================================================
        // 3. WATUMIAJI WAPYA
        // =====================================================
        $newUsers = User::latest('created_at')
            ->limit(5)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->full_name,
                    'type' => $user->user_type,
                    'type_label' => $this->getUserTypeLabel($user->user_type),
                    'time' => $user->created_at->diffForHumans(),
                    'avatar' => strtoupper(substr($user->full_name, 0, 1)),
                ];
            });
        
        // =====================================================
        // 4. MAAGIZO YA HIVI KARIBUNI
        // =====================================================
        $recentOrders = RetailOrder::with(['consumer', 'retailer', 'items.product'])
            ->latest('created_at')
            ->limit(10)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->order_number,
                    'order_id' => $order->id,
                    'consumer' => $order->consumer->full_name ?? 'Mteja',
                    'retailer' => $order->retailer->business_name ?? 'Haijajulikana',
                    'service' => $order->items->first()->product->name ?? 'Bidhaa',
                    'total' => $order->total_amount,
                    'status' => $order->status,
                    'payment_status' => $order->payment_status,
                    'date' => $order->created_at->format('d/m/Y'),
                    'date_full' => $order->created_at->format('d M Y, H:i'),
                    'time_ago' => $order->created_at->diffForHumans(),
                ];
            });
        
        // =====================================================
        // 5. USER STATISTICS BY TYPE
        // =====================================================
        $consumersCount = User::where('user_type', 'consumer')->count();
        $retailersCount = User::where('user_type', 'retailer')->count();
        $wholesalersCount = User::where('user_type', 'wholesaler')->count();
        $adminsCount = User::where('user_type', 'admin')->count();
        
        // =====================================================
        // 6. ORDER STATISTICS
        // =====================================================
        $totalOrders = RetailOrder::count();
        $deliveredOrders = RetailOrder::where('status', 'delivered')->count();
        $cancelledOrders = RetailOrder::where('status', 'cancelled')->count();
        $pendingOrders = RetailOrder::where('status', 'pending')->count();
        $activeOrders = RetailOrder::whereIn('status', ['accepted', 'picked_up', 'out_for_delivery'])->count();
        
        // Delivery success rate
        $deliveryRate = $totalOrders > 0 
            ? round(($deliveredOrders / $totalOrders) * 100) 
            : 0;
        
        return view('admin.dashboard', compact(
            'totalUsers',
            'usersThisMonth',
            'userGrowth',
            'totalPaidSales',
            'revenueThisWeek',
            'revenueGrowth',
            'totalPlatformRevenue',
            'pendingPayouts',
            'totalPayments',
            'successfulPayments',
            'paymentSuccessRate',
            'pendingVerifications',
            'ordersToday',
            'ordersActive',
            'totalBusinesses',
            'totalRetailers',
            'totalWholesalers',
            'totalProducts',
            'newUsers',
            'recentOrders',
            'consumersCount',
            'retailersCount',
            'wholesalersCount',
            'adminsCount',
            'totalOrders',
            'deliveredOrders',
            'cancelledOrders',
            'pendingOrders',
            'activeOrders',
            'deliveryRate'
        ));
    }
    
    /**
     * Get user type label in Swahili.
     */
    private function getUserTypeLabel($type)
    {
        return match ($type) {
            'consumer' => 'Mtumiaji',
            'retailer' => 'Muuza Rejareja',
            'wholesaler' => 'Muuza Jumla',
            'admin' => 'Msimamizi',
            default => ucfirst($type),
        };
    }
}
