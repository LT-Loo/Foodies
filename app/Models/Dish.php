<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dish extends Model
{
    use HasFactory;

    protected $fillable = ["name", "desc", "restaurant_id", "price", "promo", "category_id", "pfp"];

    function restaurant() {
        return $this->belongsTo('App\Models\User', 'restaurant_id');
    }

    function category() {
        return $this->belongsTo('App\Models\Category', 'category_id');
    }

    function purchases() {
        return $this->belongsToMany('App\Models\Purchase', 'orders', 'dish_id', 'purchase_id')->withPivot(['quantity', 'price', 'promo']);
    }

    function favourites() {
        return $this->belongsToMany('App\Models\User', 'favourites', 'dish_id', 'customer_id');
    }

    function reviews() {
        return $this->hasMany('App\Models\Upload', 'dish_id');
    }

}
