<?php


namespace App\Helpers;


class UrlHelper
{
    public static function vAsset($path)
    {
        return asset($path) . (stristr($path, '?') ? '&' : '?') . env('VERSION_ASSET', '1.0.0');
    }
}
