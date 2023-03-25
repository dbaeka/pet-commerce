<?php

/*
|--------------------------------------------------------------------------
| v1 API Routes
|--------------------------------------------------------------------------
*/

// Admin only routes

use App\Http\Controllers\Api\v1\AdminController;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::post('create', [AdminController::class, 'store'])->name('create');
    Route::post('login', [AdminController::class, 'login'])->name('login');
    Route::get('logout', [AdminController::class, 'logout'])->name('logout');
    Route::get('user-listing', [AdminController::class, 'getUserListing'])->name('user-listing');
    Route::put('user-edit/{uuid}', [AdminController::class, 'editUser'])->name('user-edit');
    Route::delete('user-delete/{uuid}', [AdminController::class, 'deleteUser'])->name('user-delete');
});
