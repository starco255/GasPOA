<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentWebhookEvent extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'payment_id', 'provider', 'event_key', 'event_name', 'event_status', 'payload', 'received_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'received_at' => 'datetime',
    ];
}
