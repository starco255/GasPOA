<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistancePricing extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'distance_pricing';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'from_km',
        'to_km',
        'delivery_fee',
        'currency'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'from_km' => 'decimal:2',
        'to_km' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
    ];

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * Scope a query to find pricing for a specific distance.
     */
    public function scopeForDistance($query, $distance)
    {
        return $query->where('from_km', '<=', $distance)
                     ->where('to_km', '>=', $distance);
    }

    /**
     * Get formatted delivery fee.
     */
    public function getFormattedFeeAttribute(): string
    {
        return number_format($this->delivery_fee) . ' ' . $this->currency;
    }

    /**
     * Get the range as a string.
     */
    public function getRangeAttribute(): string
    {
        return $this->from_km . ' - ' . $this->to_km . ' km';
    }

    /**
     * Check if a distance falls within this pricing range.
     */
    public function containsDistance($distance): bool
    {
        return $distance >= $this->from_km && $distance <= $this->to_km;
    }

    /**
     * Get the delivery fee for a given distance.
     */
    public static function getFeeForDistance($distance): float
    {
        $pricing = self::forDistance($distance)->first();
        
        if ($pricing) {
            return $pricing->delivery_fee;
        }
        
        // If no exact match, find the closest range
        $maxPricing = self::orderBy('to_km', 'desc')->first();
        if ($maxPricing && $distance > $maxPricing->to_km) {
            // Calculate extra fee for distance beyond max range
            $extraKm = $distance - $maxPricing->to_km;
            return $maxPricing->delivery_fee + ($extraKm * 1000);
        }
        
        // Default fee
        return 5000;
    }
}