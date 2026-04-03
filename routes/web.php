<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use App\Models\Quote;
// Controllers
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\CoinController;
use App\Http\Controllers\AddressController;
// Admin Controllers
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminBookController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\HeroSlideController;
use App\Http\Controllers\Admin\AuthorController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\CoinController as AdminCoinController;
use App\Http\Controllers\Admin\ProductPreviewController;
use App\Http\Controllers\Admin\BookScraperController;
// Middleware
use App\Http\Middleware\IsAdmin;

Route::get('/random-quote', function () {
    $quote = Quote::inRandomOrder()->first(['quote', 'author']);

    if (! $quote) {
        return response()->json(['quote' => '', 'author' => ''], 404);
    }

    return response()->json($quote);
});

// ADD THIS NEW ROUTE FOR LIVE SEARCH:
Route::get('/api/search-books', [CatalogController::class, 'liveSearch'])->name('api.search.books')->middleware('throttle:60,1');

/*
|--------------------------------------------------------------------------
| Rate Limiters
|--------------------------------------------------------------------------
*/
RateLimiter::for('cart', function (Request $request) {
    return Limit::perMinute(30)->by($request->ip());
});

/*
|------------------------------------------------------------------f--------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [CatalogController::class, 'home'])->name('home');
Route::get('/categories', [CatalogController::class, 'categories'])->name('categories.index');
Route::get('/product/{slug}', [CatalogController::class, 'show'])->name('product.show');
Route::get('/authors', [CatalogController::class, 'authors'])->name('authors.index');
Route::get('/bestsellers', [CatalogController::class, 'bestsellers'])->name('bestsellers.index');

Route::view('/contact', 'pages.contact')->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

Route::view('/newsletter', 'pages.newsletter')->name('newsletter');
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');

/*
|--------------------------------------------------------------------------
| Cart Routes (Rate Limited)
|--------------------------------------------------------------------------
*/
Route::middleware(['throttle:cart'])->group(function () {
    Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/increment/{id}', [CartController::class, 'increment'])->name('cart.increment');
    Route::post('/cart/decrement/{id}', [CartController::class, 'decrement'])->name('cart.decrement');
});

/*
|--------------------------------------------------------------------------
| Checkout (Public + Throttled)
|--------------------------------------------------------------------------
*/
Route::get('/checkout', [OrderController::class, 'index']);

Route::middleware('throttle:5,1')->post('/checkout', [OrderController::class, 'store'])->name('checkout.store');
Route::get('/orders/{order}/confirmation', [OrderController::class, 'confirmation'])->name('order.confirmation');

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->middleware('throttle:5,1')
        ->name('profile.destroy');

    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('order.show');

    // Wishlist
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/{product}/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

    // Reviews
    Route::post('/products/{product}/reviews', [ReviewController::class, 'store'])->name('reviews.store');

    // Wallet
    Route::get('/wallet', [CoinController::class, 'index'])->name('wallet.index');

    // Saved Addresses
    Route::get('/addresses', [AddressController::class, 'index'])->name('addresses.index');
    Route::post('/addresses', [AddressController::class, 'store'])->name('addresses.store');
    Route::put('/addresses/{address}', [AddressController::class, 'update'])->name('addresses.update');
    Route::delete('/addresses/{address}', [AddressController::class, 'destroy'])->name('addresses.destroy');
    Route::patch('/addresses/{address}/default', [AddressController::class, 'setDefault'])->name('addresses.default');

    // Account Settings
    Route::get('/account/settings', [ProfileController::class, 'accountSettings'])->name('account.settings');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', IsAdmin::class])
    ->group(function () {

        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::resource('hero-slides', HeroSlideController::class);

        // Orders
        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{id}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::get('/orders/{id}/invoice', [AdminOrderController::class, 'invoice'])->name('orders.invoice');
        Route::patch('/orders/{id}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');

        // Books
        Route::get('/books', [AdminBookController::class, 'index'])->name('books.index');
        Route::get('/books/create', [AdminBookController::class, 'create'])->name('books.create');
        Route::post('/books/create', [AdminBookController::class, 'store'])->name('books.store');
        Route::get('/books/{id}/edit', [AdminBookController::class, 'edit'])->name('books.edit');
        Route::put('/books/{id}', [AdminBookController::class, 'update'])->name('books.update');
        Route::delete('/books/{id}', [AdminBookController::class, 'destroy'])->name('books.destroy');

        // Authors — search route must be above the resource to avoid {author} capture
        Route::get('/authors/search', [AuthorController::class, 'search'])->name('authors.search');
        Route::resource('authors', AuthorController::class);

        // Reviews
        Route::resource('reviews', AdminReviewController::class)->only(['index']);
        Route::put('/reviews/{review}/approve', [AdminReviewController::class, 'approve'])->name('reviews.approve');
        Route::put('/reviews/{review}/reject', [AdminReviewController::class, 'reject'])->name('reviews.reject');

        // Coins
        Route::get('/coins', [AdminCoinController::class, 'index'])->name('coins.index');
        Route::post('/coins/{user}/adjust', [AdminCoinController::class, 'adjust'])->name('coins.adjust');

        // Product Previews
        Route::post('/products/{product}/previews', [ProductPreviewController::class, 'store'])->name('product-previews.store');
        Route::delete('/previews/{preview}', [ProductPreviewController::class, 'destroy'])->name('product-previews.destroy');

        // Book Scraper
        Route::get('/scraper', [BookScraperController::class, 'index'])->name('scraper.index');
        Route::post('/scraper/search', [BookScraperController::class, 'search'])->name('scraper.search');
        Route::post('/scraper/import', [BookScraperController::class, 'import'])->name('scraper.import');
    });

require __DIR__.'/auth.php';
