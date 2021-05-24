<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketItem extends Model
{
    protected $fillable = [
        'text',
        'creator_id',
        'creator_type',
        'ticket_id'
    ];

    public function creator() {
        return $this->morphTo();
    }
}
