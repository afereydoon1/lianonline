<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ReferralLink extends Model
{
    protected $fillable = ['user_id', 'code'];

    public static function generateCode() {
        $unique = false;
        $code = '';
        while (!$unique) {
            $code = Str::random(10);

            $unique = self::where('code', $code)->count() > 0 ? false : true;
        }

        return $code;
    }

    public function referralRequests() {
        return $this->hasMany('App\Models\ReferralRequest');
    }

    public function user() {
        return $this->belongsTo('App\Models\User');
    }
}
