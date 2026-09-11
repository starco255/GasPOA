<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WholesaleOrderItem extends Model
{
    protected $fillable = [
        'wholesale_order_id', 'product_id', 'quantity', 'price_per_item'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price_per_item' => 'decimal:2',
    ];

    public $timestamps = false;

    public function order(): BelongsTo
    {
        return $this->belongsTo(WholesaleOrder::class, 'wholesale_order_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}