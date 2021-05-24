<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable implements JWTSubject, CanResetPassword
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'mobile',
        'password',
        'role_id',
        'email_verified_at',
        'mobile_verified_at',
        'status',
        'avatar',
        'bio'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = [
        'wallet_amount',
        'type'
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function products() {
        return $this->morphMany('App\Models\Product', 'creator');
    }

    public function posts() {
        return $this->morphMany('App\Models\Post', 'creator');
    }

    public function comments()
    {
        return $this->morphMany('App\Models\Comment', 'authorable');
    }

    public function tickets() {
        return $this->morphMany('App\Models\Ticket', 'creator');
    }

    public function otherTickets() {
        return $this->morphMany('App\Models\Ticket', 'receiver');
    }

    public function ticketItems() {
        return $this->morphMany('App\Models\TicketItem', 'creator');
    }

    public function contacts() {
        return $this->morphMany('App\Models\ContactsList', 'owner');
    }

    public function transactions() {
        return $this->morphMany('App\Models\Transaction','user');
    }
    public function wallet() {
        return $this->morphOne('App\Models\Wallet','user');
    }
    public function getWalletAmountAttribute() {
        return $this->wallet->amount;
    }
    public function getTypeAttribute() {
        return 'App\Models\Admin';
    }

    public function menu() {
        return [
            [
                'id' => '',
                'title' => 'نوشته ها',
                'route' => '',
                'icon' => '<i class="fas fa-sticky-note"></i>',
                'open' => false,
                'childs' => [
                    [
                        'id' => '',
                        'title' => 'مشاهده ی همه',
                        'route' => 'posts.list',
                    ],
                    [
                        'id' => '',
                        'title' => 'ثبت نوشته ی جدید',
                        'route' => 'posts.create',
                    ],
                    [
                        'id' => '',
                        'title' => 'دیدگاه های نوشته ها',
                        'route' => 'posts.comments',
                    ],
                    [
                        'id' => '',
                        'title' => 'دسته بندی نوشته ها',
                        'route' => 'assortment.list',
                    ],
                    [
                        'id' => '',
                        'title' => 'برچسب نوشته ها',
                        'route' => 'tags.list',
                    ],
                ]
            ],
            [
                'id' => '',
                'title' => 'محصولات',
                'route' => '',
                'icon' => '<i class="fas fa-shopping-cart"></i>',
                'open' => false,
                'childs' => [
                    [
                        'id' => '',
                        'title' => 'مشاهده ی همه',
                        'route' => 'product.list',
                    ],
                    [
                        'id' => '',
                        'title' => 'ثبت محصول جدید',
                        'route' => 'product.create',
                    ],
                    [
                        'id' => '',
                        'title' => 'دیدگاه های محصولات',
                        'route' => 'product.comments',
                    ],
                    [
                        'id' => '',
                        'title' => 'دسته بندی محصولات',
                        'route' => 'category.list',
                    ],
                    [
                        'id' => '',
                        'title' => 'برچسب محصولات',
                        'route' => 'tags.list',
                    ],
                    [
                        'id' => '',
                        'title' => 'لیست نقض کپی رایت',
                        'route' => 'copy_right.List',
                    ],
                    [
                        'id' => '',
                        'title' => 'لیست گزارش تخلف',
                        'route' => 'report_abuse.list',
                    ],
                ]
            ],
            [
                'id' => '',
                'title' => 'سفارشات',
                'route' => '',
                'icon' => '<i class="fas fa-shopping-bag"></i>',
                'open' => false,
                'childs' => [
                    [
                        'id' => '',
                        'title' => 'مشاهده ی همه',
                        'route' => 'orders.list',
                    ],
//                    [
//                        'id' => '',
//                        'title' => 'خرید',
//                        'route' => 'orders.buy',
//                    ],
//                    [
//                        'id' => '',
//                        'title' => 'فروش',
//                        'route' => 'orders.sale',
//                    ],
//                    [
//                        'id' => '',
//                        'title' => 'بازاریابی',
//                        'route' => 'orders.partnership',
//                    ],
                    [
                        'id' => '',
                        'title' => 'سفارشات ناموفق',
                        'route' => 'orders.failed',
                    ]
                ]
            ],
            [
                'id' => '',
                'title' => 'کاربران',
                'route' => '',
                'icon' => '<i class="fas fa-user"></i>',
                'open' => false,
                'childs' => [
                    [
                        'id' => '',
                        'title' => 'مشاهده ی کاربران',
                        'route' => 'users.list',
                    ],
                    [
                        'id' => '',
                        'title' => 'مشاهده ی ادمین ها',
                        'route' => 'admins.list',
                    ],
                    [
                        'id' => '',
                        'title' => 'ثبت کاربر جدید',
                        'route' => 'users.create',
                    ]
                ]
            ],
            [
                'id' => '',
                'title' => 'همکاری در فروش',
                'route' => '',
                'icon' => '<i class="fas fa-users"></i>',
                'open' => false,
                'childs' => [
                    // [
                    //     'id' => '',
                    //     'title' => 'گزارش فروش',
                    //     'route' => 'partnership.user-sales.list',
                    // ],
                    [
                        'id' => '',
                        'title' => 'گزارش بازاریابی',
                        'route' => 'partnership.link-sales.list',
                    ]
                ]
            ],
            [
                'id' => '',
                'title' => 'امور مالی',
                'route' => '',
                'icon' => '<i class="fas fa-money-bill"></i>',
                'open' => false,
                'childs' => [
                    [
                        'id' => '',
                        'title' => 'لیست تراکنش ها',
                        'route' => 'finance.transactions',
                    ],
                    [
                        'id' => '',
                        'title' => 'تسویه حساب',
                        'route' => 'finance.checkout',
                    ],
                    [
                        'id' => '',
                        'title' => 'شارژ کیف پول',
                        'route' => 'finance.wallet.charge',
                    ],
                    // [
                    //     'id' => '',
                    //     'title' => 'گردش مالی',
                    //     'route' => 'finance.turnover',
                    // ]
                ]
            ],
            [
                'id' => '',
                'title' => 'پشتیبانی',
                'route' => '',
                'icon' => '<i class="fas fa-ticket-alt"></i>',
                'open' => false,
                'childs' => [
                    [
                        'id' => '',
                        'title' => 'لیست درخواست ها',
                        'route' => 'tickets.list',
                    ],
                    [
                        'id' => '',
                        'title' => 'ایجاد درخواست جدید',
                        'route' => 'tickets.create',
                    ]
                ]
            ],
            [
                'id' => '',
                'title' => 'تنظیمات',
                'route' => '',
                'icon' => '<i class="fas fa-cog"></i>',
                'open' => false,
                'childs' => [
                    [
                        'id' => '',
                        'title' => 'تنظیمات عمومی',
                        'route' => 'settings.general',
                    ],
                    [
                        'id' => '',
                        'title' => 'تنظیمات پنل پیامک',
                        'route' => 'settings.sms',
                    ],
                    [
                        'id' => '',
                        'title' => 'تنظیمات درگاه پرداخت',
                        'route' => 'settings.gateway',
                    ],
//                    [
//                        'id' => '',
//                        'title' => 'تنظیمات همکاری در فروش',
//                        'route' => 'settings.partnership',
//                    ]
                    [
                        'id' => '',
                        'title' => 'تنظیمات اسلایدر',
                        'route' => 'settings.slider',
                    ],
                ]
            ],
        ];
    }
}
