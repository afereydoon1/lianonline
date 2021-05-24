<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{

    public static function getSign() {

        $sign = mt_rand(0, 9999999);

        while (self::where('sign', $sign)->count()) {
            $sign = mt_rand(0, 9999999);
        }

        return $sign;
    }
}
