<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeroSlide extends Model
{
    // Add this property to allow these columns to be saved via HeroSlide::create()
    protected $fillable = [
        'image_path',
        'tag',
        'title',
        'order',
    ];
}
