<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'order_type', 'order_id', 'payment_method', 'provider', 'provider_reference',
        'provider_payment_reference', 'checkout_url', 'status', 'amount',
        'raw_callback_payload', 'paid_at', 'failed_at', 'failure_reason',
        'gateway_event_id', 'gateway_event', 'gateway_status', 'gateway_paid_amount',
        'gateway_currency', 'gateway_callback_received_at', 'checkout_expires_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2', 'raw_callback_payload' => 'array',
        'gateway_paid_amount' => 'decimal:2', 'paid_at' => 'datetime', 'failed_at' => 'datetime',
        'gateway_callback_received_at' => 'datetime', 'checkout_expires_at' => 'datetime',
    ];
}
