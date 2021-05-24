<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class Category extends Model
{
    use Sluggable;

    protected $fillable = [
        'title',
        'briefly',
        'icon',
        'image',
        'parent_id',
        'order',
        'status',
    ];

    protected $appends = [
        'product_count'
    ];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    public function children() {
        return $this->hasMany('App\Models\Category', 'parent_id')->orderBy('order','asc')->with('children');
    }

    public function parent() {
        return $this->belongsTo('App\Models\Category', 'parent_id');
    }

    public function products() {
        return $this->belongsToMany('App\Models\Product');
    }

    public function getProductCountAttribute() {
        return $this->products()->count();
    }
}
