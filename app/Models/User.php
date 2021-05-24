<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * @method static whereMobile($mobile)
 */
class User extends Authenticatable implements JWTSubject
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
        'avatar',
        'state',
        'city',
        'address',
        'bio',
        'bank',
        'bank_account_number',
        'card_number',
        'sheba',
        'postal_code',
        'national_card_image',
        'credit_card_image',
        'mobile_verified_at',
        'password',
        'status'
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
        'post_count',
        'product_count',
        // 'wallet_amount',
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

    public function getProductCountAttribute() {
        return $this->products()->count();
    }

    public function posts() {
        return $this->morphMany('App\Models\Post', 'creator');
    }

    public function getPostCountAttribute() {
        return $this->posts()->count();
    }

    public function comments()
    {
        return $this->morphMany('App\Models\Comment', 'authorable');
    }

    public function orders() {
        return $this->hasMany('App\Models\Order');
    }

    public function sales() {
        return $this->products()->whereHas('orderItems')->with('orderItems');
    }

    public function wallet() {
        return $this->morphOne('App\Models\Wallet','user');
    }

    public function getWalletAmountAttribute() {
        return $this->wallet->amount;
    }

    public function transactions() {
        return $this->morphMany('App\Models\Transaction','user');
    }

    public function withdrawals() {
        return $this->hasOne('App\Models\Withdrawal');
    }

    public function referralLinks() {
        return $this->hasMany('App\Models\ReferralLink');
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


    public function getTypeAttribute() {
        return 'App\Models\User';
    }

}
