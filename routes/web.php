<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\CartController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CuisineController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserCuisineController;
use App\Http\Controllers\UserProfileController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auditLog'])->group(function () {
    // Route to show register form and handle register submission
    Route::get('register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('register', [AuthController::class, 'register']);

    // Route to show login form and handle login submission
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login']);

    // Route to handle logout
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    // Allow to update user profile Email and Password
    Route::get('/profiles/edit', [UserProfileController::class, 'edit'])->name('profiles.edit');
    Route::put('/profiles/update/{id}', [UserProfileController::class, 'update'])->name('profiles.update');
    
    // Protecting the cuisines routes to be accessible only for - admin - logged-in
    Route::middleware('admin')->prefix('admins')->name('admins.')->group(function() {
        Route::resource('cuisines', CuisineController::class);
        Route::resource('categories', CategoryController::class)->except(['show']);
    });

    // Protecting the cuisines routes to be accessible only for - user - logged-in 
    Route::middleware('user')->prefix('users')->name('users.')->group(function() {
        Route::resource('cuisines', UserCuisineController::class)->only(['index', 'show']);
    });

    Route::get('/carts', [CartController::class, 'index'])->name('carts.index');
    Route::post('/carts/add', [CartController::class, 'add'])->name('carts.add');
    Route::post('/carts/update', [CartController::class, 'update'])->name('carts.update');
    Route::post('/carts/remove', [CartController::class, 'remove'])->name('carts.remove');
    Route::post('/carts/clear', [CartController::class, 'clear'])->name('carts.clear');

    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/complete', [OrderController::class, 'complete'])->name('orders.complete');
});