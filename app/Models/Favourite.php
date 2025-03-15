<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favourite extends Model
{
    protected $fillable = ["customer_id", "dish_id"];

    use HasFactory;
}
