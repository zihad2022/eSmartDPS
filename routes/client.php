<?php

use App\Http\Controllers\Client\Auth\LoginController;
use App\Http\Controllers\Client\DashboardController;
use App\Http\Controllers\Client\LedgerCategoryController;
use App\Http\Controllers\Client\LedgerController;
use App\Http\Controllers\Client\MemberController;
use App\Http\Controllers\Client\PaymentController;
use App\Http\Controllers\Client\ProjectCategoryController;
use App\Http\Controllers\Client\ProjectController;
use App\Http\Controllers\Client\Settings\GeneralController;
use App\Http\Controllers\Client\SubscriptionController;
use App\Http\Controllers\Client\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('client')->name('client.')->group(function () {

    /**
     * -------------------------
     * Authentication Routes
     * -------------------------
     */
    Route::controller(LoginController::class)->group(function () {
        Route::get('login', 'login')->name('login');
        Route::post('login', 'authenticate')->name('authenticate');
        Route::post('logout', 'logout')->name('logout');
    });

    /**
     * ------------------------------
     * Protected Client Panel Routes
     * ------------------------------
     */
    Route::middleware('client')->group(function () {

        // Dashboard (with subscription check)
        Route::get('/', DashboardController::class)
            ->name('dashboard')
            ->middleware('subscription');

        /**
         * Resource Controllers
         */
        Route::resources([
            'members' => MemberController::class,
            'projects' => ProjectController::class,
            'project-categories' => ProjectCategoryController::class,
            'ledger-categories' => LedgerCategoryController::class,
            'ledgers' => LedgerController::class,
        ]);

        // Payments (limited actions)
        Route::resource('payments', PaymentController::class)
            ->only(['index', 'edit', 'update']);

        // Users (restricted by role)
        Route::resource('users', UserController::class)
            ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);

        /**
         * Settings
         */
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('general', [GeneralController::class, 'edit'])->name('general.edit');
            Route::put('general', [GeneralController::class, 'update'])->name('general.update');
        });

        /**
         * Subscription Management
         */
        Route::prefix('subscription')->name('subscription.')->group(function () {
            Route::get('expired', [SubscriptionController::class, 'expired'])
                ->name('expired');

            Route::get('renew', [SubscriptionController::class, 'renew'])
                ->name('renew');

            Route::get('packages', [SubscriptionController::class, 'packages'])
                ->name('packages');
        });
    });
});
