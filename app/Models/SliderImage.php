<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SliderImage extends Model
{
    protected $fillable = [
        'name',
        'full_path',
        'download_path',
    ];

    protected $hidden = [
        'full_path'
    ];
}
