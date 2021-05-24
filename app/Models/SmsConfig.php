<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsConfig extends Model
{
    protected $fillable = [
        'api_key',
        'api_secret',
        'api_number',
        'api_url'
    ];
}
