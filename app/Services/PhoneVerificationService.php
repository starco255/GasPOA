<?php

namespace App\Services;

use App\Mail\PhoneVerificationOtpMail;
use App\Models\OtpCode;
use App\Models\User;
use App\Services\SecurityAuditService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PhoneVerificationService
{
    /** Create a fresh phone-verification OTP and deliver it to the account email. */
    public function issue(User $user): OtpCode
    {
        if (blank($user->email)) {
            Log::warning('Phone-verification OTP was not emailed because the user has no email address.', [
                'user_id' => $user->id,
            ]);

            throw new \RuntimeException('A phone-verification OTP requires an account email address.');
        }

        // OTP zote za uthibitishaji wa simu zina muda wa dakika tano.
        // generateForUser huhifadhi rekodi ya zamani kama imetumika kwa audit trail.
        $otp = OtpCode::generateForUser($user->id, 'phone_verification', 5);

        try {
            Mail::to($user->email)->send(new PhoneVerificationOtpMail($user, $otp->code));
        } catch (\Throwable $exception) {
            Log::error('Phone-verification OTP email could not be sent.', [
                'user_id' => $user->id,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }

        app(SecurityAuditService::class)->record('phone_verification_otp_issued', $user, [
            'delivery' => 'email',
            'expires_at' => $otp->expires_at->toIso8601String(),
        ]);

        return $otp;
    }
}
