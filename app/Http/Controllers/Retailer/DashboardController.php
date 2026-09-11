<?php

namespace App\Http\Controllers\Retailer;

use App\Http\Controllers\Controller;
use App\Models\RetailOrder;
use App\Models\BusinessProfile;
use App\Models\WholesaleOrder;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display retailer dashboard with real data.
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
        
        $retailerId = $businessProfile->id;
        
        // 1. MAUZO LEO
        $todaySales = RetailOrder::where('retailer_id', $retailerId)
            ->whereDate('created_at', today())
            ->where('payment_status', 'paid')
            ->sum('total_amount');
        
        // Mauzo jana kwa ajili ya percentage change
        $yesterdaySales = RetailOrder::where('retailer_id', $retailerId)
            ->whereDate('created_at', today()->subDay())
            ->where('payment_status', 'paid')
            ->sum('total_amount');
        
        $salesChange = $yesterdaySales > 0 
            ? round((($todaySales - $yesterdaySales) / $yesterdaySales) * 100) 
            : 0;
        
        // 2. MAAGIZO MAPYA YANAYOSUBIRI KUKUBALIWA
        $newOrdersCount = RetailOrder::where('retailer_id', $retailerId)
            ->where('status', 'pending')
            ->count();
        
        // 3. MAAGIZO YANAYOENDELEA (Active Orders)
        $activeOrdersCount = RetailOrder::where('retailer_id', $retailerId)
            ->whereIn('status', ['accepted', 'picked_up', 'out_for_delivery'])
            ->count();
        
        // 4. MAUZO YA MWEZI
        $monthlySales = RetailOrder::where('retailer_id', $retailerId)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('payment_status', 'paid')
            ->sum('total_amount');
        
        $totalOrdersMonth = RetailOrder::where('retailer_id', $retailerId)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        // 5. MAAGIZO MAPYA YANAYOSUBIRI (kwa ajili ya table)
        $incomingOrders = RetailOrder::with(['items.product', 'consumer'])
            ->where('retailer_id', $retailerId)
            ->where('status', 'pending')
            ->latest('created_at')
            ->limit(5)
            ->get()
            ->map(function ($order) use ($businessProfile) {
                // Calculate distance
                $distance = $this->calculateDistance(
                    $order->delivery_latitude,
                    $order->delivery_longitude,
                    $businessProfile->shop_latitude,
                    $businessProfile->shop_longitude
                );
                
                // Get service name
                $serviceName = $order->items->first() 
                    ? ($order->items->first()->product->name ?? 'Bidhaa') 
                    : 'Huduma';
                
                return [
                    'id' => $order->id,
                    'number' => $order->order_number,
                    'customer' => $order->consumer->full_name ?? 'Mteja',
                    'service' => $serviceName,
                    'distance' => round($distance, 1) . ' km',
                    'total' => $order->total_amount,
                ];
            });
        
        // 6. MAUZO YA HIVI KARIBUNI (Yaliyokamilika na yanayoendelea)
        $recentSales = RetailOrder::with(['items.product', 'consumer'])
            ->where('retailer_id', $retailerId)
            ->whereIn('status', ['delivered', 'accepted', 'picked_up', 'out_for_delivery'])
            ->latest('created_at')
            ->limit(5)
            ->get()
            ->map(function ($order) {
                $serviceName = $order->items->first() 
                    ? ($order->items->first()->product->name ?? 'Bidhaa') 
                    : 'Huduma';
                
                // Tarehe iliyofomatiwa vizuri
                $createdDate = $order->created_at;
                $displayDate = $this->formatDisplayDate($createdDate);
                
                return [
                    'id' => $order->id,
                    'number' => $order->order_number,
                    'customer' => $order->consumer->full_name ?? 'Mteja',
                    'service' => $serviceName,
                    'total' => $order->total_amount,
                    'status' => $order->status,
                    'time' => $displayDate,
                    'created_at' => $createdDate->format('d M Y'),
                    'created_at_full' => $createdDate->format('d M Y, H:i'),
                    'created_at_raw' => $createdDate,
                ];
            });
        
        // 7. MAAGIZO YA JUMLA YANAYOSUBIRI (Kutoka kwake kwenda kwa Wholesaler)
        $pendingWholesaleOrders = WholesaleOrder::with('wholesaler')
            ->where('retailer_id', $retailerId)
            ->whereIn('status', ['pending', 'confirmed', 'processing'])
            ->latest('created_at')
            ->limit(5)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'wholesaler' => $order->wholesaler->business_name ?? 'Wholesaler',
                    'total' => $order->total_amount,
                    'status' => $order->status,
                    'created_at' => $order->created_at->format('d M Y'),
                    'created_at_full' => $order->created_at->format('d M Y, H:i'),
                ];
            });
        
        // 8. MAAGIZO YOTE YA JUMLA (Kwa ajili ya table kamili)
        $allWholesaleOrders = WholesaleOrder::with('wholesaler')
            ->where('retailer_id', $retailerId)
            ->latest('created_at')
            ->limit(5)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'wholesaler' => $order->wholesaler->business_name ?? 'Wholesaler',
                    'total' => $order->total_amount,
                    'status' => $order->status,
                    'created_at' => $order->created_at->format('d M Y'),
                    'created_at_full' => $order->created_at->format('d M Y, H:i'),
                ];
            });
        
        // 9. BIDHAA ZINAZOUZWA SANA (Top Products)
        $topProducts = Product::select('products.id', 'products.name', DB::raw('SUM(retail_order_items.quantity) as total_sold'))
            ->join('retail_order_items', 'products.id', '=', 'retail_order_items.product_id')
            ->join('retail_orders', 'retail_order_items.retail_order_id', '=', 'retail_orders.id')
            ->where('retail_orders.retailer_id', $retailerId)
            ->where('retail_orders.status', 'delivered')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_sold')
            ->limit(3)
            ->get();
        
        // 10. MAUZO KWA SIKU 7 ZILIZOPITA (Kwa ajili ya chart)
        $last7DaysSales = RetailOrder::where('retailer_id', $retailerId)
            ->where('payment_status', 'paid')
            ->whereDate('created_at', '>=', now()->subDays(7))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as total')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => Carbon::parse($item->date)->format('D'),
                    'total' => $item->total,
                ];
            });

        return view('retailer.dashboard', compact(
            'businessProfile',
            'todaySales',
            'salesChange',
            'newOrdersCount',
            'activeOrdersCount',
            'monthlySales',
            'totalOrdersMonth',
            'incomingOrders',
            'recentSales',
            'pendingWholesaleOrders',
            'allWholesaleOrders',
            'topProducts',
            'last7DaysSales'
        ));
    }
    
    /**
     * Format display date for recent sales.
     * Shows "Leo", "Jana", or full date.
     */
    private function formatDisplayDate($date)
    {
        $carbonDate = Carbon::parse($date);
        
        if ($carbonDate->isToday()) {
            return 'Leo, ' . $carbonDate->format('H:i');
        } elseif ($carbonDate->isYesterday()) {
            return 'Jana, ' . $carbonDate->format('H:i');
        } else {
            return $carbonDate->format('d M Y');
        }
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
}