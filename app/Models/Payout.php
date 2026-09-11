<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payout extends Model
{
    protected $fillable = ['payment_id', 'business_profile_id', 'amount', 'commission_amount', 'status', 'provider_reference'];
    protected $casts = ['amount' => 'decimal:2', 'commission_amount' => 'decimal:2'];
}
