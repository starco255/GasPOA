<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessProfile;
use App\Models\RetailOrder;
use App\Models\SecurityAuditLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class LogController extends Controller
{
    /** Display ordinary system activity together with immutable security-audit events. */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'filter' => ['nullable', 'in:all,user,order,business'],
            'security_filter' => ['nullable', 'in:all,login,password,phone,account,warning'],
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date', 'after_or_equal:from_date'],
        ]);

        $filter = $validated['filter'] ?? 'all';
        $securityFilter = $validated['security_filter'] ?? 'all';
        $fromDate = $validated['from_date'] ?? null;
        $toDate = $validated['to_date'] ?? null;

        $hasSecurityAuditLogs = Schema::hasTable('security_audit_logs');
        $securityQuery = $hasSecurityAuditLogs
            ? SecurityAuditLog::with(['actor:id,full_name,phone_number', 'subject:id,full_name,phone_number'])->latest('occurred_at')
            : null;

        if ($securityQuery && ($fromDate || $toDate)) {
            $securityQuery->whereBetween('occurred_at', [
                Carbon::parse($fromDate ?? now()->startOfMonth())->startOfDay(),
                Carbon::parse($toDate ?? now())->endOfDay(),
            ]);
        }

        if ($securityQuery) $this->applySecurityFilter($securityQuery, $securityFilter);
        $securityLogs = $securityQuery ? $securityQuery->limit(100)->get() : collect();

        $securityTotals = [
            'all' => $hasSecurityAuditLogs ? SecurityAuditLog::count() : 0,
            'today' => $hasSecurityAuditLogs ? SecurityAuditLog::whereDate('occurred_at', today())->count() : 0,
            'unsafe_logins' => $hasSecurityAuditLogs ? SecurityAuditLog::whereIn('event_type', ['login_failed', 'login_rate_limited'])->count() : 0,
            'password_events' => $hasSecurityAuditLogs ? SecurityAuditLog::where('event_type', 'like', 'password_%')->count() : 0,
            'phone_events' => $hasSecurityAuditLogs ? SecurityAuditLog::where(function ($query) {
                $query->where('event_type', 'phone_number_changed')
                    ->orWhere('event_type', 'like', 'phone_%');
            })->count() : 0,
        ];

        $activities = $this->systemActivities($filter);

        return view('admin.logs.index', compact(
            'activities', 'filter', 'securityLogs', 'securityFilter', 'securityTotals', 'fromDate', 'toDate', 'hasSecurityAuditLogs'
        ));
    }

    private function systemActivities(string $filter)
    {
        $recentUsers = User::latest('created_at')->limit(20)->get()->map(fn (User $user) => [
            'type' => 'user_registration', 'icon' => 'person-plus', 'color' => 'success',
            'description' => "{$user->full_name} alijisajili kama " . $this->getUserTypeLabel($user->user_type),
            'time' => $user->created_at, 'time_human' => $user->created_at->diffForHumans(), 'user' => $user->full_name,
        ]);

        $recentOrders = RetailOrder::with(['consumer', 'retailer'])->latest('created_at')->limit(20)->get()->map(function (RetailOrder $order) {
            $consumer = $order->consumer?->full_name ?? 'Mteja';
            $retailer = $order->retailer?->business_name ?? 'Haijajulikana';
            return [
                'type' => 'order_' . $order->status, 'icon' => 'cart',
                'color' => $order->status === 'delivered' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'warning'),
                'description' => "Agizo #{$order->order_number} - {$consumer} → {$retailer} - TZS " . number_format($order->total_amount) . ' (' . $this->getOrderStatusLabel($order->status) . ')',
                'time' => $order->created_at, 'time_human' => $order->created_at->diffForHumans(), 'user' => $consumer,
            ];
        });

        $businesses = BusinessProfile::with('user')->latest('created_at')->limit(20)->get()->map(function (BusinessProfile $business) {
            return [
                'type' => 'business_verification', 'icon' => $business->is_open ? 'check-circle' : 'x-circle',
                'color' => $business->is_open ? 'success' : 'warning',
                'description' => "Biashara '{$business->business_name}' - " . ($business->is_open ? 'Imeidhinishwa' : 'Hajaidhinishwa'),
                'time' => $business->created_at, 'time_human' => $business->created_at->diffForHumans(),
                'user' => $business->user?->full_name ?? 'Haijulikani',
            ];
        });

        $activities = collect()->merge($recentUsers)->merge($recentOrders)->merge($businesses)->sortByDesc('time');

        return $filter === 'all'
            ? $activities->take(50)->values()
            : $activities->filter(fn (array $activity) => str_contains($activity['type'], $filter))->take(50)->values();
    }

    private function applySecurityFilter($query, string $filter): void
    {
        match ($filter) {
            'login' => $query->where('event_type', 'like', 'login_%'),
            'password' => $query->where('event_type', 'like', 'password_%'),
            'phone' => $query->where(function ($builder) {
                $builder->where('event_type', 'phone_number_changed')->orWhere('event_type', 'like', 'phone_%');
            }),
            'account' => $query->whereIn('event_type', ['account_registered', 'account_activated', 'account_deactivated']),
            'warning' => $query->whereIn('severity', ['warning', 'danger']),
            default => null,
        };
    }

    private function getUserTypeLabel(string $type): string
    {
        return match ($type) {
            'consumer' => 'Mtumiaji', 'retailer' => 'Muuza Rejareja', 'wholesaler' => 'Muuza Jumla', 'admin' => 'Msimamizi', default => ucfirst($type),
        };
    }

    private function getOrderStatusLabel(string $status): string
    {
        return match ($status) {
            'pending' => 'Inasubiri', 'accepted' => 'Imekubaliwa', 'picked_up' => 'Imeshachukuliwa',
            'out_for_delivery' => 'Njiani', 'delivered' => 'Imekamilika', 'cancelled' => 'Imefutwa', default => ucfirst($status),
        };
    }
}
