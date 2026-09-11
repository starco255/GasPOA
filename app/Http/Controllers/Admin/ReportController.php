<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Payout;
use App\Models\RetailOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ReportController extends Controller
{
    /** Display a financial report sourced only from recorded orders, payments and payouts. */
    public function finance(Request $request)
    {
        $validated = $request->validate([
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date', 'after_or_equal:from_date'],
        ]);

        $isFiltered = filled($validated['from_date'] ?? null) || filled($validated['to_date'] ?? null);
        $fromDate = $isFiltered ? ($validated['from_date'] ?? now()->startOfMonth()->toDateString()) : null;
        $toDate = $isFiltered ? ($validated['to_date'] ?? now()->toDateString()) : null;

        $orders = RetailOrder::query();
        $hasPayouts = Schema::hasTable('payouts');
        $hasPayments = Schema::hasTable('payments');
        $payouts = $hasPayouts ? Payout::query() : null;
        $payments = $hasPayments ? Payment::query() : null;

        if ($isFiltered) {
            $this->applyDateRange($orders, 'created_at', $fromDate, $toDate);
            if ($payouts) $this->applyDateRange($payouts, 'created_at', $fromDate, $toDate);
            if ($payments) $this->applyDateRange($payments, 'created_at', $fromDate, $toDate);
        }

        // "Mauzo" below are confirmed customer payments; they are not platform income.
        $totalSales = (clone $orders)->where('payment_status', 'paid')->sum('total_amount');
        $totalOrders = (clone $orders)->count();
        $completedOrders = (clone $orders)->where('status', 'delivered')->count();
        $cancelledOrders = (clone $orders)->where('status', 'cancelled')->count();
        $paidOrders = (clone $orders)->where('payment_status', 'paid')->count();

        // Commission is never calculated from a fixed percentage.  It is only
        // reportable when a payout has explicitly recorded commission_amount.
        $totalCommission = $payouts ? (clone $payouts)->sum('commission_amount') : 0;
        $totalPayoutAmount = $payouts ? (clone $payouts)->sum('amount') : 0;
        $pendingPayouts = $payouts ? (clone $payouts)->where('status', 'pending')->count() : 0;

        $deliveryRate = $totalOrders > 0 ? round(($completedOrders / $totalOrders) * 100) : 0;
        $paymentAttempts = $payments ? (clone $payments)->count() : 0;
        $successfulPayments = $payments ? (clone $payments)->where('status', 'success')->count() : 0;
        $paymentSuccessRate = $paymentAttempts > 0 ? round(($successfulPayments / $paymentAttempts) * 100) : 0;

        $dailyReports = RetailOrder::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total_orders'),
                DB::raw("SUM(CASE WHEN payment_status = 'paid' THEN total_amount ELSE 0 END) as total_sales"),
                DB::raw("SUM(CASE WHEN payment_status = 'paid' THEN 1 ELSE 0 END) as paid_count"),
                DB::raw("SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered_count"),
                DB::raw("SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_count")
            )
            ->when($isFiltered, fn ($query) => $this->applyDateRange($query, 'created_at', $fromDate, $toDate))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderByDesc('date')
            ->get();

        $commissionByDate = $payouts ? Payout::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(commission_amount) as commission')
            )
            ->when($isFiltered, fn ($query) => $this->applyDateRange($query, 'created_at', $fromDate, $toDate))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->pluck('commission', 'date') : collect();

        $dailyReports->each(function ($report) use ($commissionByDate) {
            $report->commission = (float) ($commissionByDate[$report->date] ?? 0);
        });

        $paymentMethods = RetailOrder::select(
                'payment_method',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total_amount) as total')
            )
            ->where('payment_status', 'paid')
            ->when($isFiltered, fn ($query) => $this->applyDateRange($query, 'created_at', $fromDate, $toDate))
            ->groupBy('payment_method')
            ->get();

        $topRetailers = RetailOrder::where('retail_orders.payment_status', 'paid')
            ->join('business_profiles', 'retail_orders.retailer_id', '=', 'business_profiles.id')
            ->select(
                'business_profiles.business_name',
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(retail_orders.total_amount) as total_sales')
            )
            ->when($isFiltered, fn ($query) => $this->applyDateRange($query, 'retail_orders.created_at', $fromDate, $toDate))
            ->groupBy('business_profiles.business_name')
            ->orderByDesc('total_sales')
            ->limit(5)
            ->get();

        return view('admin.reports.finance', compact(
            'totalSales', 'totalOrders', 'completedOrders', 'cancelledOrders', 'paidOrders',
            'totalCommission', 'totalPayoutAmount', 'pendingPayouts', 'deliveryRate',
            'paymentAttempts', 'successfulPayments', 'paymentSuccessRate', 'dailyReports',
            'paymentMethods', 'topRetailers', 'fromDate', 'toDate', 'isFiltered'
        ));
    }

    private function applyDateRange($query, string $column, string $fromDate, string $toDate): mixed
    {
        return $query->whereBetween($column, [
            Carbon::parse($fromDate)->startOfDay(),
            Carbon::parse($toDate)->endOfDay(),
        ]);
    }
}
