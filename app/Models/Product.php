<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    // 1. Allow mass assignment for all the book details
    protected $fillable = [
        'category_id', 
        'title', 
        'author', 
        'slug', 
        'description',
        'synopsis',  
        'price', 
        'stock_quantity', 
        'rating', 
        'image_path'
    ];

    // 2. Define the relationship back to the Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}