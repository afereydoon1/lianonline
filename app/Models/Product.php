<?php

namespace App\Models;

use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class Product extends Model implements Searchable
{
    use Sluggable;

    protected $fillable = [
        'title',
        'creator_id',
        'creator_type',
        'price',
        'total',
        'discounted_price',
        'marketer_percent',
        'key',
        'briefly',
        'description',
        'type',
        'weight',
        'order',
        'status',
        'history',
        'visit',
        'sale',
        'show_status',
        'comment_status',
        'main_photo',
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
                'source' => 'title',
                'onUpdate' => true,
            ]
        ];
    }

    public function creator()
    {
        return $this->morphTo();
    }

    public function categories() {
        return $this->belongsToMany('App\Models\Category');
    }

    public function images() {
        return $this->hasMany('App\Models\ProductImage');
    }

    public function file() {
        return $this->hasOne('App\Models\ProductFile');
    }

    public function tags()
    {
        return $this->morphToMany('App\Models\Tag', 'taggable');
    }

    public function comments()
    {
        return $this->morphMany('App\Models\Comment', 'commentable');
    }

    public function getSearchResult(): SearchResult
    {
        return new \Spatie\Searchable\SearchResult(
            $this,
            $this->title
        );
    }

    public function orderItems() {
        return $this->hasMany('App\Models\OrderItem');
    }

    public function mainPhotoObj() {
        return $this->hasOne('App\Models\ProductImage','id','main_photo');
    }
}
