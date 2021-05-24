<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class Assortment extends Model
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
        'post_count'
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

    public function children()
    {
        return $this->hasMany('App\Models\Assortment', 'parent_id')->orderBy('order','asc')->with('children');
    }

    public function parent()
    {
        return $this->belongsTo('App\Models\Assortment', 'parent_id');
    }

    public function posts() {
        return $this->belongsToMany('App\Models\Post');
    }

    public function getPostCountAttribute() {
        return $this->posts()->count();
    }
}
