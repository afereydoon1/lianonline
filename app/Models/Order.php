<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'customer_name',
        'customer_email',
        'customer_mobile',
        'customer_state',
        'customer_city',
        'customer_address',
        'customer_postal_code',
        'referrer_id',
        'callback_url',
        'total_price',
    ];

    protected $appends = [
        'order_status',
        'order_date'
    ];

    public function orderItems() {
        return $this->hasMany('App\Models\OrderItem');
    }

    public function payments() {
        return $this->hasMany('App\Models\Payment');
    }

    public function user() {
        return $this->belongsTo('App\Models\User');
    }

    public function getOrderStatusAttribute() {
        return trans('labels.orders.statuses.' . $this->status);
    }

    public function getOrderDateAttribute() {
        return jdate($this->created_at)->format('Y/m/d');
    }
}
