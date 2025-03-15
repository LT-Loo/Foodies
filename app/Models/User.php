<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'address',
        'userType',
        'desc',
        'restType',
        'pfp'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    function dishes() {
        return $this->hasMany('App\Models\Dish', 'restaurant_id');
    }

    function cust_purchases() {
        return $this->hasMany('App\Models\Purchase', 'customer_id');
    }

    function rest_purchases() {
        return $this->hasMany('App\Models\Purchase', 'restaurant_id');
    }

    function addresses() {
        return $this->hasMany('App\Models\Address', 'customer_id');
    }

    function favourites() {
        return $this->belongsToMany('App\Models\Dish', 'favourites', 'customer_id', 'dish_id');
    }

    function photos() {
        return $this->hasMany('App\Models\Photo', 'user_id');
    }

    function categories() {
        return $this->hasMany('App\Models\Category', 'restaurant_id');
    }

    function uploads() {
        return $this->hasMany('App\Models\Upload', 'user_id');
    }
}
