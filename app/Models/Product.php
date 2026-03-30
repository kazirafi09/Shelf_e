<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    // 1. Allow mass assignment for all the book details
    protected $fillable = [
        'category_id', 
        'title', 
        'author', 
        'slug', 
        'description',
        'synopsis',  
        'paperback_price',
        'hardcover_price', 
        'stock_quantity', 
        'rating', 
        'image_path'
    ];

    // 2. Define the relationship back to the Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function getDisplayPriceAttribute()
    {
        // If both exist, show the lowest price
        if ($this->paperback_price && $this->hardcover_price) {
            return min($this->paperback_price, $this->hardcover_price);
        }
        
        // Otherwise, return whichever one exists (or 0 if neither)
        return $this->paperback_price ?? $this->hardcover_price ?? 0;
    }
}