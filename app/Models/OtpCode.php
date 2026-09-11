<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OtpCode extends Model
{
    protected $fillable = [
        'user_id',
        'code',
        'purpose',
        'is_used',
        'expires_at',
    ];

    protected $casts = [
        'is_used' => 'boolean',
        'expires_at' => 'datetime',
    ];

    /**
     * Indicates if the model should be timestamped.
     * Weka true ili iweze kutumia created_at na updated_at
     * Hii itasaidia kujua OTP ilitumwa lini
     */
    public $timestamps = true;

    /**
     * Get the user that owns the OTP code.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include unused and non-expired OTPs.
     */
    public function scopeValid($query)
    {
        return $query->where('is_used', false)
                     ->where('expires_at', '>', now());
    }

    /**
     * Scope a query to filter by purpose.
     */
    public function scopeForPurpose($query, $purpose)
    {
        return $query->where('purpose', $purpose);
    }

    /**
     * Scope a query to filter by user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Check if OTP is valid (not used and not expired).
     */
    public function isValid(): bool
    {
        return !$this->is_used && $this->expires_at->isFuture();
    }

    /**
     * Check if OTP has expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Mark OTP as used.
     */
    public function markAsUsed(): bool
    {
        $this->is_used = true;
        return $this->save();
    }

    /**
     * Get the time remaining before OTP expires (in minutes).
     */
    public function getRemainingMinutesAttribute(): int
    {
        if ($this->isExpired()) {
            return 0;
        }
        
        return max(0, now()->diffInMinutes($this->expires_at));
    }

    /**
     * Get the time remaining before OTP expires (formatted).
     */
    public function getRemainingTimeAttribute(): string
    {
        $minutes = $this->remaining_minutes;
        
        if ($minutes <= 0) {
            return 'Imekwisha muda';
        }
        
        if ($minutes >= 1) {
            return "Dakika {$minutes}";
        }
        
        $seconds = max(0, now()->diffInSeconds($this->expires_at));
        return "Sekunde {$seconds}";
    }

    /**
     * Generate a new OTP for a user.
     */
    public static function generateForUser($userId, $purpose = 'phone_verification', $expiryMinutes = 5): self
    {
        // Keep the OTP audit trail. A newly issued code makes any earlier
        // unused code invalid, but does not remove its database record.
        self::where('user_id', $userId)
            ->where('purpose', $purpose)
            ->where('is_used', false)
            ->update([
                'is_used' => true,
                'updated_at' => now(),
            ]);
        
        // Generate 6-digit OTP
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Create new OTP
        return self::create([
            'user_id' => $userId,
            'code' => $code,
            'purpose' => $purpose,
            'is_used' => false,
            'expires_at' => now()->addMinutes($expiryMinutes),
        ]);
    }

    /**
     * Verify an OTP for a user.
     */
    public static function verifyForUser($userId, $code, $purpose = 'phone_verification'): ?self
    {
        $otp = self::where('user_id', $userId)
            ->where('code', $code)
            ->where('purpose', $purpose)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->first();
        
        if ($otp) {
            $otp->markAsUsed();
        }
        
        return $otp;
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($otp) {
            // Ensure expires_at is set
            if (!$otp->expires_at) {
                $otp->expires_at = now()->addMinutes(5);
            }
            
            // Ensure is_used defaults to false
            if (!isset($otp->is_used)) {
                $otp->is_used = false;
            }
        });
    }
}
