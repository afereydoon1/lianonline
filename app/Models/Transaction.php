<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'user_type',
        'wallet_id',
        'type',     //["sale","referral","site","charge","purchase","penalty","withdrawal"]
        'amount',
        'text',
        'status',    //["success","failed","pending"]
        'order_id',
        'product_id',
        'withdrawal_id',
    ];

    protected $hidden = [
    ];

    public function wallet() {
        return $this->belongsTo('App\Models\Wallet');
    }

    public function user() {
        return $this->morphTo();
    }

    public function order() {
        return $this->belongsTo('App\Models\Order');
    }

    public function product() {
        return $this->belongsTo('App\Models\Product');
    }

    public function withdrawal() {
        return $this->belongsTo('App\Models\Withdrawal');
    }
}
