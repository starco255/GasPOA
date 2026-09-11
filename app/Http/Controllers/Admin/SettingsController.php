<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\OtpCode;
use App\Services\PhoneVerificationService;
use App\Services\SecurityAuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{
    /**
     * Show admin profile settings page.
     */
    public function profile()
    {
        $user = User::find(Auth::id());
        
        return view('admin.settings.profile', compact('user'));
    }
    
    /**
     * Update admin profile information.
     */
    public function update(Request $request)
    {
        $user = User::find(Auth::id());
        
        $validated = $request->validate([
            'full_name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone_number' => 'nullable|string|max:15|unique:users,phone_number,' . $user->id,
        ]);
        
        // Process phone number
        $fullPhoneNumber = $user->phone_number;
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
        }
        
        DB::table('users')
            ->where('id', $user->id)
            ->update([
                'full_name' => $validated['full_name'],
                'email' => $validated['email'],
                'phone_number' => $fullPhoneNumber,
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
        
        // If phone changed, redirect to OTP verification
        if ($phoneChanged) {
            try {
                app(PhoneVerificationService::class)->issue(User::findOrFail($user->id));
            } catch (\Throwable $exception) {
                return redirect()->route('admin.settings.profile')
                    ->with('error', 'Namba imehifadhiwa, lakini imeshindikana kutuma OTP kwenye barua pepe yako. Tafadhali jaribu tena.');
            }
            
            return redirect()->route('admin.phone.verify')
                             ->with('info', 'Namba yako ya simu imebadilishwa. OTP imetumwa kwenye barua pepe yako.');
        }
        
        return redirect()->route('admin.settings.profile')
                         ->with('success', 'Maelezo ya akaunti yamesasishwa!');
    }
    
    /**
     * Show phone verification page.
     */
    public function showPhoneVerification()
    {
        $user = Auth::user();
        
        $existingOtp = OtpCode::forUser($user->id)
            ->forPurpose('phone_verification')
            ->valid()
            ->latest('id')
            ->first();
        if (!$existingOtp) {
            try {
                app(PhoneVerificationService::class)->issue($user);
            } catch (\Throwable $exception) {
                return redirect()->route('admin.settings.profile')
                    ->with('error', 'Imeshindikana kutuma OTP kwenye barua pepe yako. Tafadhali jaribu tena.');
            }
        }
        
        $activeOtp = $existingOtp ?? OtpCode::forUser($user->id)
            ->forPurpose('phone_verification')
            ->valid()
            ->latest('id')
            ->first();

        return view('admin.auth.verify-phone', [
            'user' => $user,
            'expiresAt' => $activeOtp?->expires_at?->timestamp,
        ]);
    }
    
    /**
     * Verify phone with OTP.
     */
    public function verifyPhone(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);
        
        $user = Auth::user();
        
        // Find valid OTP
        $otpCode = OtpCode::where('user_id', $user->id)
            ->where('code', $request->otp)
            ->where('purpose', 'phone_verification')
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->first();
        
        if (!$otpCode) {
            return back()->with('error', 'OTP si sahihi au imeisha muda. Tafadhali jaribu tena.');
        }
        
        // Mark OTP as used
        $otpCode->is_used = true;
        $otpCode->save();
        
        // Mark phone as verified
        DB::table('users')
            ->where('id', $user->id)
            ->update([
                'is_phone_verified' => true,
                'updated_at' => now(),
            ]);

        app(SecurityAuditService::class)->record('phone_verified', $user, [
            'phone' => app(SecurityAuditService::class)->maskPhone($user->phone_number),
        ]);
        
        Log::info('Admin phone verified', [
            'admin_id' => $user->id,
        ]);
        
        return redirect()->route('admin.settings.profile')
                         ->with('success', 'Namba yako ya simu imethibitishwa kikamilifu!');
    }
    
    /**
     * Resend OTP.
     */
    public function resendOtp()
    {
        $user = Auth::user();
        
        try {
            app(PhoneVerificationService::class)->issue($user);
        } catch (\Throwable $exception) {
            return redirect()->route('admin.phone.verify')
                ->with('error', 'Imeshindikana kutuma OTP kwenye barua pepe yako. Tafadhali jaribu tena.');
        }
        
        return redirect()->route('admin.phone.verify')
                         ->with('success', 'OTP mpya imetumwa kwenye barua pepe yako.');
    }
    
    /**
     * Update admin password.
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
        
        return redirect()->route('admin.settings.profile')
                         ->with('success', 'Nywila imebadilishwa kikamilifu!');
    }


    /**
 * Show active sessions.
 */
public function sessions()
{
    $user = Auth::user();
    
    // Get all active sessions for this user
    $sessions = DB::table('sessions')
        ->where('user_id', $user->id)
        ->orderBy('last_activity', 'desc')
        ->get()
        ->map(function ($session) {
            $decoded = base64_decode($session->payload);
            
            return [
                'id' => $session->id,
                'ip_address' => $session->ip_address,
                'user_agent' => $this->parseUserAgent($session->user_agent),
                'last_activity' => \Carbon\Carbon::createFromTimestamp($session->last_activity),
                'last_activity_human' => \Carbon\Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
                'is_current' => $session->id === session()->getId(),
            ];
        });
    
    return view('admin.settings.sessions', compact('sessions'));
}

/**
 * Destroy a session (logout other device).
 */
public function destroySession(Request $request, $sessionId)
{
    // Cannot destroy current session
    if ($sessionId === session()->getId()) {
        return redirect()->route('admin.settings.sessions')
                         ->with('error', 'Huwezi kufunga kikao cha sasa. Tumia "Toka" badala yake.');
    }
    
    DB::table('sessions')
        ->where('id', $sessionId)
        ->where('user_id', Auth::id())
        ->delete();
    
    Log::info('Session destroyed by admin', [
        'admin_id' => Auth::id(),
        'session_id' => $sessionId,
    ]);
    
    return redirect()->route('admin.settings.sessions')
                     ->with('success', 'Kikao kimefungwa kwa ufanisi!');
}

/**
 * Destroy all other sessions (logout all devices except current).
 */
public function destroyAllSessions(Request $request)
{
    $currentSessionId = session()->getId();
    
    DB::table('sessions')
        ->where('user_id', Auth::id())
        ->where('id', '!=', $currentSessionId)
        ->delete();
    
    Log::info('All other sessions destroyed by admin', [
        'admin_id' => Auth::id(),
    ]);
    
    return redirect()->route('admin.settings.sessions')
                     ->with('success', 'Vikao vyote vingine vimefungwa! Kikao cha sasa ndicho pekee kinachobaki.');
}

/**
 * Parse user agent string to get browser and device info.
 */
private function parseUserAgent($userAgent)
{
    if (!$userAgent) {
        return 'Haijulikani';
    }
    
    $info = [];
    
    // Detect browser
    if (strpos($userAgent, 'Chrome') !== false) {
        $info[] = 'Chrome';
    } elseif (strpos($userAgent, 'Firefox') !== false) {
        $info[] = 'Firefox';
    } elseif (strpos($userAgent, 'Safari') !== false) {
        $info[] = 'Safari';
    } elseif (strpos($userAgent, 'Edge') !== false) {
        $info[] = 'Edge';
    } elseif (strpos($userAgent, 'Opera') !== false) {
        $info[] = 'Opera';
    }
    
    // Detect device
    if (strpos($userAgent, 'Windows') !== false) {
        $info[] = 'Windows';
    } elseif (strpos($userAgent, 'Mac') !== false) {
        $info[] = 'Mac';
    } elseif (strpos($userAgent, 'Linux') !== false) {
        $info[] = 'Linux';
    } elseif (strpos($userAgent, 'Android') !== false) {
        $info[] = 'Android';
    } elseif (strpos($userAgent, 'iPhone') !== false || strpos($userAgent, 'iPad') !== false) {
        $info[] = 'iOS';
    }
    
    return !empty($info) ? implode(' | ', $info) : 'Haijulikani';
}

}
