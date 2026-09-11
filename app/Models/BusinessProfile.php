<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BusinessProfile extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'business_profiles';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id', 
        'business_name', 
        'tinn_number', 
        'business_type',
        'shop_latitude', 
        'shop_longitude', 
        'physical_address', 
        'is_open',
        'service_radius_km', 
        'can_deliver', 
        'delivery_fee_per_km',
        // Payment methods - IMEPONGEWA
        'accept_cash',
        'accept_mobile_money',
        'accept_bank',
        'accept_mpesa',
        'accept_tigopesa',
        'accept_airtelmoney',
        'accept_halopesa',
        // Mobile money numbers - IMEPONGEWA
        'mpesa_number',
        'halopesa_number',
        'airtel_number',
        'mixx_number',
        // Bank details - IMEPONGEWA
        'bank_name',
        'bank_account_number',
        'bank_account_name',
        // Notification preferences - IMEPONGEWA
        'notify_new_order',
        'notify_chat',
        // Phone number for business - IMEPONGEWA
        'phone_number',
        'created_at'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_open' => 'boolean',
        'can_deliver' => 'boolean',
        'accept_cash' => 'boolean',           // Imeongezwa
        'accept_mobile_money' => 'boolean',   // Imeongezwa
        'accept_bank' => 'boolean',           // Imeongezwa
        'accept_mpesa' => 'boolean',
        'accept_tigopesa' => 'boolean',
        'accept_airtelmoney' => 'boolean',
        'accept_halopesa' => 'boolean',
        'notify_new_order' => 'boolean',      // Imeongezwa
        'notify_chat' => 'boolean',           // Imeongezwa
        'shop_latitude' => 'decimal:8',
        'shop_longitude' => 'decimal:8',
        'delivery_fee_per_km' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * Get the user that owns the business profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the inventory items for this business profile.
     */
    public function inventory(): HasMany
    {
        return $this->hasMany(Inventory::class, 'business_profile_id');
    }

    /**
     * Get the retail orders where this business is the retailer.
     */
    public function retailOrders(): HasMany
    {
        return $this->hasMany(RetailOrder::class, 'retailer_id');
    }

    /**
     * Get the wholesale orders where this business is the retailer (buyer).
     */
    public function wholesaleOrdersAsRetailer(): HasMany
    {
        return $this->hasMany(WholesaleOrder::class, 'retailer_id');
    }

    /**
     * Get the wholesale orders where this business is the wholesaler (seller).
     */
    public function wholesaleOrdersAsWholesaler(): HasMany
    {
        return $this->hasMany(WholesaleOrder::class, 'wholesaler_id');
    }

    /**
     * Get all wholesale orders associated with this business profile.
     */
    public function getAllWholesaleOrders()
    {
        return WholesaleOrder::where('retailer_id', $this->id)
            ->orWhere('wholesaler_id', $this->id);
    }

    /**
     * Scope a query to only include open businesses.
     */
    public function scopeIsOpen($query)
    {
        return $query->where('is_open', true);
    }

    /**
     * Scope a query to only include businesses that can deliver.
     */
    public function scopeCanDeliver($query)
    {
        return $query->where('can_deliver', true);
    }

    /**
     * Scope a query to only include retailers.
     */
    public function scopeRetailers($query)
    {
        return $query->where('business_type', 'retailer');
    }

    /**
     * Scope a query to only include wholesalers.
     */
    public function scopeWholesalers($query)
    {
        return $query->where('business_type', 'wholesaler');
    }

    /**
     * Get businesses within a certain radius of a given location.
     */
    public static function withinRadius($latitude, $longitude, $radiusKm = 10)
    {
        $haversine = "(6371 * acos(cos(radians(?)) * cos(radians(shop_latitude)) 
                     * cos(radians(shop_longitude) - radians(?)) 
                     + sin(radians(?)) * sin(radians(shop_latitude))))";
        
        return self::select('*')
            ->selectRaw("{$haversine} AS distance", [$latitude, $longitude, $latitude])
            ->having('distance', '<', $radiusKm)
            ->orderBy('distance');
    }

    /**
     * Check if business is active and can receive orders.
     */
    public function isAvailable(): bool
    {
        return $this->is_open && $this->user && $this->user->is_active;
    }

    /**
     * Get the full address as a string.
     */
    public function getFullAddressAttribute(): string
    {
        return $this->physical_address;
    }

    /**
     * Get the coordinates as an array.
     */
    public function getCoordinatesAttribute(): array
    {
        return [
            'lat' => (float) $this->shop_latitude,
            'lng' => (float) $this->shop_longitude,
        ];
    }

    /**
     * Check if business accepts cash payments.
     */
    public function acceptsCash(): bool
    {
        return $this->accept_cash ?? true;
    }

    /**
     * Check if business accepts mobile money payments.
     */
    public function acceptsMobileMoney(): bool
    {
        return $this->accept_mobile_money ?? true;
    }

    /**
     * Check if business accepts bank transfers.
     */
    public function acceptsBank(): bool
    {
        return $this->accept_bank ?? false;
    }

    /**
     * Get all mobile money numbers as an array.
     */
    public function getMobileMoneyNumbersAttribute(): array
    {
        $numbers = [];
        if ($this->mpesa_number) $numbers['M-Pesa'] = $this->mpesa_number;
        if ($this->halopesa_number) $numbers['HaloPesa'] = $this->halopesa_number;
        if ($this->airtel_number) $numbers['Airtel Money'] = $this->airtel_number;
        if ($this->mixx_number) $numbers['Mixx by Yas'] = $this->mixx_number;
        return $numbers;
    }

    /**
     * Get bank details as an array.
     */
    public function getBankDetailsAttribute(): array
    {
        return [
            'bank_name' => $this->bank_name,
            'account_number' => $this->bank_account_number,
            'account_name' => $this->bank_account_name,
        ];
    }

    /**
     * Check if business has a specific product in stock.
     */
    public function hasProductInStock($productId, $minQuantity = 1): bool
    {
        return $this->inventory()
            ->where('product_id', $productId)
            ->where('is_active', true)
            ->where('quantity', '>=', $minQuantity)
            ->exists();
    }

    /**
     * Get stock quantity for a specific product.
     */
    public function getStockQuantity($productId): int
    {
        $inventory = $this->inventory()
            ->where('product_id', $productId)
            ->where('is_active', true)
            ->first();
            
        return $inventory ? $inventory->quantity : 0;
    }

    /**
     * Calculate delivery fee to a given location.
     */
    public function calculateDeliveryFee($destinationLat, $destinationLng): float
    {
        if (!$this->can_deliver) {
            return 0;
        }

        $earthRadius = 6371; // km
        
        $latFrom = deg2rad($this->shop_latitude);
        $lonFrom = deg2rad($this->shop_longitude);
        $latTo = deg2rad($destinationLat);
        $lonTo = deg2rad($destinationLng);
        
        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;
        
        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        
        $distance = $angle * $earthRadius;
        
        return $distance * $this->delivery_fee_per_km;
    }

    /**
     * Get pending retail orders count.
     */
    public function getPendingOrdersCountAttribute(): int
    {
        return $this->retailOrders()
            ->whereIn('status', ['pending', 'accepted'])
            ->count();
    }

    /**
     * Get today's total sales.
     */
    public function getTodaySalesAttribute(): float
    {
        return $this->retailOrders()
            ->whereDate('created_at', today())
            ->where('payment_status', 'paid')
            ->sum('total_amount');
    }

    /**
     * Get this month's total sales.
     */
    public function getThisMonthSalesAttribute(): float
    {
        return $this->retailOrders()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('payment_status', 'paid')
            ->sum('total_amount');
    }

    /**
     * Get low stock items (quantity less than threshold).
     */
    public function getLowStockItems($threshold = 5)
    {
        return $this->inventory()
            ->with('product')
            ->where('is_active', true)
            ->where('quantity', '<', $threshold)
            ->get();
    }

    /**
     * Get out of stock items.
     */
    public function getOutOfStockItems()
    {
        return $this->inventory()
            ->with('product')
            ->where('is_active', true)
            ->where('quantity', '<=', 0)
            ->get();
    }

    /**
     * Get total inventory value.
     */
    public function getTotalInventoryValueAttribute(): float
    {
        return $this->inventory()
            ->where('is_active', true)
            ->get()
            ->sum(function ($item) {
                $price = $item->price_override ?? $item->product->suggested_wholesale_price ?? 0;
                return $price * $item->quantity;
            });
    }

    /**
     * Get average rating from completed orders.
     */
    public function getAverageRatingAttribute(): float
    {
        return 4.5; // Placeholder
    }

    /**
     * Get completed orders count.
     */
    public function getCompletedOrdersCountAttribute(): int
    {
        return $this->retailOrders()
            ->where('status', 'delivered')
            ->count();
    }

    /**
     * Should notify on new order.
     */
    public function shouldNotifyNewOrder(): bool
    {
        return $this->notify_new_order ?? true;
    }

    /**
     * Should notify on new chat message.
     */
    public function shouldNotifyChat(): bool
    {
        return $this->notify_chat ?? true;
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (!$model->created_at) {
                $model->created_at = now();
            }
            // Set default values
            if (!isset($model->is_open)) {
                $model->is_open = true;
            }
            if (!isset($model->can_deliver)) {
                $model->can_deliver = false;
            }
            if (!isset($model->service_radius_km)) {
                $model->service_radius_km = 5;
            }
            if (!isset($model->delivery_fee_per_km)) {
                $model->delivery_fee_per_km = 0;
            }
            // Set default payment method preferences
            if (!isset($model->accept_cash)) {
                $model->accept_cash = true;
            }
            if (!isset($model->accept_mobile_money)) {
                $model->accept_mobile_money = true;
            }
            if (!isset($model->accept_bank)) {
                $model->accept_bank = false;
            }
            // Set default notification preferences
            if (!isset($model->notify_new_order)) {
                $model->notify_new_order = true;
            }
            if (!isset($model->notify_chat)) {
                $model->notify_chat = true;
            }
        });
    }
}
