<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes; // Kwa ajili ya soft deletes (deleted_at)

    /**
     * The table associated with the model.
     * Hii inahakikisha Laravel inatafuta 'products' na sio jina lingine.
     */
    protected $table = 'products';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'category_id', 
        'name', 
        'description', 
        'service_type',
        'suggested_retail_price', 
        'suggested_wholesale_price',
        'weight_kg', 
        'image_url',
        'is_active'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_active' => 'boolean',
        'suggested_retail_price' => 'decimal:2',
        'suggested_wholesale_price' => 'decimal:2',
        'weight_kg' => 'integer',
        'deleted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the category that owns the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the inventory items for this product.
     * IMEREKEBISHWA: Taja foreign key kwa usahihi
     */
    public function inventory(): HasMany
    {
        return $this->hasMany(Inventory::class, 'product_id');
    }

    /**
     * Get the retail order items for this product.
     */
    public function retailOrderItems(): HasMany
    {
        return $this->hasMany(RetailOrderItem::class, 'product_id');
    }

    /**
     * Get the wholesale order items for this product.
     */
    public function wholesaleOrderItems(): HasMany
    {
        return $this->hasMany(WholesaleOrderItem::class, 'product_id');
    }

    /**
     * Scope a query to only include active products.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive products.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope a query to only include new cylinders.
     */
    public function scopeNewCylinders($query)
    {
        return $query->where('service_type', 'new_cylinder');
    }

    /**
     * Scope a query to only include refill products.
     */
    public function scopeRefillExchange($query)
    {
        return $query->where('service_type', 'refill_exchange');
    }

    /**
     * Scope a query to only include products with stock.
     */
    public function scopeInStock($query, $businessProfileId = null)
    {
        return $query->whereHas('inventory', function ($q) use ($businessProfileId) {
            $q->where('quantity', '>', 0)
              ->where('is_active', true);
            if ($businessProfileId) {
                $q->where('business_profile_id', $businessProfileId);
            }
        });
    }

    /**
     * Activate the product.
     */
    public function activate(): bool
    {
        $this->is_active = true;
        return $this->save();
    }

    /**
     * Deactivate the product.
     */
    public function deactivate(): bool
    {
        $this->is_active = false;
        return $this->save();
    }

    /**
     * Check if product is available for order.
     */
    public function isAvailable(): bool
    {
        return $this->is_active && !$this->trashed();
    }

    /**
     * Get the formatted price for display.
     */
    public function getFormattedRetailPriceAttribute(): string
    {
        return 'TZS ' . number_format($this->suggested_retail_price);
    }

    /**
     * Get the formatted wholesale price for display.
     */
    public function getFormattedWholesalePriceAttribute(): string
    {
        return 'TZS ' . number_format($this->suggested_wholesale_price);
    }

    /**
     * Get the price based on service type.
     */
    public function getPriceForServiceType($serviceType): float
    {
        return $serviceType === 'new_cylinder' 
            ? $this->suggested_retail_price 
            : $this->suggested_wholesale_price;
    }

    /**
     * Get total stock across all retailers.
     */
    public function getTotalStockAttribute(): int
    {
        return $this->inventory()
            ->where('is_active', true)
            ->sum('quantity');
    }

    /**
     * Get number of retailers stocking this product.
     */
    public function getRetailersCountAttribute(): int
    {
        return $this->inventory()
            ->where('is_active', true)
            ->where('quantity', '>', 0)
            ->distinct('business_profile_id')
            ->count('business_profile_id');
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
            // Hakikisha is_active ina thamani default
            if (!isset($model->is_active)) {
                $model->is_active = true;
            }
        });
    }
}