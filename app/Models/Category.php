<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * The "booted" method of the model.
     * This automatically clears the global category cache anytime
     * a category is created, updated, or deleted!
     */
    protected static function booted(): void
    {
        $clearCache = function () {
            Cache::forget('global_categories');
        };

        static::created($clearCache);
        static::updated($clearCache);
        static::deleted($clearCache);
    }
}
