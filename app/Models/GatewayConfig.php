<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GatewayConfig extends Model
{
    protected $fillable = [
        'merchantId',
        'gateway_key',
        'gateway_name',
        'gateway_base_url',
        'min_withdrawal_amount',
    ];
}
