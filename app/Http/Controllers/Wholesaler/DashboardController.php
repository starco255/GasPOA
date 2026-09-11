<?php

namespace App\Http\Controllers\Wholesaler;

use App\Http\Controllers\Controller;
use App\Models\WholesaleOrder;
use App\Models\BusinessProfile;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display wholesaler dashboard with real data.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get wholesaler's business profile
        $businessProfile = BusinessProfile::where('user_id', $user->id)
            ->where('business_type', 'wholesaler')
            ->first();
        
        if (!$businessProfile) {
            return redirect()->route('wholesaler.settings.profile')
                             ->with('warning', 'Tafadhali kamilisha maelezo ya ghala lako kwanza.');
        }
        
        $wholesalerId = $businessProfile->id;
        
        // 1. MAUZO YA WIKI HII
        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();
        
        $weeklySales = WholesaleOrder::where('wholesaler_id', $wholesalerId)
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->where('payment_status', 'paid')
            ->sum('total_amount');
        
        // Mauzo ya wiki iliyopita kwa ajili ya percentage change
        $lastWeekStart = now()->subWeek()->startOfWeek();
        $lastWeekEnd = now()->subWeek()->endOfWeek();
        
        $lastWeekSales = WholesaleOrder::where('wholesaler_id', $wholesalerId)
            ->whereBetween('created_at', [$lastWeekStart, $lastWeekEnd])
            ->where('payment_status', 'paid')
            ->sum('total_amount');
        
        $salesChange = $lastWeekSales > 0 
            ? round((($weeklySales - $lastWeekSales) / $lastWeekSales) * 100) 
            : 0;
        
        // 2. MAAGIZO MAPYA YANAYOSUBIRI
        $newOrdersCount = WholesaleOrder::where('wholesaler_id', $wholesalerId)
            ->where('status', 'pending')
            ->count();
        
        // 3. WAUZAJI REJAREJA WALIO HAI
        $activeRetailersCount = BusinessProfile::where('business_type', 'retailer')
            ->where('is_open', true)
            ->whereHas('user', function($q) {
                $q->where('is_active', true);
            })
            ->count();
        
        // 4. HISA GHALA (Jumla ya stock)
        $totalStock = Inventory::where('business_profile_id', $wholesalerId)
            ->where('is_active', true)
            ->sum('quantity');
        
        // 5. MAAGIZO MAPYA YANAYOSUBIRI (kwa ajili ya table)
        $incomingOrders = WholesaleOrder::with(['retailer', 'items.product'])
            ->where('wholesaler_id', $wholesalerId)
            ->where('status', 'pending')
            ->latest('created_at')
            ->limit(5)
            ->get()
            ->map(function ($order) {
                // Build items string
                $itemsList = $order->items->map(function($item) {
                    return ($item->product->name ?? 'Bidhaa') . ' x' . $item->quantity;
                })->implode(', ');
                
                return [
                    'id' => $order->id,
                    'number' => $order->order_number,
                    'retailer' => $order->retailer->business_name ?? 'Duka',
                    'items' => $itemsList,
                    'total' => $order->total_amount,
                    'time' => $order->created_at->diffForHumans(),
                ];
            });
        
        // 6. TOP RETAILERS (Wauzaji rejareja wanaoongoza)
        $topRetailers = WholesaleOrder::select(
                'retailer_id',
                DB::raw('COUNT(*) as orders_count'),
                DB::raw('SUM(total_amount) as total_spent')
            )
            ->with('retailer')
            ->where('wholesaler_id', $wholesalerId)
            ->where('payment_status', 'paid')
            ->groupBy('retailer_id')
            ->orderByDesc('total_spent')
            ->limit(4)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->retailer_id,
                    'name' => $item->retailer->business_name ?? 'Duka',
                    'orders' => $item->orders_count,
                    'total' => $item->total_spent,
                ];
            });
        
        // 7. HISA ZINAZOKARIBIA KUISHA (chini ya threshold 20)
        $lowStockThreshold = 20;
        $lowStockItems = Inventory::with('product')
            ->where('business_profile_id', $wholesalerId)
            ->where('is_active', true)
            ->where('quantity', '<', $lowStockThreshold)
            ->orderBy('quantity', 'asc')
            ->limit(5)
            ->get()
            ->map(function ($item) use ($lowStockThreshold) {
                return [
                    'id' => $item->id,
                    'name' => $item->product->name ?? 'Bidhaa',
                    'quantity' => $item->quantity,
                    'min' => $lowStockThreshold,
                    'status' => $item->quantity == 0 ? 'Imekwisha' : 'Chache',
                ];
            });
        
        // 8. MAUZO YA MWEZI HUU
        $monthlySales = WholesaleOrder::where('wholesaler_id', $wholesalerId)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('payment_status', 'paid')
            ->sum('total_amount');
        
        $totalOrdersMonth = WholesaleOrder::where('wholesaler_id', $wholesalerId)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        // 9. BIDHAA ZINAZOUZWA SANA (Top Products)
        $topProducts = Product::select('products.id', 'products.name', DB::raw('SUM(wholesale_order_items.quantity) as total_sold'))
            ->join('wholesale_order_items', 'products.id', '=', 'wholesale_order_items.product_id')
            ->join('wholesale_orders', 'wholesale_order_items.wholesale_order_id', '=', 'wholesale_orders.id')
            ->where('wholesale_orders.wholesaler_id', $wholesalerId)
            ->where('wholesale_orders.status', 'delivered')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_sold')
            ->limit(3)
            ->get();

        return view('wholesaler.dashboard', compact(
            'businessProfile',
            'weeklySales',
            'salesChange',
            'newOrdersCount',
            'activeRetailersCount',
            'totalStock',
            'incomingOrders',
            'topRetailers',
            'lowStockItems',
            'monthlySales',
            'totalOrdersMonth',
            'topProducts'
        ));
    }
}