<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetOtpMail;
use App\Models\OtpCode;
use App\Models\User;
use App\Services\SecurityAuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    private const OTP_PURPOSE = 'password_reset_email';
    private const OTP_EXPIRY_MINUTES = 5;
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /** Send a six-digit password-reset OTP to the account email address. */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'login' => ['required', 'string'],
        ]);

        $login = trim($request->input('login'));
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone_number';
        if ($field === 'phone_number') {
            $digits = preg_replace('/\D/', '', $login);
            $login = str_starts_with($digits, '0')
                ? '+255' . substr($digits, 1)
                : (str_starts_with($digits, '255') ? '+' . $digits : '+255' . $digits);
        }
        $user = User::where($field, $login)->first();

        if (!$user) {
            return back()->withErrors([
                'login' => __('Hatupati mtumiaji na taarifa hizo.'),
            ])->onlyInput('login');
        }

        if (!filled($user->email)) {
            return back()->withErrors([
                'login' => 'Akaunti hii haina barua pepe. Weka email kwenye profile au wasiliana na msaada wa GasPOA.',
            ])->onlyInput('login');
        }

        try {
            $this->issueOtp($user);
        } catch (\Throwable $exception) {
            return back()->withErrors([
                'login' => 'Imeshindikana kutuma OTP kwa sasa. Tafadhali jaribu tena baadaye.',
            ])->onlyInput('login');
        }

        app(SecurityAuditService::class)->record('password_reset_requested', $user, [
            'delivery' => 'email',
        ], 'warning');

        session(['password_reset_otp_user_id' => $user->id]);

        return redirect()->route('password.otp.notice')
            ->with('status', 'Tumetuma OTP ya tarakimu 6 kwenye barua pepe yako. Inaisha baada ya dakika 5.');
    }

    /** Re-send an OTP for the reset request currently stored in the session. */
    public function resendOtp(Request $request): RedirectResponse
    {
        $user = User::find($request->session()->get('password_reset_otp_user_id'));

        if (!$user || blank($user->email)) {
            $request->session()->forget('password_reset_otp_user_id');

            return redirect()->route('password.request')
                ->withErrors(['login' => 'Kikao cha kurejesha nywila kimeisha. Tafadhali omba OTP nyingine.']);
        }

        try {
            $this->issueOtp($user);
        } catch (\Throwable $exception) {
            return back()->withErrors([
                'otp' => 'Imeshindikana kutuma OTP mpya kwa sasa. Tafadhali jaribu tena baadaye.',
            ]);
        }

        app(SecurityAuditService::class)->record('password_reset_requested', $user, [
            'delivery' => 'email',
            'resent' => true,
        ], 'warning');

        return redirect()->route('password.otp.notice')
            ->with('status', 'OTP mpya imetumwa kwenye barua pepe yako. Inaisha baada ya dakika 5.');
    }

    /**
     * Save the active hashed token and the OTP audit entry before emailing it.
     * password_reset_tokens has one row per email by design; otp_codes preserves
     * the chronological history of every issued reset code.
     */
    private function issueOtp(User $user): OtpCode
    {
        $otp = OtpCode::generateForUser($user->id, self::OTP_PURPOSE, self::OTP_EXPIRY_MINUTES);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => Hash::make($otp->code),
                'created_at' => now(),
            ]
        );

        try {
            Mail::to($user->email)->send(new PasswordResetOtpMail($user, $otp->code));
        } catch (\Throwable $exception) {
            Log::error('Password-reset OTP email could not be sent.', [
                'user_id' => $user->id,
                'error' => $exception->getMessage(),
            ]);

            throw new \RuntimeException('Password-reset OTP email could not be sent.', previous: $exception);
        }

        return $otp;
    }

    public function showOtp(Request $request): View|RedirectResponse
    {
        $user = User::find($request->session()->get('password_reset_otp_user_id'));

        if (!$user) {
            return redirect()->route('password.request');
        }

        $otp = OtpCode::forUser($user->id)
            ->forPurpose(self::OTP_PURPOSE)
            ->valid()
            ->latest('id')
            ->first();

        return view('auth.verify-password-reset-otp', [
            'expiresAt' => $otp?->expires_at?->timestamp,
        ]);
    }

    public function verifyOtp(Request $request): RedirectResponse
    {
        $request->validate(['otp' => ['required', 'digits:6']]);

        $userId = $request->session()->get('password_reset_otp_user_id');
        $user = $userId ? User::find($userId) : null;
        $resetToken = $user ? DB::table('password_reset_tokens')
            ->where('email', $user->email)
            ->where('created_at', '>', now()->subMinutes(self::OTP_EXPIRY_MINUTES))
            ->first() : null;
        $otp = $user ? OtpCode::forUser($user->id)
            ->forPurpose(self::OTP_PURPOSE)
            ->valid()
            ->where('code', $request->otp)
            ->latest('id')
            ->first() : null;

        if (!$otp || !$resetToken || !Hash::check($request->otp, $resetToken->token)) {
            app(SecurityAuditService::class)->record('password_reset_otp_failed', $user, [], 'warning');
            return back()->withErrors(['otp' => 'OTP si sahihi au imeisha muda. Omba OTP mpya.']);
        }

        $otp->markAsUsed();
        $request->session()->forget('password_reset_otp_user_id');
        $request->session()->put('password_reset_verified_user_id', $userId);
        app(SecurityAuditService::class)->record('password_reset_otp_verified', $user, [], 'warning');

        return redirect()->route('password.reset');
    }
}
