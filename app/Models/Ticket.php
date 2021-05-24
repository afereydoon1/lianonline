<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'title',
        'creator_id',
        'creator_type',
        'product_id',
        'order_id',
        'receiver_id',
        'receiver_type',
        'status',       //["new","answered","processing","closed"]
        'type'          //["support","report_abuse","copy_right"]
    ];

    public function creator() {
        return $this->morphTo();
    }

    public function receiver() {
        return $this->morphTo();
    }

    public function order() {
        return $this->belongsTo('App\Models\Order');
    }

    public function product() {
        return $this->belongsTo('App\Models\Product');
    }

    public function ticketItems() {
        return $this->hasMany('App\Models\TicketItem');
    }
}
