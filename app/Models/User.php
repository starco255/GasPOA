<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use App\Notifications\SmsPasswordResetNotification;
use App\Services\SecurityAuditService;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'uuid',
        'full_name',
        'phone_number',
        'email',
        'user_type',
        'password_hash',
        'is_phone_verified',
        'is_active',
        'remember_token',
        'interface_language',
        'interface_theme',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_phone_verified' => 'boolean',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($user) {
            // Generate UUID if not provided
            if (!$user->uuid) {
                $user->uuid = (string) Str::uuid();
            }
            
            // Set default values
            if (!isset($user->is_active)) {
                $user->is_active = true;
            }
            
            if (!isset($user->is_phone_verified)) {
                $user->is_phone_verified = false;
            }
            
            // Set created_at if not set
            if (!$user->created_at) {
                $user->created_at = now();
            }
            
            if (!$user->updated_at) {
                $user->updated_at = now();
            }
        });
        
        // 🔥 IMEONDOSHWA - static::updating inaweza kusababisha conflicts
        // static::updating(function ($user) {
        //     $user->updated_at = now();
        // });
        
        // 🔥 IMEONGEZWA - saving event (covers both creating and updating)
        static::saving(function ($user) {
            if (!$user->created_at) {
                $user->created_at = now();
            }
            $user->updated_at = now();
        });

        static::created(function ($user) {
            app(SecurityAuditService::class)->record('account_registered', $user);
        });

        static::updated(function ($user) {
            $audit = app(SecurityAuditService::class);

            if ($user->wasChanged('phone_number')) {
                $audit->record('phone_number_changed', $user, [
                    'old_phone' => $audit->maskPhone($user->getOriginal('phone_number')),
                    'new_phone' => $audit->maskPhone($user->phone_number),
                    'verification_required' => !$user->is_phone_verified,
                ], 'warning');
            }

            if ($user->wasChanged('is_phone_verified') && $user->is_phone_verified) {
                $audit->record('phone_verified', $user, [
                    'phone' => $audit->maskPhone($user->phone_number),
                ]);
            }

            if ($user->wasChanged('password_hash')) {
                $audit->record('password_changed', $user, [], 'warning');
            }

            if ($user->wasChanged('is_active')) {
                $audit->record(
                    $user->is_active ? 'account_activated' : 'account_deactivated',
                    $user,
                    [],
                    $user->is_active ? 'info' : 'warning'
                );
            }
        });
    }

    /**
     * Tell Laravel to use password_hash column for authentication.
     */
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    /**
     * Get the user's full name or fallback to phone number.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->full_name ?: ($this->phone_number ?? 'Mtumiaji');
    }

    /**
     * Check if user is a consumer.
     */
    public function isConsumer(): bool
    {
        return $this->user_type === 'consumer';
    }

    /**
     * Check if user is a retailer.
     */
    public function isRetailer(): bool
    {
        return $this->user_type === 'retailer';
    }

    /**
     * Check if user is a wholesaler.
     */
    public function isWholesaler(): bool
    {
        return $this->user_type === 'wholesaler';
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->user_type === 'admin';
    }

    /**
     * Check if user is a business user (retailer or wholesaler).
     */
    public function isBusinessUser(): bool
    {
        return in_array($this->user_type, ['retailer', 'wholesaler']);
    }

    /**
     * Check if user can place orders.
     */
    public function canPlaceOrders(): bool
    {
        return $this->is_active && $this->is_phone_verified;
    }

    /**
     * Activate the user account.
     */
    public function activate(): bool
    {
        $this->is_active = true;
        return $this->save();
    }

    /**
     * Deactivate the user account.
     */
    public function deactivate(): bool
    {
        $this->is_active = false;
        return $this->save();
    }

    /**
     * Mark phone as verified.
     */
    public function verifyPhone(): bool
    {
        $this->is_phone_verified = true;
        return $this->save();
    }

    /**
     * Get the business profile associated with the user.
     */
    public function businessProfile()
    {
        return $this->hasOne(BusinessProfile::class);
    }

    /**
     * Get the retail orders placed by this user (as consumer).
     */
    public function retailOrders()
    {
        return $this->hasMany(RetailOrder::class, 'consumer_id');
    }

    /**
     * Get messages sent by this user.
     */
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Get messages received by this user.
     */
    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    /**
     * Get unread messages count for this user.
     */
    public function getUnreadMessagesCountAttribute(): int
    {
        return $this->receivedMessages()->where('is_read', false)->count();
    }

    /**
     * Send the password reset notification.
     */
    public function sendPasswordResetNotification($token)
    {
        if ($this->phone_number) {
            $this->notify(new SmsPasswordResetNotification($token));
        } else {
            $this->notify(new \Illuminate\Auth\Notifications\ResetPassword($token));
        }
    }

    /**
     * Route notifications for the SMS channel.
     */
    public function routeNotificationForSms()
    {
        return $this->phone_number;
    }
}
