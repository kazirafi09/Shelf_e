<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    // 1. Allow mass assignment for these columns
    protected $fillable = ['name', 'slug', 'parent_id'];

    // 2. Define the relationship to Products
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}