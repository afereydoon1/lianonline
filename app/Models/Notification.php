<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'text',
        'type',
        'user_id',
        'user_type',
        'connect_id',
        'connect_type',
        'read',
    ];
    public function user()
    {
        return $this->morphTo();
    }
    public function connect()
    {
        return $this->morphTo();
    }

    // $types = [
    //     'CreateUser' => 'ثبت کاربر جدید',
    //     'EditUser' => 'ویرایش کاربر',
    //     'DelUser' => 'حذف کاربر',
    //     'CreatePost' => 'ثبت پست جدید',
    //     'EditPost' => 'ویرایش پست',
    //     'DelPost' => 'حذف پست',
    //     'CreatePostComment' => 'ثبت کامنت جدید یرای پست',
    //     'CreateProduct' => 'ثبت محصول جدید',
    //     'EditProduct' => 'ویرایش محصول',
    //     'DelProduct' => 'حذف محصول',
    //     'CreateProductComment' => 'ثبت کامنت جدید یرای محصول',
    //     'CreateTicket' => 'ثبت تیکت جدید',
    //     'ReplayTicket' => 'پاسخ به تیکت',
    //     'CopyRightTicket' => 'گزارش نقض کپی رایت',
    //     'AbuseReportTicket' => 'گزارش تخلف',
    //     'CreateOrder' => 'ثبت سفارش جدید',
    //     'OrderSuccess' => 'ثبت سفارش موفق',
    //     'CreateWithdrawal' => 'ثبت درخواست وجه',
    // ]
}
