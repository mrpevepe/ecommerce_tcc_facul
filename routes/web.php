<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\CommentsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [ProductController::class, 'indexPublic'])->name('home');
Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');
Route::post('/products/{id}/add-to-cart', [ProductController::class, 'addToCart'])->name('products.addToCart');

// Comments
Route::middleware('auth')->group(function () {
    Route::post('/products/{productId}/comments', [CommentsController::class, 'store'])->name('comments.store');
    Route::put('/products/{productId}/comments/{commentId}', [CommentsController::class, 'update'])->name('comments.update');
    Route::delete('/products/{productId}/comments/{commentId}', [CommentsController::class, 'destroy'])->name('comments.destroy');
});

// Auth
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Carrinho
Route::get('/cart', [ProductController::class, 'cart'])->name('cart.index');
Route::delete('/cart/{variationId}', [ProductController::class, 'removeFromCart'])->name('cart.remove');
Route::post('/cart/update-quantity', [ProductController::class, 'updateCartQuantity'])->name('cart.updateQuantity');

// Rotas do Usuário
Route::middleware('auth')->group(function () {
    Route::get('/user', [UserController::class, 'index'])->name('user.index');
    Route::get('/user/address', [UserController::class, 'showAddressForm'])->name('user.address.form');
    Route::post('/user/address', [UserController::class, 'saveAddress'])->name('user.address.save');
    Route::post('/orders', [OrdersController::class, 'store'])->name('orders.store');
    Route::get('/user/orders/{id}', [OrdersController::class, 'show'])->name('user.orders.show');
    Route::post('/user/orders/{id}/cancel', [OrdersController::class, 'cancel'])->name('user.orders.cancel');
});

// Rotas Admin
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    Route::get('/orders', [OrdersController::class, 'index'])->name('admin.orders.index');
    Route::post('/orders/{id}/deliver', [OrdersController::class, 'markAsDelivered'])->name('admin.orders.deliver');

    Route::get('/products', [ProductController::class, 'index'])->name('admin.products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('admin.products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('admin.products.store');
    Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('admin.products.edit');
    Route::put('/products/{id}', [ProductController::class, 'update'])->name('admin.products.update');
    Route::patch('/variations/{id}/stock', [ProductController::class, 'updateStock'])->name('admin.variations.updateStock');
    Route::get('/products/{id}/variations', [ProductController::class, 'showVariations'])->name('admin.products.variations');
    Route::post('/products/{id}/variations', [ProductController::class, 'storeVariation'])->name('admin.products.storeVariation');
    Route::get('/products/{variationId}/stock/edit', [ProductController::class, 'editStock'])->name('admin.products.editStock');
    Route::post('/products/{variationId}/stock', [ProductController::class, 'saveStock'])->name('admin.products.saveStock');

    Route::get('/categories/create', [ProductController::class, 'createCategory'])->name('admin.categories.create');
    Route::post('/categories', [ProductController::class, 'storeCategory'])->name('admin.categories.store');
    Route::get('/categories/{id}/edit', [ProductController::class, 'editCategory'])->name('admin.categories.edit');
    Route::put('/categories/{id}', [ProductController::class, 'updateCategory'])->name('admin.categories.update');
    Route::delete('/categories/{id}', [ProductController::class, 'destroyCategory'])->name('admin.categories.destroy');
});