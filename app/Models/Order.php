<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['purchase_id', 'dish_id', 'quantity', 'price', 'promo'];

    public function dish() {
        return $this->belongsTo('App\Models\Dish', 'dish_id');
    }

}
