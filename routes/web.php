<?php

use App\Http\Controllers\LandingController;
use App\Http\Controllers\PricingController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegisterController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

Route::get('/', [LandingController::class, 'landing'])->name('home');
Route::get('pricing', PricingController::class)->name('pricing');
Route::get('about', AboutController::class)->name('about');
Route::get('register', [RegisterController::class, 'create'])->name('register');
Route::post('register', [RegisterController::class, 'store'])->name('register.store');
Route::get('success/{id}', [RegisterController::class, 'success'])->name('auth.success');


// require __DIR__.'/auth.php';
require __DIR__ . '/admin.php';
require __DIR__ . '/client.php';
require __DIR__ . '/member.php';
