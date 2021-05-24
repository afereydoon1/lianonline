<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    protected $dates = false;

    protected $fillable = [
        'token',
        'type',
        'email',
        'expired_at'
    ];
}
