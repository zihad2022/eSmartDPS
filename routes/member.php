<?php

use App\Http\Controllers\Member\Auth\LoginController;
use App\Http\Controllers\Member\DashboardController;
use App\Http\Controllers\Member\PaymentController;
use Illuminate\Support\Facades\Route;

Route::prefix('member')->name('member.')->group(function () {

    // Authentication Routes
    Route::get('login', [LoginController::class, 'login'])->name('login');
    Route::post('login', [LoginController::class, 'authenticate'])->name('authenticate');

    // Protected Routes (Only for logged-in members)
    Route::middleware('member')->group(function () {

        // Dashboard
        Route::get('/', DashboardController::class)->name('dashboard');

        // Payment Routes
        Route::get('payment', [PaymentController::class, 'create'])->name('payment.create');
        Route::post('payment', [PaymentController::class, 'store'])->name('payment.store');

        // Logout
        Route::post('logout', [LoginController::class, 'logout'])->name('logout');
    });
});
