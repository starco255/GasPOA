<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RetailOrder extends Model
{
    protected $fillable = [
        'order_number', 'consumer_id', 'retailer_id', 'delivery_address',
        'delivery_latitude', 'delivery_longitude', 'status', 'urgency_level',
        'total_amount', 'payment_method', 'payment_method_provider', 'payment_status', 'transaction_reference',
        'assigned_at', 'delivered_at', 'created_at'
    ];

    protected $casts = [
        'delivery_latitude' => 'decimal:8',
        'delivery_longitude' => 'decimal:8',
        'total_amount' => 'decimal:2',
        'assigned_at' => 'datetime',
        'delivered_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public $timestamps = false;

    public function consumer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'consumer_id');
    }

    public function retailer(): BelongsTo
    {
        return $this->belongsTo(BusinessProfile::class, 'retailer_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(RetailOrderItem::class, 'retail_order_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'order_id');
    }
}
