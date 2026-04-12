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
use App\Http\Controllers\Admin\AuthorController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\CoinController as AdminCoinController;
use App\Http\Controllers\Admin\ProductPreviewController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\VoucherController as AdminVoucherController;
use App\Http\Controllers\Admin\AdminContactController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\AdminHeroImageController;
use App\Http\Controllers\Admin\AdminHeroSlideController;
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

// Voucher preview — authenticated, throttled to prevent brute-force
Route::get('/api/voucher/validate', function (Request $request) {
    if (! auth()->check()) {
        return response()->json(['valid' => false, 'message' => 'Login required.']);
    }

    $code = strtoupper(trim($request->input('code', '')));
    if ($code === '') {
        return response()->json(['valid' => false]);
    }

    $voucher = \App\Models\Voucher::where('code', $code)->first();

    if (! $voucher || ! $voucher->isUsable()) {
        return response()->json(['valid' => false, 'message' => 'Invalid or expired code.']);
    }

    if ($voucher->hasBeenUsedByUser(auth()->id())) {
        return response()->json(['valid' => false, 'message' => 'You have already used this code.']);
    }

    return response()->json([
        'valid'          => true,
        'discount_type'  => $voucher->discount_type,
        'discount_value' => $voucher->discount_value,
        'description'    => $voucher->description,
    ]);
})->middleware(['auth', 'throttle:30,1'])->name('api.voucher.validate');

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
Route::get('/series', [CatalogController::class, 'series'])->name('series.index');

Route::view('/contact', 'pages.contact')->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

Route::get('/returns-policy', function () {
    return view('pages.returns-policy', ['content' => \App\Models\Setting::get('returns_policy', '')]);
})->name('returns-policy');

Route::get('/faq', function () {
    return view('pages.faq', ['content' => \App\Models\Setting::get('faq_content', '')]);
})->name('faq');

Route::view('/newsletter', 'pages.newsletter')->name('newsletter');
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])
    ->name('newsletter.subscribe')
    ->middleware('auth');

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
Route::get('/checkout', [OrderController::class, 'index'])->name('checkout.index');

Route::middleware('throttle:5,1')->post('/checkout', [OrderController::class, 'store'])->name('checkout.store');
Route::get('/orders/{order}/confirmation', [OrderController::class, 'confirmation'])->name('order.confirmation');

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', \App\Http\Middleware\PreventBackHistory::class])->get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::middleware(['auth', \App\Http\Middleware\PreventBackHistory::class])->group(function () {
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
    Route::post('/wallet/redeem', [CoinController::class, 'redeem'])->name('wallet.redeem')->middleware('throttle:10,1');

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
    ->middleware(['auth', IsAdmin::class, \App\Http\Middleware\PreventBackHistory::class])
    ->group(function () {

        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

// Orders
        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{id}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::get('/orders/{id}/invoice', [AdminOrderController::class, 'invoice'])->name('orders.invoice');
        Route::patch('/orders/{id}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');

        // Books
        Route::get('/books', [AdminBookController::class, 'index'])->name('books.index');
        Route::get('/books/create', [AdminBookController::class, 'create'])->name('books.create');
        Route::post('/books/create', [AdminBookController::class, 'store'])->name('books.store');
        Route::get('/books/search', [AdminBookController::class, 'search'])->name('books.search');
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

        // Store Settings
        Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [AdminSettingController::class, 'update'])->name('settings.update');

        // Vouchers
        Route::resource('vouchers', AdminVoucherController::class)->except(['show']);

        // Categories
        Route::resource('categories', AdminCategoryController::class)->only(['index', 'store', 'update', 'destroy']);

        // Contact Messages
        Route::get('/contacts', [AdminContactController::class, 'index'])->name('contacts.index');
        Route::patch('/contacts/{message}/read', [AdminContactController::class, 'markRead'])->name('contacts.markRead');
        Route::delete('/contacts/{message}', [AdminContactController::class, 'destroy'])->name('contacts.destroy');

        // Hero Images (display only — no uploads)
        Route::get('/hero-images', [AdminHeroImageController::class, 'index'])->name('hero-images.index');

        // Hero Books (Featured Slides)
        Route::get('/hero-books', [AdminHeroSlideController::class, 'index'])->name('hero-books.index');
        Route::post('/hero-books', [AdminHeroSlideController::class, 'store'])->name('hero-books.store');
        Route::put('/hero-books/{heroBook}', [AdminHeroSlideController::class, 'update'])->name('hero-books.update');
        Route::delete('/hero-books/{heroBook}', [AdminHeroSlideController::class, 'destroy'])->name('hero-books.destroy');
    });

require __DIR__.'/auth.php';
