<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    protected $fillable = [
        'amount',
        'card',
        'bank',
        'sheba',
        'user_id',
        'wallet_id',
        'status',   //["success","failed","pending"]
    ];

    public function wallet() {
        return $this->belongsTo('App\Models\Wallet');
    }

    public function user() {
        return $this->belongsTo('App\Models\User');
    }

    public function transaction() {
        return $this->hasOne('App\Models\Transaction');
    }

}
