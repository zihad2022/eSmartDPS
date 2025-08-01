<?php

use App\Http\Controllers\Client\Auth\LoginController;
use App\Http\Controllers\Client\DashboardController;
use App\Http\Controllers\Client\LedgerCategoryController;
use App\Http\Controllers\Client\LedgerController;
use App\Http\Controllers\Client\MemberController;
use App\Http\Controllers\Client\PaymentController;
use App\Http\Controllers\Client\ProjectCategoryController;
use App\Http\Controllers\Client\ProjectController;
use App\Http\Controllers\Client\ShareController;
use App\Http\Controllers\Client\UserController;
use App\Services\PakageService;
use Illuminate\Support\Facades\Route;

Route::prefix('client')->as('client.')->group(function () {
    // Guest Routes
    Route::middleware('guest:client')->group(function () {
        Route::get('login', [LoginController::class, 'login'])->name('login');
        Route::post('login', [LoginController::class, 'authenticate'])->name('authenticate');
    });

    // Authenticated Routes
    Route::middleware('client')->group(function () {
        // Dashboard
        Route::get('/', DashboardController::class)->name('dashboard')->middleware('subscription');
        // Members
        Route::resource('members', MemberController::class);
        // shares
        Route::resource('shares', ShareController::class)->only('index', 'create', 'store', 'edit', 'update', 'destroy');
        // Projects
        Route::resource('projects', ProjectController::class);
        // Project Categories
        Route::resource('project-categories', ProjectCategoryController::class);
        // Payments
        Route::resource('payments', PaymentController::class)->only('index', 'edit', 'update');
        // Ledger Categories
        Route::resource('ledger-categories', LedgerCategoryController::class);
        // Ledgers
        Route::resource('ledgers', LedgerController::class);
        // Users
        Route::resource('users', UserController::class)->only('index', 'create', 'store', 'show', 'edit', 'update', 'destroy')->middleware('client.role:admin,manager');
        // Logout
        Route::post('logout', [LoginController::class, 'logout'])->name('logout');

        Route::get('subscription/expired', function () {
            return view('client.subscription.expired');
        })->name('subscription.expired');

        Route::get('renew-subscription', function (PakageService $pakageService) {
            $client = auth('client')->user();
            $lastSubscription = $client->lastSubscription?->load('subscription');

            if (! $lastSubscription || ! $lastSubscription->subscription) {
                abort(404, 'No valid subscription to renew.');
            }

            $pakageService->renewSubscription($client, $lastSubscription->subscription);

            return redirect()->route('client.dashboard');
        })->name('subscription.renew');

    });
});
