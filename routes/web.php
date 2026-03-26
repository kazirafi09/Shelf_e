<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\AdminDashboardController;
use Illuminate\Support\Facades\Route;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminBookController;
use App\Http\Controllers\CatalogController;
use App\Http\Middleware\IsAdmin;
use App\Http\Controllers\Admin\AdminOrderController;

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
Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');

// --- THE FIX: MOVED THESE ROUTES OUT OF THE ADMIN GROUP ---
Route::post('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/increment/{id}', [CartController::class, 'increment'])->name('cart.increment');
Route::post('/cart/decrement/{id}', [CartController::class, 'decrement'])->name('cart.decrement');

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

Route::post('/checkout', [OrderController::class, 'store'])->name('checkout.store');

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
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Admin Routes (PROTECTED)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['auth', IsAdmin::class])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    
    // Order routes
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('admin.orders.index');
    Route::patch('/orders/{id}/status', [AdminOrderController::class, 'updateStatus'])->name('admin.orders.updateStatus');
    Route::get('/orders/{id}', [AdminOrderController::class, 'show'])->name('admin.orders.show');
    Route::get('/orders/{id}/invoice', [AdminOrderController::class, 'invoice'])->name('admin.orders.invoice');
    
    // Book Inventory routes
    Route::get('/books', [AdminBookController::class, 'index'])->name('admin.books.index');
    Route::get('/books/create', [AdminBookController::class, 'create'])->name('admin.books.create');
    Route::post('/books/create', [AdminBookController::class, 'store'])->name('admin.books.store');
    Route::delete('/books/{id}', [AdminBookController::class, 'destroy'])->name('admin.books.destroy');
    Route::get('/books/{id}/edit', [AdminBookController::class, 'edit'])->name('admin.books.edit');
    Route::put('/books/{id}', [AdminBookController::class, 'update'])->name('admin.books.update');
    
    // Note: The public cart routes were removed from here!
});

require __DIR__.'/auth.php';