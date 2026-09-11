<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use App\Services\SecurityAuditService;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'login' => ['required', 'string'], // Inakubali namba ya simu au barua pepe
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // Pata thamani ya login (inaweza kuwa namba ya simu au barua pepe)
        $login = $this->input('login');
        
        // Tambua aina ya field: kama ni email, tumia 'email', la sivyo tumia 'phone_number'
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone_number';

        // Jaribu kuingia. Kigezo cha pili cha Auth::attempt() ni 'remember' (kutoka checkbox)
        // Laravel itatumia getAuthPassword() kwenye User model kupata password_hash
        if (! Auth::attempt(
            [$field => $login, 'password' => $this->input('password')],
            $this->boolean('remember')
        )) {
            $subject = User::where($field, $login)->first();
            $audit = app(SecurityAuditService::class);
            $audit->record('login_failed', $subject, [
                'login' => $audit->maskIdentifier($login),
                'attempts_remaining' => max(0, 4 - RateLimiter::attempts($this->throttleKey())),
            ], 'warning');

            // Ongeza kiwango cha majaribio yaliyoshindikana
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'login' => trans('auth.failed'), // Ujumbe: "Taarifa za kuingia si sahihi."
            ]);
        }

        // Safisha kiwango cha majaribio baada ya kufanikiwa
        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $login = $this->input('login');
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone_number';
        $subject = User::where($field, $login)->first();
        $audit = app(SecurityAuditService::class);
        $audit->record('login_rate_limited', $subject, [
            'login' => $audit->maskIdentifier($login),
            'retry_after_seconds' => RateLimiter::availableIn($this->throttleKey()),
        ], 'danger');

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'login' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        // Tumia login na IP address kufuatilia majaribio
        return Str::transliterate(Str::lower($this->input('login')) . '|' . $this->ip());
    }
}
