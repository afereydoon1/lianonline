<?php

return [
    'box_titles' => [
        'users' => [
            'main' => 'کاربران',
            'index' => 'لیست کاربران',
            'create' => 'افزودن کاربر جدید',
            'edit' => 'ویرایش کاربر : ',
            'delete' => 'حذف کاربر : ',
        ],
        'profiles' => [
            'main' => 'ناحیه ی کاربری',
            'edit_info' => 'ویرایش اطلاعات کاربری : ',
            'edit_password' => 'تغییر رمزعبور : ',
        ],
        'feedback' => [
            'main' => 'نظرات و پیشنهادات',
            'index' => 'لیست نظرات و پیشنهادات',
        ],
        'rate' => [
            'main' => 'امتیازدهی',
            'index' => 'لیست امتیازدهی',
        ],
    ],
    'table' => [
        'row_number' => '#',
        'users' => [
            'name' => 'نام و نام خانوادگی',
            'email' => 'ایمیل',
            'role_id' => 'گروه کاربری'
        ],
        'feedback' => [
            'subject' => 'موضوع',
            'comment' => 'متن نظر و پیشنهاد',
            'mobile' => 'شماره موبایل',
            'sim_type' => 'نوع سیم کارت',
            'app_version' => 'ورژن اپلیکیشن',
            'os_version' => 'سیستم عامل',
            'source' => 'درگاه',
            'device_model' => 'نوع دستگاه',
        ],
        'rate' => [
            'subject' => 'موضوع',
            'comment' => 'متن نظر',
            'mobile' => 'شماره موبایل',
            'sim_type' => 'نوع سیم کارت',
            'app_version' => 'ورژن اپلیکیشن',
            'os_version' => 'سیستم عامل',
            'source' => 'درگاه',
            'device_model' => 'نوع دستگاه',
        ],
    ],
    'buttons' => [
        'operations' => 'عملیات',
        'add' => 'افزودن',
        'edit' => 'ویرایش',
        'delete' => 'حذف',
        'back' => 'بازگشت',
        'save' => 'ذخیره',
        'filters' => 'فیلترگذاری',
        'users' => [
            'add' => 'افزودن کاربر جدید'
        ],
        'feedback' => [
            'answer' => 'پاسخ',
        ],
        'rate' => [
            'answer' => 'پاسخ',
        ]
    ],
    'menus' => [
        'dashboard' => 'داشبورد',
        'logout' => 'خروج از ناحیه ی کاربری',
    ],
    'errors' => [
        'feedback' => [
            'invalid' => 'نظر انتخاب شده معتبر نمی باشد.',
            'alreadyHasAnswer' => 'قبلا برای این نظر پاسخ ثبت شده و امکان ثبت مجدد پاسخ وجود ندارد.',
            'webserviceError' => 'خطایی هنگام ارتباط با وب سرویس رخ داده است و امکان ثبت پاسخ وجود ندارد.',
        ],
        'rate' => [
            'invalid' => 'امتیاز انتخاب شده معتبر نمی باشد.',
            'alreadyHasAnswer' => 'قبلا برای این امتیاز پاسخ ثبت شده و امکان ثبت مجدد پاسخ وجود ندارد.',
            'webserviceError' => 'خطایی هنگام ارتباط با وب سرویس رخ داده است و امکان ثبت پاسخ وجود ندارد.',
        ],
    ],
    'privateMessageSubject' => [
        'feedback' => 'پاسخ پیشنهاد و انتقاد',
        'rate' => 'پاسخ امتیازدهی',
    ],
    'orders' => [
        'statuses' => [
            'new' => 'جدید',
            'ok' => 'موفق',
            'canceled' => 'لغو شده',
            'failed' => 'خطا در تکمیل سفارش',
        ]
    ]
];
