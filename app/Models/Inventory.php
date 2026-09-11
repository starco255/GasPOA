<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventory extends Model
{
    /**
     * The table associated with the model.
     * Hii inasema table ni 'inventory' (umoja) badala ya 'inventories' (wingi)
     */
    protected $table = 'inventory';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'business_profile_id',
        'product_id',
        'quantity',
        'price_override',
        'is_active',
        'last_updated'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'quantity' => 'integer',
        'price_override' => 'decimal:2',
        'is_active' => 'boolean',
        'last_updated' => 'datetime',
    ];

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * Get the business profile that owns this inventory.
     */
    public function businessProfile(): BelongsTo
    {
        return $this->belongsTo(BusinessProfile::class, 'business_profile_id');
    }

    /**
     * Get the product for this inventory.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Scope a query to only include active inventory.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include items in stock.
     */
    public function scopeInStock($query, $minQuantity = 1)
    {
        return $query->where('quantity', '>=', $minQuantity);
    }

    /**
     * Check if item is in stock.
     */
    public function isInStock($minQuantity = 1): bool
    {
        return $this->quantity >= $minQuantity && $this->is_active;
    }

    /**
     * Get the effective price (override or suggested).
     */
    public function getEffectivePriceAttribute(): float
    {
        if ($this->price_override) {
            return $this->price_override;
        }
        
        return $this->product->suggested_retail_price ?? 0;
    }

    /**
     * Decrease stock quantity.
     */
    public function decreaseStock($amount = 1): bool
    {
        if ($this->quantity >= $amount) {
            $this->quantity -= $amount;
            $this->last_updated = now();
            return $this->save();
        }
        return false;
    }

    /**
     * Increase stock quantity.
     */
    public function increaseStock($amount = 1): bool
    {
        $this->quantity += $amount;
        $this->last_updated = now();
        return $this->save();
    }
}