<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = ['customer_id', 'restaurant_id', 'address'];

    function customer() {
        return $this->belongsTo('App\Models\User', 'customer_id');
    }

    function restaurant() {
        return $this->belongsTo('App\Models\User', 'restaurant_id');
    }
    
    function dishes() {
        return $this->belongsToMany('App\Models\Dish', 'orders', 'purchase_id', 'dish_id')->withPivot(['quantity', 'price', 'promo']);
    }
}
