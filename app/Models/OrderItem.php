<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'product_id',
        'order_id',
        'product_title',
        'product_price',
        'product_total',
        'product_weight',
        'type',
        'file_url',
        'marketer_percent'
    ];

    public function product() {
        return $this->belongsTo('App\Models\Product');
    }
}
