<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'dish_id', 'comment'];

    function uploader() {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    function photos() {
        return $this->hasMany('App\Models\Photo', 'upload_id');
    }
}
