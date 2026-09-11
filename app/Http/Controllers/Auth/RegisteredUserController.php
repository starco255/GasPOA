<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\WelcomeMail;
use App\Models\User;
use App\Services\PhoneVerificationService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Str;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        // Custom validation for phone number
        $request->validate([
            'full_name'     => ['required', 'string', 'max:255'],
            'phone_number'  => [
                'required', 
                'string', 
                'max:12',
                'regex:/^[1-9][0-9]{8,11}$/',
                'unique:users,phone_number'
            ],
            'email'         => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'user_type'     => ['required', 'string', 'in:consumer,retailer,wholesaler'],
            'password'      => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'phone_number.regex' => 'Tafadhali weka namba ya simu BILA "0" mwanzoni. Mfano: 615004300',
        ]);

        // Safisha namba ya simu na ongeza +255
        $phone = $request->phone_number;
        $phone = preg_replace('/\D/', '', $phone);
        if (str_starts_with($phone, '0')) {
            $phone = substr($phone, 1);
        }
        $fullPhoneNumber = '+255' . $phone;

        // The form has no country prefix, while the database stores +255.
        // Validate against the stored form to prevent a database exception page.
        if (User::where('phone_number', $fullPhoneNumber)->exists()) {
            return back()->withErrors([
                'phone_number' => 'Namba hii ya simu tayari imesajiliwa. Tafadhali tumia namba nyingine au ingia kwenye akaunti yako.',
            ])->withInput();
        }

        // Generate UUID
        $uuid = $request->uuid ?: (string) Str::uuid();
        while (User::where('uuid', $uuid)->exists()) {
            $uuid = (string) Str::uuid();
        }

        // Create user
        try {
            $user = User::create([
                'uuid'               => $uuid,
                'full_name'          => $request->full_name,
                'phone_number'       => $fullPhoneNumber,
                'email'              => $request->email,
                'user_type'          => $request->user_type,
                'password_hash'      => Hash::make($request->password),
                'is_phone_verified'  => false, // Bado hajathibitisha
                'is_active'          => true,
            ]);
        } catch (QueryException $exception) {
            // Covers the small race window between the duplicate check above
            // and the database insert.
            if ((string) $exception->getCode() === '23000'
                && str_contains($exception->getMessage(), 'phone_number')) {
                return back()->withErrors([
                    'phone_number' => 'Namba hii ya simu tayari imesajiliwa. Tafadhali tumia namba nyingine au ingia kwenye akaunti yako.',
                ])->withInput();
            }

            throw $exception;
        }

        event(new Registered($user));

        if (filled($user->email)) {
            try {
                Mail::to($user->email)->send(new WelcomeMail($user));
            } catch (\Throwable $exception) {
                // Usizuie usajili kwa sababu ya tatizo la muda la SMTP.
                Log::error('Welcome email could not be sent.', [
                    'user_id' => $user->id,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        // Log in the user
        Auth::login($user);

        // Generate and send OTP
        try {
            app(PhoneVerificationService::class)->issue($user);
        } catch (\Throwable $exception) {
            Log::error('Registration phone-verification OTP email could not be sent.', [
                'user_id' => $user->id,
                'error' => $exception->getMessage(),
            ]);

            return redirect()->route('verification.phone.notice')
                ->with('error', 'Akaunti imeundwa, lakini imeshindikana kutuma OTP kwenye barua pepe yako. Tafadhali bonyeza “Tuma OTP Mpya”.');
        }

        // Redirect to OTP verification page
        return redirect()->route('verification.phone.notice')
                         ->with('success', 'Akaunti imeundwa! Tafadhali thibitisha namba yako ya simu.');
    }

}
