<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\OtpCode;
use App\Services\PhoneVerificationService;
use App\Services\SecurityAuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PhoneVerificationController extends Controller
{
    /**
     * Show the phone verification notice.
     */
    public function show()
    {
        $user = Auth::user();
        
        // If already verified, redirect to dashboard
        if ($user->is_phone_verified) {
            return redirect()->route('dashboard');
        }
        
        // Check if there's an existing valid OTP
        $existingOtp = OtpCode::forUser($user->id)
            ->forPurpose('phone_verification')
            ->valid()
            ->latest('id')
            ->first();
        
        // If no valid OTP exists, generate one automatically
        if (!$existingOtp) {
            try {
                app(PhoneVerificationService::class)->issue($user);
            } catch (\Throwable $exception) {
                return back()->with('error', 'Imeshindikana kutuma OTP kwenye barua pepe yako. Tafadhali jaribu tena.');
            }
        }
        
        $activeOtp = $existingOtp ?? OtpCode::forUser($user->id)
            ->forPurpose('phone_verification')
            ->valid()
            ->latest('id')
            ->first();

        return view('auth.verify-phone', [
            'expiresAt' => $activeOtp?->expires_at?->timestamp,
        ]);
    }

    /**
     * Verify the phone number with OTP.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $user = Auth::user();
        
        // Verify OTP
        $otp = OtpCode::verifyForUser($user->id, $request->otp, 'phone_verification');
        
        if (!$otp) {
            app(SecurityAuditService::class)->record('phone_verification_failed', $user, [], 'warning');
            return back()->with('error', 'OTP si sahihi au imeisha muda. Tafadhali jaribu tena.');
        }
        
        // Mark phone as verified
        $user->update(['is_phone_verified' => true]);
        
        // If request is AJAX, return JSON
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Namba yako ya simu imethibitishwa!'
            ]);
        }
        
        return redirect()->route('dashboard')
                         ->with('success', 'Namba yako ya simu imethibitishwa! Karibu GasPOA.');
    }

    /**
     * Resend OTP.
     */
    public function resend(Request $request)
    {
        $user = Auth::user();
        
        // Check if user already verified
        if ($user->is_phone_verified) {
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Namba tayari imethibitishwa.'
                ]);
            }
            return redirect()->route('dashboard');
        }
        
        // Generate new OTP
        try {
            app(PhoneVerificationService::class)->issue($user);
        } catch (\Throwable $exception) {
            return back()->with('error', 'Imeshindikana kutuma OTP kwenye barua pepe yako. Tafadhali jaribu tena.');
        }
        
        // If request is AJAX, return JSON
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'OTP mpya imetumwa kwenye barua pepe yako!'
            ]);
        }
        
        return back()->with('success', 'OTP mpya imetumwa kwenye barua pepe yako.');
    }
}
