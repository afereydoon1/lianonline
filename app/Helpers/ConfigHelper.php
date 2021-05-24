<?php


namespace App\Helpers;


use Illuminate\Support\Facades\DB;

class ConfigHelper
{
    public static function getDbConfig($table, $credential)
    {
        try {
            return DB::table($table)->where($credential)->first()->toArray();
        } catch (\Exception $exception) {
            dd($exception);
        }
    }
}
