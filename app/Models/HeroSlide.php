<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeroSlide extends Model
{
    protected $fillable = [
        'product_id',
        'image_path',
        'tag',
        'title',
        'order',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
