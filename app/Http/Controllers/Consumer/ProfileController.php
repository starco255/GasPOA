<?php

namespace App\Http\Controllers\Consumer;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\OtpCode;
use App\Services\PhoneVerificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Show the profile edit page.
     */
    public function edit()
    {
        $user = Auth::user();
        return view('consumer.profile.edit', compact('user'));
    }

    /**
     * Update user profile.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'full_name' => 'required|string|max:100',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'phone_number' => 'required|string|max:15',
        ]);
        
        // Format phone number
        if (!str_starts_with($validated['phone_number'], '+255')) {
            $validated['phone_number'] = '+255' . ltrim($validated['phone_number'], '0');
        }
        
        // Check if phone number changed
        $phoneChanged = $validated['phone_number'] !== $user->phone_number;
        
        if ($phoneChanged) {
            $validated['is_phone_verified'] = false;
        }
        
        $user->update($validated);
        
        if ($phoneChanged) {
            try {
                $this->sendOtp($user);
            } catch (\Throwable $exception) {
                return redirect()->route('consumer.profile.edit')
                    ->with('error', 'Namba imehifadhiwa, lakini imeshindikana kutuma OTP kwenye barua pepe yako. Tafadhali jaribu tena.');
            }

            session(['active_tab' => 'profile']);
            return redirect()->route('consumer.profile.edit')
                             ->with('info', 'Namba ya simu imebadilishwa. OTP imetumwa kwenye barua pepe yako.');
        }
        
        session(['active_tab' => 'profile']);
        return redirect()->route('consumer.profile.edit')
                         ->with('success', 'Profaili imesasishwa kwa ufanisi.');
    }
    
    /**
     * Update password.
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'current_password' => [
                'required',
                function ($attribute, $value, $fail) use ($user) {
                    if (!Hash::check($value, $user->password_hash)) {
                        $fail('Nywila ya sasa si sahihi.');
                    }
                }
            ],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);
        
        $user->update([
            'password_hash' => Hash::make($validated['password']),
        ]);
        
        session(['active_tab' => 'security']);
        return back()->with('success', 'Nywila imebadilishwa kwa ufanisi.');
    }
    
    /**
     * Verify phone number with OTP.
     */
    public function verifyPhone(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);
        
        $user = Auth::user();
        
        // Tumia method ya verifyForUser kutoka OtpCode model
        $otp = OtpCode::verifyForUser($user->id, $request->otp, 'phone_verification');
        
        if (!$otp) {
            $message = 'OTP si sahihi au imeisha muda.';
            
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ]);
            }
            
            return back()->with('error', $message);
        }
        
        $user->update(['is_phone_verified' => true]);
        
        Log::info('Phone verified', [
            'user_id' => $user->id,
            'phone_number' => $user->phone_number,
        ]);
        
        $message = 'Namba ya simu imethibitishwa!';
        
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        }
        
        return back()->with('success', $message);
    }
    
    /**
     * Resend OTP.
     */
    public function resendOtp(Request $request)
    {
        $user = Auth::user();
        
        // Check if phone is already verified
        if ($user->is_phone_verified) {
            $message = 'Namba yako tayari imethibitishwa.';
            
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ]);
            }
            
            return back()->with('info', $message);
        }
        
        try {
            $otp = app(PhoneVerificationService::class)->issue($user);
        } catch (\Throwable $exception) {
            $message = 'Imeshindikana kutuma OTP kwenye barua pepe yako. Tafadhali jaribu tena.';

            return $request->ajax() || $request->expectsJson()
                ? response()->json(['success' => false, 'message' => $message], 422)
                : back()->with('error', $message);
        }
        
        $message = 'OTP mpya imetumwa kwenye barua pepe yako.';
        
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'expires_at' => $otp->expires_at->timestamp
            ]);
        }
        
        return back()->with('success', $message);
    }
    
    /**
     * Send OTP to user (used when phone number changes).
     */
    private function sendOtp($user)
    {
        app(PhoneVerificationService::class)->issue($user);
    }

    /**
     * Update user preferences.
     */
    public function updatePreferences(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'sms_notifications' => 'boolean',
            'email_notifications' => 'boolean',
            'promotional_notifications' => 'boolean',
        ]);
        
        // Hifadhi mapendeleo kwenye database
        // TODO: Create user_preferences table or add columns to users table
        // Kwa sasa, tumia log
        Log::info('User preferences updated', [
            'user_id' => $user->id,
            'preferences' => $validated
        ]);
        
        // Weka active tab kuwa 'preferences'
        session(['active_tab' => 'preferences']);
        
        $message = 'Mapendeleo yamehifadhiwa kwa ufanisi!';
        
        // Ikiwa ni AJAX request, rudisha JSON
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        }
        
        // Ikiwa ni form submission ya kawaida
        return redirect()->route('consumer.profile.edit')
                         ->with('success', $message);
    }
}
