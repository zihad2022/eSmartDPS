<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PricingController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('pricing', PricingController::class)->name('pricing');
Route::get('about', AboutController::class)->name('about');

require __DIR__.'/admin.php';
require __DIR__.'/client.php';
require __DIR__.'/member.php';
