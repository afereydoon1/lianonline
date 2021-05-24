<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'body',
        'name',
        'email',
        'authorable_id',
        'authorable_type',
        'commentable_id',
        'commentable_type',
        'show_status', //boolean->default(false)
    ];

    /**
     * Get the owning commentable model.
     */
    public function commentable()
    {
        return $this->morphTo();
    }

    /**
     * Get the owning commentable model.
     */
    public function authorable()
    {
        return $this->morphTo();
    }
}
