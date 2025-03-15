<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = ["customer_id", "address", "default"];

    function customer() {
        return $this->belongsTo('App\Models\User', 'customer_id');
    }
}
