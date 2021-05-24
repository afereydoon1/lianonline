<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = ['name'];

    protected $appends = [
        'product_count',
        'post_count'
    ];

    public function products() {
        return $this->morphedByMany('App\Models\Product', 'taggable');
    }

    public function posts() {
        return $this->morphedByMany('App\Models\Post', 'taggable');
    }

    public function getPostCountAttribute() {
        return $this->posts()->count();
    }

    public function getProductCountAttribute() {
        return $this->products()->count();
    }
}
