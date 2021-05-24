<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static whereMobile($get)
 * @method static create(array $array)
 */
class Otp extends Model
{
    protected $fillable = [
        'mobile',
        'code',
        'expired_at'
    ];
    public static function getNewOtp() {
        return str_pad(mt_rand(0, 999999), 6, '0');
    }

    public static function checkAndRemoveOtp($mobile, $code) {
        $otp = self::where([
            'mobile' => $mobile,
            'code' => $code
        ])
            ->where('expired_at', '>', Carbon::now())
            ->first();

        if ($otp) {
            $otp->delete();
            return true;
        }

        return false;
    }
	
	public static function checkAndRemoveMobile($mobile) {
        $otp = self::where([
            'mobile' => $mobile
        ])
            ->where('expired_at', '<', Carbon::now()->subMinutes(5)->format('Y-m-d H:i:s'))
            ->first();

        if ($otp) {
            $otp->delete();
            return true;
        }

        return false;
    }
}
