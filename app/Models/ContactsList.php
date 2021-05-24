<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactsList extends Model
{
    protected $table = 'contacts_lists';

    protected $fillable = [
        'contactable_id',
        'contactable_type',
    ];

    public function contactable() {
        return $this->morphTo();
    }
}
