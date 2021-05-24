<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $fillable = [
        'user_id',
        'user_type',
        'amount'
    ];

    public function user() {
        return $this->morphTo();
    }
}
