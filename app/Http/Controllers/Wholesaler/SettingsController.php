<?php

namespace App\Http\Controllers\Wholesaler;

use App\Http\Controllers\Controller;
use App\Models\BusinessProfile;
use App\Models\User;
use App\Services\PhoneVerificationService;
use App\Services\SecurityAuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    /**
     * Show shop/warehouse settings page (Ghala Langu).
     */
    public function shop()
    {
        $user = Auth::user();
        
        $businessProfile = BusinessProfile::where('user_id', $user->id)
            ->where('business_type', 'wholesaler')
            ->first();
        
        if (!$businessProfile) {
            $businessProfile = BusinessProfile::create([
                'user_id' => $user->id,
                'business_name' => $user->full_name . ' Wholesale',
                'business_type' => 'wholesaler',
                'shop_latitude' => '-6.8192',
                'shop_longitude' => '39.2695',
                'physical_address' => 'Dar es Salaam, Tanzania',
                'is_open' => true,
                'service_radius_km' => 50,
                'can_deliver' => false,
                'delivery_fee_per_km' => 0,
            ]);
        }
        
        return view('wholesaler.settings.shop', compact('businessProfile'));
    }
    
    /**
     * Update shop/warehouse settings.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'business_name' => 'required|string|max:100',
            'tinn_number' => 'nullable|string|max:20',
            'physical_address' => 'required|string',
            'shop_latitude' => 'required|numeric',
            'shop_longitude' => 'required|numeric',
            'is_open' => 'boolean',
            'can_deliver' => 'boolean',
            'accept_cash' => 'boolean',
            'accept_mpesa' => 'boolean',
            'accept_tigopesa' => 'boolean',
            'accept_airtelmoney' => 'boolean',
            'accept_halopesa' => 'boolean',
            'accept_bank' => 'boolean',
            'mpesa_number' => [Rule::requiredIf($request->boolean('accept_mpesa')), 'nullable', 'string', 'max:15'],
            'mixx_number' => [Rule::requiredIf($request->boolean('accept_tigopesa')), 'nullable', 'string', 'max:15'],
            'airtel_number' => [Rule::requiredIf($request->boolean('accept_airtelmoney')), 'nullable', 'string', 'max:15'],
            'halopesa_number' => [Rule::requiredIf($request->boolean('accept_halopesa')), 'nullable', 'string', 'max:15'],
            'bank_name' => [Rule::requiredIf($request->boolean('accept_bank')), 'nullable', Rule::in(['NMB', 'CRDB', 'NBC', 'Nyingine'])],
            'bank_account_number' => [Rule::requiredIf($request->boolean('accept_bank')), 'nullable', 'string', 'max:50'],
            'bank_account_name' => [Rule::requiredIf($request->boolean('accept_bank')), 'nullable', 'string', 'max:100'],
        ]);
        
        $businessProfile = BusinessProfile::where('user_id', $user->id)
            ->where('business_type', 'wholesaler')
            ->first();
        
        $updateData = [
            'business_name' => $validated['business_name'] ?? ($businessProfile->business_name ?? $user->full_name . ' Wholesale'),
            'tinn_number' => $validated['tinn_number'] ?? ($businessProfile->tinn_number ?? null),
            'physical_address' => $validated['physical_address'] ?? ($businessProfile->physical_address ?? 'Dar es Salaam, Tanzania'),
            'shop_latitude' => $validated['shop_latitude'] ?? ($businessProfile->shop_latitude ?? -6.8192),
            'shop_longitude' => $validated['shop_longitude'] ?? ($businessProfile->shop_longitude ?? 39.2695),
            'is_open' => $request->has('is_open'),
            'can_deliver' => $request->has('can_deliver'),
            'accept_cash' => $request->has('accept_cash'),
            'accept_mobile_money' => $request->hasAny(['accept_mpesa', 'accept_tigopesa', 'accept_airtelmoney', 'accept_halopesa']),
            'accept_mpesa' => $request->has('accept_mpesa'),
            'accept_tigopesa' => $request->has('accept_tigopesa'),
            'accept_airtelmoney' => $request->has('accept_airtelmoney'),
            'accept_halopesa' => $request->has('accept_halopesa'),
            'accept_bank' => $request->has('accept_bank'),
            'mpesa_number' => $validated['mpesa_number'] ?? null,
            'mixx_number' => $validated['mixx_number'] ?? null,
            'airtel_number' => $validated['airtel_number'] ?? null,
            'halopesa_number' => $validated['halopesa_number'] ?? null,
            'bank_name' => $validated['bank_name'] ?? null,
            'bank_account_number' => $validated['bank_account_number'] ?? null,
            'bank_account_name' => $validated['bank_account_name'] ?? null,
        ];
        
        if ($businessProfile) {
            $businessProfile->update($updateData);
        } else {
            $updateData['user_id'] = $user->id;
            $updateData['business_type'] = 'wholesaler';
            $updateData['service_radius_km'] = 50;
            BusinessProfile::create($updateData);
        }
        
        return redirect()->route('wholesaler.settings.shop')
                         ->with('success', 'Mipangilio ya ghala imehifadhiwa!');
    }
    
    /**
     * Show account settings page.
     */
    public function account()
    {
        $user = User::find(Auth::id());
        
        $businessProfile = BusinessProfile::where('user_id', $user->id)
            ->where('business_type', 'wholesaler')
            ->first();
        
        if (!$businessProfile) {
            $businessProfile = new BusinessProfile([
                'business_name' => $user->full_name . ' Wholesale',
                'phone_number' => $user->phone_number,
                'accept_cash' => true,
                'accept_mobile_money' => true,
                'accept_bank' => false,
                'notify_new_order' => true,
                'notify_chat' => true,
            ]);
        }
        
        return view('wholesaler.account.edit', compact('user', 'businessProfile'));
    }
    
    /**
     * Update user profile (Mipangilio ya Akaunti).
     * UPDATES BOTH TABLES SIMULTANEOUSLY - NO CONDITIONS
     */
    public function updateProfile(Request $request)
    {
        $user = User::find(Auth::id());
        
        if (!$user) {
            return redirect()->route('wholesaler.account.edit')
                             ->with('error', 'Mtumiaji hajapatikana.');
        }
        
        // Validation
        $validated = $request->validate([
            'business_name' => 'nullable|string|max:100',
            'phone_number' => 'nullable|string|max:15|unique:users,phone_number,' . $user->id,
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'notify_new_order' => 'boolean',
            'notify_chat' => 'boolean',
        ]);
        
        // ✅ Prepare business_name value (itakayotumika kwenye table zote mbili)
        $businessName = !empty($validated['business_name']) 
            ? $validated['business_name'] 
            : $user->full_name;
        
        // ✅ Prepare phone_number value
        $fullPhoneNumber = null;
        $isPhoneVerified = $user->is_phone_verified;
        $phoneChanged = false;
        
        if (!empty($validated['phone_number'])) {
            $phone = preg_replace('/\D/', '', $validated['phone_number']);
            if (str_starts_with($phone, '0')) {
                $phone = substr($phone, 1);
            }
            $fullPhoneNumber = '+255' . $phone;
            
            if ($fullPhoneNumber !== $user->phone_number) {
                $isPhoneVerified = false;
                $phoneChanged = true;
            }
        } else {
            $fullPhoneNumber = $user->phone_number;
        }
        
        // ✅ Prepare email value
        $email = isset($validated['email']) ? $validated['email'] : $user->email;
        
        // =====================================================
        // UPDATE USERS TABLE - DIRECT
        // =====================================================
        DB::table('users')
            ->where('id', $user->id)
            ->update([
                'full_name' => $businessName,
                'phone_number' => $fullPhoneNumber,
                'email' => $email,
                'is_phone_verified' => $isPhoneVerified,
                'updated_at' => now(),
            ]);

        if ($phoneChanged) {
            $audit = app(SecurityAuditService::class);
            $audit->record('phone_number_changed', $user, [
                'old_phone' => $audit->maskPhone($user->phone_number),
                'new_phone' => $audit->maskPhone($fullPhoneNumber),
                'verification_required' => true,
            ], 'warning');
        }
        
        Log::info('✅ USERS TABLE UPDATED', [
            'user_id' => $user->id,
            'full_name' => $businessName,
            'phone_number' => $fullPhoneNumber,
            'email' => $email,
        ]);
        
        // =====================================================
        // UPDATE BUSINESS_PROFILES TABLE - DIRECT
        // =====================================================
        $businessProfile = BusinessProfile::where('user_id', $user->id)
            ->where('business_type', 'wholesaler')
            ->first();
        
        if ($businessProfile) {
            $businessProfile->update([
                'business_name' => $businessName,
                'phone_number' => $fullPhoneNumber,
                'notify_new_order' => $request->has('notify_new_order'),
                'notify_chat' => $request->has('notify_chat'),
            ]);
            
            Log::info('✅ BUSINESS PROFILE UPDATED', [
                'profile_id' => $businessProfile->id,
                'business_name' => $businessName,
            ]);
        } else {
            BusinessProfile::create([
                'user_id' => $user->id,
                'business_type' => 'wholesaler',
                'business_name' => $businessName,
                'phone_number' => $fullPhoneNumber,
                'shop_latitude' => -6.8192,
                'shop_longitude' => 39.2695,
                'physical_address' => 'Dar es Salaam, Tanzania',
                'is_open' => true,
                'service_radius_km' => 50,
                'notify_new_order' => $request->has('notify_new_order'),
                'notify_chat' => $request->has('notify_chat'),
            ]);
            
            Log::info('✅ BUSINESS PROFILE CREATED');
        }
        
        // ✅ THIBITISHA: Angalia data zote mbili baada ya update
        $verifiedUser = DB::table('users')->where('id', $user->id)->first();
        $verifiedProfile = BusinessProfile::where('user_id', $user->id)
            ->where('business_type', 'wholesaler')
            ->first();
        
        Log::info('🎉 BOTH TABLES UPDATED - VERIFICATION', [
            'user_id' => $user->id,
            'users_table_name' => $verifiedUser->full_name,
            'users_table_phone' => $verifiedUser->phone_number,
            'users_table_email' => $verifiedUser->email,
            'business_profile_name' => $verifiedProfile->business_name ?? 'N/A',
            'business_profile_phone' => $verifiedProfile->phone_number ?? 'N/A',
        ]);

        if ($phoneChanged) {
            try {
                app(PhoneVerificationService::class)->issue(User::findOrFail($user->id));
            } catch (\Throwable $exception) {
                return redirect()->route('wholesaler.account.edit')
                    ->with('error', 'Namba imehifadhiwa, lakini imeshindikana kutuma OTP kwenye barua pepe yako. Tafadhali jaribu tena.');
            }

            return redirect()->route('verification.phone.notice')
                ->with('info', 'Namba ya simu imebadilishwa. OTP imetumwa kwenye barua pepe yako.');
        }
        
        return redirect()->route('wholesaler.account.edit')
                         ->with('success', 'Maelezo ya akaunti yamesasishwa! Majina yamebadilishwa kwenye akaunti na ghala lako.');
    }
    
    /**
     * Update password.
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        
        DB::table('users')
            ->where('id', $user->id)
            ->update([
                'password_hash' => Hash::make($validated['password']),
                'updated_at' => now(),
            ]);

        app(SecurityAuditService::class)->record('password_changed', $user, [], 'warning');
        
        return redirect()->route('wholesaler.account.edit')
                         ->with('success', 'Nywila imebadilishwa kikamilifu!');
    }

    /**
     * Profile method - kept for backward compatibility.
     */
    public function profile()
    {
        return redirect()->route('wholesaler.settings.shop');
    }
}
