<?php

namespace App\Services;

use App\Models\SecurityAuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SecurityAuditService
{
    /** Record a security-relevant event without ever storing a password or OTP. */
    public function record(
        string $eventType,
        ?User $subject = null,
        array $context = [],
        string $severity = 'info',
        ?string $description = null,
        ?User $actor = null,
    ): void {
        try {
            $request = app()->bound('request') ? app(Request::class) : null;
            $actorId = $actor?->id ?? auth()->id();

            SecurityAuditLog::create([
                'actor_user_id' => $actorId,
                'subject_user_id' => $subject?->id,
                'event_type' => $eventType,
                'severity' => $severity,
                'description' => $description ?? $this->descriptionFor($eventType, $subject),
                'ip_address' => $request?->ip(),
                'user_agent' => $request?->userAgent(),
                'context' => $context ?: null,
                'occurred_at' => now(),
            ]);
        } catch (\Throwable $exception) {
            // Audit persistence must never prevent a legitimate account recovery.
            Log::error('Unable to write security audit log.', [
                'event_type' => $eventType,
                'subject_user_id' => $subject?->id,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    public function maskPhone(?string $phone): ?string
    {
        if (blank($phone)) {
            return null;
        }

        return str_repeat('•', max(0, strlen($phone) - 4)) . substr($phone, -4);
    }

    public function maskIdentifier(?string $identifier): ?string
    {
        if (blank($identifier)) {
            return null;
        }

        if (str_contains($identifier, '@')) {
            [$local, $domain] = explode('@', $identifier, 2);
            return substr($local, 0, 1) . '***@' . $domain;
        }

        return $this->maskPhone($identifier);
    }

    private function descriptionFor(string $eventType, ?User $subject): string
    {
        $name = $subject?->full_name ?? 'Akaunti isiyojulikana';

        return match ($eventType) {
            'account_registered' => "Akaunti mpya imesajiliwa: {$name}",
            'account_activated' => "Akaunti imefunguliwa: {$name}",
            'account_deactivated' => "Akaunti imefungwa: {$name}",
            'account_deleted' => "Akaunti imeondolewa na msimamizi: {$name}",
            'login_success' => "Kuingia kumefaulu: {$name}",
            'login_failed' => 'Jaribio la kuingia limeshindwa',
            'login_rate_limited' => 'Majaribio mengi ya kuingia yamezuiwa',
            'password_changed' => "Nywila imebadilishwa: {$name}",
            'password_reset_requested' => "Ombi la kurejesha nywila: {$name}",
            'password_reset_otp_verified' => "OTP ya kurejesha nywila imethibitishwa: {$name}",
            'password_reset_completed' => "Nywila imerejeshwa: {$name}",
            'password_reset_otp_failed' => 'OTP ya kurejesha nywila si sahihi au imeisha',
            'phone_number_changed' => "Namba ya simu imebadilishwa: {$name}",
            'phone_verification_otp_issued' => "OTP ya uthibitishaji wa simu imetumwa: {$name}",
            'phone_verified' => "Namba ya simu imethibitishwa: {$name}",
            'phone_verification_failed' => "Uthibitishaji wa namba ya simu umeshindwa: {$name}",
            default => "Tukio la usalama: {$eventType}",
        };
    }
}
