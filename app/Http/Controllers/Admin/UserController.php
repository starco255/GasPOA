<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\BusinessProfile;
use App\Models\RetailOrder;
use App\Services\SecurityAuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class UserController extends Controller
{
    /**
     * Display all users with filters AND pending verifications.
     */
    public function index(Request $request)
    {
        // =====================================================
        // 1. WATUMIAJI WOTE (TAB 1)
        // =====================================================
        $query = User::orderBy('created_at', 'desc');
        
        // Filter by user type
        if ($request->filled('type')) {
            $query->where('user_type', $request->type);
        }
        
        // Search by name, phone, or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'LIKE', "%{$search}%")
                  ->orWhere('phone_number', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }
        
        // Filter by active status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }
        
        $users = $query->paginate(20)->withQueryString();
        
        // Statistics for Tab 1
        $totalUsers = User::count();
        $activeUsers = User::where('is_active', true)->count();
        $inactiveUsers = User::where('is_active', false)->count();
        $totalConsumers = User::where('user_type', 'consumer')->count();
        $totalRetailers = User::where('user_type', 'retailer')->count();
        $totalWholesalers = User::where('user_type', 'wholesaler')->count();

        // ✅ Maagizo 5 ya hivi karibuni (kwa ajili ya kuonyesha chini ya jedwali)
        $recentOrders = RetailOrder::with(['consumer', 'retailer'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->order_number,
                    'consumer' => $order->consumer->full_name ?? 'Mteja',
                    'retailer' => $order->retailer->business_name ?? 'N/A',
                    'total' => $order->total_amount,
                    'status' => $order->status,
                    'date' => $order->created_at->format('d/m/Y'),
                ];
            });
        
        // =====================================================
        // 2. ZINAZOSUBIRI UIDHINISHAJI (TAB 2)
        // =====================================================
        $pendingQuery = BusinessProfile::with('user')
            ->orderBy('created_at', 'desc');
        
        // Filter by business type
        if ($request->filled('vtype')) {
            $pendingQuery->where('business_type', $request->vtype);
        }
        
        // Filter by verification status
        if ($request->filled('vstatus')) {
            if ($request->vstatus === 'verified') {
                $pendingQuery->where('is_open', true);
            } elseif ($request->vstatus === 'unverified') {
                $pendingQuery->where(function ($q) {
                    $q->where('is_open', false)
                      ->orWhereNull('is_open');
                });
            }
        }
        
        // Use separate page parameter for tab 2
        $pending = $pendingQuery->paginate(20, ['*'], 'vpage')->withQueryString();
        
        // Statistics for Tab 2
        $totalPending = BusinessProfile::where('is_open', false)
            ->orWhereNull('is_open')
            ->count();
        $verifiedCount = BusinessProfile::where('is_open', true)->count();
        $retailersPending = BusinessProfile::where('business_type', 'retailer')->count();
        $wholesalersPending = BusinessProfile::where('business_type', 'wholesaler')->count();
        $pendingVerifications = $totalPending; // For the badge on the tab
        
        return view('admin.users.index', compact(
            // Tab 1 data
            'users',
            'totalUsers',
            'activeUsers',
            'inactiveUsers',
            'totalConsumers',
            'totalRetailers',
            'totalWholesalers',
            'recentOrders',          // ✅ imeongezwa
            // Tab 2 data
            'pending',
            'totalPending',
            'verifiedCount',
            'retailersPending',
            'wholesalersPending',
            'pendingVerifications'
        ));
    }
    
    /**
     * Toggle user active status.
     */
    public function toggle($id)
    {
        $user = User::findOrFail($id);
        
        // Cannot deactivate yourself
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                             ->with('error', 'Huwezi kujifungia mwenyewe. Jaribu kwa watumiaji wengine.');
        }
        
        $user->is_active = !$user->is_active;
        $user->save();
        
        $status = $user->is_active ? 'imefunguliwa' : 'imefungwa';
        
        Log::info('User status toggled', [
            'admin_id' => auth()->id(),
            'user_id' => $user->id,
            'new_status' => $user->is_active ? 'active' : 'inactive',
        ]);
        
        return redirect()->route('admin.users.index')
                         ->with('success', "Akaunti ya {$user->full_name} {$status}.");
    }

    /**
     * Permanently remove a user and the data that belongs to their account.
     *
     * The imported database deliberately has foreign keys without cascade on
     * business data, so the dependent rows are removed in child-first order.
     * No schema or table is altered by this operation.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Huwezi kufuta akaunti yako mwenyewe ukiwa umeingia.');
        }

        $userName = $user->full_name;
        $userId = $user->id;

        try {
            DB::transaction(function () use ($user) {
                $this->deleteUserData($user);
            });
        } catch (\Throwable $exception) {
            Log::error('Admin could not delete user and related data.', [
                'admin_id' => auth()->id(),
                'user_id' => $userId,
                'error' => $exception->getMessage(),
            ]);

            return redirect()->route('admin.users.index')
                ->with('error', 'Imeshindikana kufuta mtumiaji. Hakuna taarifa iliyoondolewa. Tafadhali jaribu tena.');
        }

        Log::warning('User permanently deleted by admin with related data.', [
            'admin_id' => auth()->id(),
            'user_id' => $userId,
        ]);

        app(SecurityAuditService::class)->record(
            'account_deleted',
            $user,
            ['deleted_user_id' => $userId],
            'warning',
            "Akaunti imeondolewa na msimamizi: {$userName}"
        );

        return redirect()->route('admin.users.index')
            ->with('success', "Akaunti ya {$userName} na taarifa zake zote zimeondolewa.");
    }

    private function deleteUserData(User $user): void
    {
        $businessProfileIds = DB::table('business_profiles')
            ->where('user_id', $user->id)
            ->pluck('id')
            ->all();

        $retailOrderIds = DB::table('retail_orders')
            ->where(function ($query) use ($user, $businessProfileIds) {
                $query->where('consumer_id', $user->id);

                if ($businessProfileIds) {
                    $query->orWhereIn('retailer_id', $businessProfileIds);
                }
            })
            ->pluck('id')
            ->all();

        $wholesaleOrderIds = $businessProfileIds
            ? DB::table('wholesale_orders')
                ->where(function ($query) use ($businessProfileIds) {
                    $query->whereIn('retailer_id', $businessProfileIds)
                        ->orWhereIn('wholesaler_id', $businessProfileIds);
                })
                ->pluck('id')
                ->all()
            : [];

        // Messages reference both the user and retail orders, so remove them
        // before deleting either parent record.
        DB::table('messages')
            ->where('sender_id', $user->id)
            ->orWhere('receiver_id', $user->id)
            ->delete();

        if ($retailOrderIds) {
            DB::table('messages')->whereIn('order_id', $retailOrderIds)->delete();
            $this->deleteOrderPayments('retail', $retailOrderIds);
            DB::table('retail_order_items')->whereIn('retail_order_id', $retailOrderIds)->delete();
            DB::table('retail_orders')->whereIn('id', $retailOrderIds)->delete();
        }

        if ($wholesaleOrderIds) {
            $this->deleteOrderPayments('wholesale', $wholesaleOrderIds);
            DB::table('wholesale_order_items')->whereIn('wholesale_order_id', $wholesaleOrderIds)->delete();
            DB::table('wholesale_orders')->whereIn('id', $wholesaleOrderIds)->delete();
        }

        if ($businessProfileIds) {
            DB::table('inventory')->whereIn('business_profile_id', $businessProfileIds)->delete();
            DB::table('payouts')->whereIn('business_profile_id', $businessProfileIds)->delete();
            DB::table('business_profiles')->whereIn('id', $businessProfileIds)->delete();
        }

        DB::table('addresses')->where('user_id', $user->id)->delete();
        DB::table('otp_codes')->where('user_id', $user->id)->delete();
        DB::table('password_reset_tokens')->where('email', $user->email)->delete();
        DB::table('phone_password_resets')->where('phone_number', $user->phone_number)->delete();
        DB::table('sessions')->where('user_id', $user->id)->delete();
        DB::table('ussd_sessions')->where('phone_number', $user->phone_number)->delete();

        if (Schema::hasTable('personal_access_tokens')) {
            DB::table('personal_access_tokens')
                ->where('tokenable_type', User::class)
                ->where('tokenable_id', $user->id)
                ->delete();
        }

        $user->delete();
    }

    /** Remove payment, payout, and webhook records attached to deleted orders. */
    private function deleteOrderPayments(string $orderType, array $orderIds): void
    {
        $paymentIds = DB::table('payments')
            ->where('order_type', $orderType)
            ->whereIn('order_id', $orderIds)
            ->pluck('id')
            ->all();

        if (!$paymentIds) {
            return;
        }

        DB::table('payouts')->whereIn('payment_id', $paymentIds)->delete();
        DB::table('payment_webhook_events')->whereIn('payment_id', $paymentIds)->delete();
        DB::table('payments')->whereIn('id', $paymentIds)->delete();
    }
    
    /**
     * Show user details.
     */
    public function show($id)
    {
        $user = User::with(['businessProfile', 'retailOrders.items.product', 'retailOrders.retailer'])
            ->findOrFail($id);
        
        // Get user statistics
        $totalOrders = $user->retailOrders()->count();
        $totalSpent = $user->retailOrders()
            ->where('payment_status', 'paid')
            ->sum('total_amount');
        $activeOrders = $user->retailOrders()
            ->whereIn('status', ['pending', 'accepted', 'picked_up', 'out_for_delivery'])
            ->count();
        $completedOrders = $user->retailOrders()
            ->where('status', 'delivered')
            ->count();
        $cancelledOrders = $user->retailOrders()
            ->where('status', 'cancelled')
            ->count();
        
        // Get recent orders
        $recentOrders = $user->retailOrders()
            ->with(['retailer', 'items.product'])
            ->latest()
            ->limit(10)
            ->get();
        
        return view('admin.users.show', compact(
            'user',
            'totalOrders',
            'totalSpent',
            'activeOrders',
            'completedOrders',
            'cancelledOrders',
            'recentOrders'
        ));
    }
    
    /**
     * Show business verification page (standalone - still works).
     */
    public function verify(Request $request)
    {
        // Redirect to index with verify tab active
        return redirect()->route('admin.users.index', ['tab' => 'verify']);
    }
    
    /**
     * Approve business verification.
     */
    public function approve($id)
    {
        $businessProfile = BusinessProfile::with('user')->findOrFail($id);
        
        $businessProfile->is_open = true;
        $businessProfile->save();
        
        Log::info('Business approved', [
            'admin_id' => auth()->id(),
            'business_id' => $businessProfile->id,
            'business_name' => $businessProfile->business_name,
        ]);
        
        return redirect()->route('admin.users.index', ['tab' => 'verify'])
                         ->with('success', "Biashara ya {$businessProfile->business_name} imeidhinishwa!");
    }
    
    /**
     * Reject business verification.
     */
    public function reject(Request $request, $id)
    {
        $businessProfile = BusinessProfile::with('user')->findOrFail($id);
        
        $reason = $request->input('rejection_reason', 'Haijatajwa');
        
        $businessProfile->is_open = false;
        $businessProfile->save();
        
        Log::info('Business rejected', [
            'admin_id' => auth()->id(),
            'business_id' => $businessProfile->id,
            'business_name' => $businessProfile->business_name,
            'reason' => $reason,
        ]);
        
        return redirect()->route('admin.users.index', ['tab' => 'verify'])
                         ->with('success', "Biashara ya {$businessProfile->business_name} imekataliwa.");
    }
}
