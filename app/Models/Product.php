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
        'sale_price',
        'sale_ends_at',
        'stock_quantity',
        'rating',
        'image_path',
    ];

    protected $casts = [
        'sale_ends_at' => 'datetime',
    ];

    // 2. Define the relationship back to the Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function authors()
    {
        return $this->belongsToMany(Author::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews()
    {
        return $this->hasMany(Review::class)->approved();
    }

    public function previews()
    {
        return $this->hasMany(ProductPreview::class)->orderBy('sort_order');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
    /**
     * Returns the sale_price if a sale is currently active, null otherwise.
     */
    public function getActiveSalePriceAttribute(): ?float
    {
        if ($this->sale_price && $this->sale_ends_at && $this->sale_ends_at->isFuture()) {
            return (float) $this->sale_price;
        }
        return null;
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
