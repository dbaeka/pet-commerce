<?php

/*
|--------------------------------------------------------------------------
| v1 API Routes
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Api\v1\AdminController;
use App\Http\Controllers\Api\v1\BrandController;
use App\Http\Controllers\Api\v1\CategoryController;
use App\Http\Controllers\Api\v1\FileController;
use App\Http\Controllers\Api\v1\MainPageController;
use App\Http\Controllers\Api\v1\OrderController;
use App\Http\Controllers\Api\v1\OrderStatusController;
use App\Http\Controllers\Api\v1\PaymentController;
use App\Http\Controllers\Api\v1\ProductController;
use App\Http\Controllers\Api\v1\UserController;

// Admin only routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::post('create', [AdminController::class, 'store'])->name('create');
    Route::post('login', [AdminController::class, 'login'])->name('login');
    Route::get('logout', [AdminController::class, 'logout'])->name('logout');
    Route::get('user-listing', [AdminController::class, 'getUserListing'])->name('user-listing');
    Route::put('user-edit/{uuid}', [AdminController::class, 'editUser'])->name('user-edit');
    Route::delete('user-delete/{uuid}', [AdminController::class, 'deleteUser'])->name('user-delete');
});


// User routes
Route::prefix('user')->name('user.')->group(function () {
    Route::post('create', [UserController::class, 'store'])->name('create');
    Route::post('login', [UserController::class, 'login'])->name('login');
    Route::get('logout', [UserController::class, 'logout'])->name('logout');
    Route::get('', [UserController::class, 'show'])->name('user-show');
    Route::put('edit', [UserController::class, 'update'])->name('user-edit');
    Route::delete('', [UserController::class, 'delete'])->name('user-delete');
    Route::post('forgot-password', [UserController::class, 'forgotPassword'])->name('forgot-password');
    Route::post('reset-password-token', [UserController::class, 'resetPasswordToken'])->name('reset-password-token');
    Route::get('orders', [UserController::class, 'getOrders'])->name('orders');
});

// Additional Order routes
Route::prefix('orders')->name('orders.')->group(function () {
    Route::get('dashboard', [OrderController::class, 'getDashboard'])->name('dashboard');
    Route::get('shipment-locator', [OrderController::class, 'getShipmentLocator'])->name('shipment-locator');
    Route::get('{uuid}/download', [OrderController::class, 'downloadOrder'])->name('download');
});

// CRUD routes
Route::apiResources([
    'brands' => BrandController::class,

    'categories' => CategoryController::class,

    'order-statuses' => OrderStatusController::class,

    'products' => ProductController::class,

    'payments' => PaymentController::class,

    'orders' => OrderController::class
]);

// Main page routes
Route::prefix('main')->name('main_page.')->group(function () {
    Route::get('promotions', [MainPageController::class, 'getPromotions'])->name('promotions-index');
    Route::get('blogs', [MainPageController::class, 'getBlogs'])->name('blogs-index');
    Route::get('blogs/{uuid}', [MainPageController::class, 'showBlog'])->name('blogs-show');
});

// File routes
Route::prefix('files')->name('files.')->group(function () {
    Route::get('{uuid}', [FileController::class, 'show'])->name('read');
    Route::post('upload', [FileController::class, 'store'])->name('upload');
});
