<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    use HasFactory;

    protected $fillable = ["img", "upload_id"];

    function uploads() {
        return $this->belongsTo('App\Models\Upload', 'upload_id');
    }
}
