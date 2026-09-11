<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SecurityAuditService;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request): View|RedirectResponse
    {
        if (!$request->session()->has('password_reset_verified_user_id')) {
            return redirect()->route('password.request');
        }

        return view('auth.reset-password');
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::find($request->session()->get('password_reset_verified_user_id'));
        if (!$user) {
            $request->session()->forget('password_reset_verified_user_id');
            return redirect()->route('password.request')->withErrors(['login' => 'Muda wa kubadilisha nywila umeisha. Omba OTP nyingine.']);
        }

        $user->forceFill([
            'password_hash' => Hash::make($request->password),
            'remember_token' => Str::random(60),
        ])->save();
        app(SecurityAuditService::class)->record('password_reset_completed', $user, [], 'warning');
        event(new PasswordReset($user));

        $request->session()->forget('password_reset_verified_user_id');

        return redirect()->route('login')->with('status', 'Nywila yako imebadilishwa kwa mafanikio.');
    }
}
