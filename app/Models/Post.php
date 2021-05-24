<?php

namespace App\Models;

use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class Post extends Model implements Searchable
{
    use Sluggable;

    protected $fillable = [
        'creator_id',
        'creator_type',
        'title',
        'text',
        'visit',
        'show_status',
        'comment_status'
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

    public function assortments() {
        return $this->belongsToMany('App\Models\Assortment');
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

    public function image()
    {
        return $this->hasOne(PostImage::class, 'post_id','id');
    }
}
