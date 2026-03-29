<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\AdminDashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminBookController;
use App\Http\Controllers\CatalogController;
use App\Http\Middleware\IsAdmin;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\HeroSlideController;
use App\Models\Quote;

// --- NEW IMPORTS FOR RATE LIMITING ---
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;

Route::get('/random-quote', function () {
    return response()->json(
        Quote::inRandomOrder()->first()
    );
});

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [CatalogController::class, 'home'])->name('home');
Route::get('/categories', [CatalogController::class, 'categories'])->name('categories.index');
Route::get('/product/{slug}', [CatalogController::class, 'show'])->name('product.show');
Route::get('/authors', [CatalogController::class, 'authors'])->name('authors.index');
Route::get('/bestsellers', [CatalogController::class, 'bestsellers'])->name('bestsellers.index');
Route::view('/contact', 'pages.contact')->name('contact');
Route::view('/newsletter', 'pages.newsletter')->name('newsletter');

/*
|--------------------------------------------------------------------------
| Cart & Checkout Routes (PUBLIC)
|--------------------------------------------------------------------------
*/

// Define the Rate Limiter (Max 30 cart actions per minute per IP) to prevent spam
RateLimiter::for('cart', function (Request $request) {
    return Limit::perMinute(30)->by($request->ip());
});

// Wrap Cart routes in the throttle middleware
Route::middleware(['throttle:cart'])->group(function () {
    Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/increment/{id}', [CartController::class, 'increment'])->name('cart.increment');
    Route::post('/cart/decrement/{id}', [CartController::class, 'decrement'])->name('cart.decrement');
});

// View Checkout Page
Route::get('/checkout', function () {
    $cartItems = session()->get('cart', []);
    $subtotal = 0;
    foreach ($cartItems as $item) {
        $subtotal += $item['price'] * $item['quantity'];
    }
    $shipping = count($cartItems) > 0 ? 150 : 0; 
    $total = $subtotal + $shipping;
    return view('checkout.index', compact('cartItems', 'subtotal', 'shipping', 'total'));
});

// Submit Checkout (Throttled to 5 requests per minute to prevent order spam)
Route::middleware('throttle:5,1')->group(function () {
    Route::post('/checkout', [OrderController::class, 'store'])->name('checkout.store');
});

/*
|--------------------------------------------------------------------------
| User Dashboard & Profile Routes (Authenticated)
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    
    // Throttled to 5 requests per minute for security
    Route::middleware('throttle:5,1')->delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Admin Routes (PROTECTED)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->name('admin.') 
    ->middleware(['auth', IsAdmin::class])
    ->group(function () {
        
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        
        Route::resource('hero-slides', HeroSlideController::class);
        
        // Order routes
        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::patch('/orders/{id}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');
        Route::get('/orders/{id}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::get('/orders/{id}/invoice', [AdminOrderController::class, 'invoice'])->name('orders.invoice');
        
        // Book Inventory routes
        Route::get('/books', [AdminBookController::class, 'index'])->name('books.index');
        Route::get('/books/create', [AdminBookController::class, 'create'])->name('books.create');
        Route::post('/books/create', [AdminBookController::class, 'store'])->name('books.store');
        Route::delete('/books/{id}', [AdminBookController::class, 'destroy'])->name('books.destroy');
        Route::get('/books/{id}/edit', [AdminBookController::class, 'edit'])->name('books.edit');
        Route::put('/books/{id}', [AdminBookController::class, 'update'])->name('books.update');
    });

require __DIR__.'/auth.php';