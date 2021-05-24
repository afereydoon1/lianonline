<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferralRequest extends Model
{
    protected $fillable = [
        'referral_link_id ',
        'referral_code',
        'landing',
        'host',
        'ip',
    ];//order_id,valid

    public function order() {
        return $this->belongsTo('App\Models\Order');
    }

    public function referralLink() {
        return $this->belongsTo('App\Models\ReferralLink');
    }
}
