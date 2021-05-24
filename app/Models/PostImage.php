<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostImage extends Model
{
    protected $fillable = [
        'name',
        'full_path',
        'download_path',
        'post_id',
    ];

    protected $hidden = [
        'full_path'
    ];

    public function post() {
        return $this->belongsTo('App\Models\Post','id','post_id');
    }
}
