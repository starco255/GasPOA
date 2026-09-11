<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WholesaleOrder extends Model
{
    protected $fillable = [
        'order_number', 'retailer_id', 'wholesaler_id', 'total_amount',
        'status', 'payment_method', 'payment_status', 'transaction_reference', 'delivery_address', 'created_at', 'delivered_at'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public $timestamps = false;

    public function retailer(): BelongsTo
    {
        return $this->belongsTo(BusinessProfile::class, 'retailer_id');
    }

    public function wholesaler(): BelongsTo
    {
        return $this->belongsTo(BusinessProfile::class, 'wholesaler_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(WholesaleOrderItem::class);
    }
}
