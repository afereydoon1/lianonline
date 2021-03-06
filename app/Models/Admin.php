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
                'title' => '?????????? ????',
                'route' => '',
                'icon' => '<i class="fas fa-sticky-note"></i>',
                'open' => false,
                'childs' => [
                    [
                        'id' => '',
                        'title' => '???????????? ?? ??????',
                        'route' => 'posts.list',
                    ],
                    [
                        'id' => '',
                        'title' => '?????? ?????????? ?? ????????',
                        'route' => 'posts.create',
                    ],
                    [
                        'id' => '',
                        'title' => '???????????? ?????? ?????????? ????',
                        'route' => 'posts.comments',
                    ],
                    [
                        'id' => '',
                        'title' => '???????? ???????? ?????????? ????',
                        'route' => 'assortment.list',
                    ],
                    [
                        'id' => '',
                        'title' => '?????????? ?????????? ????',
                        'route' => 'tags.list',
                    ],
                ]
            ],
            [
                'id' => '',
                'title' => '??????????????',
                'route' => '',
                'icon' => '<i class="fas fa-shopping-cart"></i>',
                'open' => false,
                'childs' => [
                    [
                        'id' => '',
                        'title' => '???????????? ?? ??????',
                        'route' => 'product.list',
                    ],
                    [
                        'id' => '',
                        'title' => '?????? ?????????? ????????',
                        'route' => 'product.create',
                    ],
                    [
                        'id' => '',
                        'title' => '???????????? ?????? ??????????????',
                        'route' => 'product.comments',
                    ],
                    [
                        'id' => '',
                        'title' => '???????? ???????? ??????????????',
                        'route' => 'category.list',
                    ],
                    [
                        'id' => '',
                        'title' => '?????????? ??????????????',
                        'route' => 'tags.list',
                    ],
                    [
                        'id' => '',
                        'title' => '???????? ?????? ?????? ????????',
                        'route' => 'copy_right.List',
                    ],
                    [
                        'id' => '',
                        'title' => '???????? ?????????? ????????',
                        'route' => 'report_abuse.list',
                    ],
                ]
            ],
            [
                'id' => '',
                'title' => '??????????????',
                'route' => '',
                'icon' => '<i class="fas fa-shopping-bag"></i>',
                'open' => false,
                'childs' => [
                    [
                        'id' => '',
                        'title' => '???????????? ?? ??????',
                        'route' => 'orders.list',
                    ],
//                    [
//                        'id' => '',
//                        'title' => '????????',
//                        'route' => 'orders.buy',
//                    ],
//                    [
//                        'id' => '',
//                        'title' => '????????',
//                        'route' => 'orders.sale',
//                    ],
//                    [
//                        'id' => '',
//                        'title' => '??????????????????',
//                        'route' => 'orders.partnership',
//                    ],
                    [
                        'id' => '',
                        'title' => '?????????????? ????????????',
                        'route' => 'orders.failed',
                    ]
                ]
            ],
            [
                'id' => '',
                'title' => '??????????????',
                'route' => '',
                'icon' => '<i class="fas fa-user"></i>',
                'open' => false,
                'childs' => [
                    [
                        'id' => '',
                        'title' => '???????????? ?? ??????????????',
                        'route' => 'users.list',
                    ],
                    [
                        'id' => '',
                        'title' => '???????????? ?? ?????????? ????',
                        'route' => 'admins.list',
                    ],
                    [
                        'id' => '',
                        'title' => '?????? ?????????? ????????',
                        'route' => 'users.create',
                    ]
                ]
            ],
            [
                'id' => '',
                'title' => '???????????? ???? ????????',
                'route' => '',
                'icon' => '<i class="fas fa-users"></i>',
                'open' => false,
                'childs' => [
                    // [
                    //     'id' => '',
                    //     'title' => '?????????? ????????',
                    //     'route' => 'partnership.user-sales.list',
                    // ],
                    [
                        'id' => '',
                        'title' => '?????????? ??????????????????',
                        'route' => 'partnership.link-sales.list',
                    ]
                ]
            ],
            [
                'id' => '',
                'title' => '???????? ????????',
                'route' => '',
                'icon' => '<i class="fas fa-money-bill"></i>',
                'open' => false,
                'childs' => [
                    [
                        'id' => '',
                        'title' => '???????? ???????????? ????',
                        'route' => 'finance.transactions',
                    ],
                    [
                        'id' => '',
                        'title' => '?????????? ????????',
                        'route' => 'finance.checkout',
                    ],
                    [
                        'id' => '',
                        'title' => '???????? ?????? ??????',
                        'route' => 'finance.wallet.charge',
                    ],
                    // [
                    //     'id' => '',
                    //     'title' => '???????? ????????',
                    //     'route' => 'finance.turnover',
                    // ]
                ]
            ],
            [
                'id' => '',
                'title' => '????????????????',
                'route' => '',
                'icon' => '<i class="fas fa-ticket-alt"></i>',
                'open' => false,
                'childs' => [
                    [
                        'id' => '',
                        'title' => '???????? ?????????????? ????',
                        'route' => 'tickets.list',
                    ],
                    [
                        'id' => '',
                        'title' => '?????????? ?????????????? ????????',
                        'route' => 'tickets.create',
                    ]
                ]
            ],
            [
                'id' => '',
                'title' => '??????????????',
                'route' => '',
                'icon' => '<i class="fas fa-cog"></i>',
                'open' => false,
                'childs' => [
                    [
                        'id' => '',
                        'title' => '?????????????? ??????????',
                        'route' => 'settings.general',
                    ],
                    [
                        'id' => '',
                        'title' => '?????????????? ?????? ??????????',
                        'route' => 'settings.sms',
                    ],
                    [
                        'id' => '',
                        'title' => '?????????????? ?????????? ????????????',
                        'route' => 'settings.gateway',
                    ],
//                    [
//                        'id' => '',
//                        'title' => '?????????????? ???????????? ???? ????????',
//                        'route' => 'settings.partnership',
//                    ]
                    [
                        'id' => '',
                        'title' => '?????????????? ??????????????',
                        'route' => 'settings.slider',
                    ],
                ]
            ],
        ];
    }
}
