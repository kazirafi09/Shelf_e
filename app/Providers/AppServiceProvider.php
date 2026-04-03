<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache; // FIX: Added Cache facade
use App\Models\Category;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::defaultView('vendor.pagination.custom');
        Paginator::defaultSimpleView('vendor.pagination.simple-tailwind');

        // FIX 2.3: View Composer + Caching
        // 1. View::composer delays the query until a view actually needs it (protects Artisan commands).
        // 2. Cache::remember stores the result in memory for 1 hour (3600 seconds) so the database isn't hammered.
        View::composer('*', function ($view) {
            $categories = Cache::remember('global_categories', 3600, function () {
                return Category::orderBy('name')->get();
            });

            $view->with('globalCategories', $categories);
        });
    }
}
