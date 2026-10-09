<?php

use App\Http\Controllers\Client\Auth\ForgotPasswordPhoneController;
use App\Http\Controllers\Client\Auth\LoginController;
use App\Http\Controllers\Client\Auth\OtpVerifyController;
use App\Http\Controllers\Client\Auth\RegisterController;
use App\Http\Controllers\Client\Auth\ResetPasswordPhoneController;
use App\Http\Controllers\Client\BkashPaymentController;
use App\Http\Controllers\Client\CheckoutController;
use App\Http\Controllers\Client\DashboardController;
use App\Http\Controllers\Client\InvoiceController;
use App\Http\Controllers\Client\LedgerCategoryController;
use App\Http\Controllers\Client\LedgerController;
use App\Http\Controllers\Client\LedgerReportController;
use App\Http\Controllers\Client\MemberController;
use App\Http\Controllers\Client\MemberExportController;
use App\Http\Controllers\Client\PaymentController;
use App\Http\Controllers\Client\PaymentExportController;
use App\Http\Controllers\Client\ProjectCategoryController;
use App\Http\Controllers\Client\ProjectController;
use App\Http\Controllers\Client\ProjectExportController;
use App\Http\Controllers\Client\Settings\BackupSecurityController;
use App\Http\Controllers\Client\Settings\GeneralController;
use App\Http\Controllers\Client\Settings\NotificationController;
use App\Http\Controllers\Client\Settings\PaymentController as SettingsPaymentController;
use App\Http\Controllers\Client\Settings\ShareController;
use App\Http\Controllers\Client\SslcommerzPaymentController;
use App\Http\Controllers\Client\StartPaidSubscriptionController;
use App\Http\Controllers\Client\StartTrailSubscriptionController;
use App\Http\Controllers\Client\SubscriptionController;
use App\Http\Controllers\Client\SubscriptionPaymentController;
use App\Http\Controllers\Client\TicketChatController;
use App\Http\Controllers\Client\TicketController;
use App\Http\Controllers\Client\TicketExportController;
use App\Http\Controllers\Client\UserActivityController;
use App\Http\Controllers\Client\UserController;
use App\Http\Controllers\Client\UserExportController;
use App\Http\Controllers\Client\UserProfileController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Client Routes
|--------------------------------------------------------------------------
*/

Route::prefix('client')->name('client.')->group(function () {

    /**
     * Authentication
     */
    Route::controller(LoginController::class)->group(function () {
        Route::get('login', 'login')->name('login');
        Route::post('login', 'authenticate')->name('authenticate');
        Route::post('logout', 'logout')->name('logout');
    });

    Route::controller(RegisterController::class)->group(function () {
        Route::get('register', 'create')->name('register');
        Route::post('register', 'store')->name('register.store');
        Route::get('success/{id}', 'success')->name('auth.success');
    });

    /**
     * OTP & Password Reset
     */
    Route::prefix('password')->name('password.')->group(function () {
        Route::get('forgot', [ForgotPasswordPhoneController::class, 'create'])->name('forgot');
        Route::post('forgot', [ForgotPasswordPhoneController::class, 'store'])->name('forgot.store');
        Route::get('reset', [ResetPasswordPhoneController::class, 'create'])->name('reset');
        Route::post('reset', [ResetPasswordPhoneController::class, 'store'])->name('reset.store');
    });

    Route::prefix('otp')->name('otp.')->group(function () {
        Route::get('verify/{phone}', [OtpVerifyController::class, 'create'])->name('verify');
        Route::post('verify/{phone}', [OtpVerifyController::class, 'verify'])->name('verify.post');
    });

    /**
     * Protected Client Routes
     */
    Route::middleware('client')->group(function () {

        // Dashboard
        Route::get('/', DashboardController::class)->name('dashboard')->middleware('subscription');
        Route::view('menu', 'client.menu')->name('menu')->middleware('subscription');
        Route::view('settings', 'client.settings.index')->name('settings.index')->middleware(['subscription','client.role:super-admin,admin']);

        /**
         * Subscription Management
         */
        Route::prefix('subscription')->name('subscription.')->group(function () {
            Route::get('expired', [SubscriptionController::class, 'expired'])->name('expired');
            Route::get('packages', [SubscriptionController::class, 'packages'])->name('packages');

            Route::get('start-trial/{package}', StartTrailSubscriptionController::class)->name('start.trial');
            Route::get('start-paid/{package}', StartPaidSubscriptionController::class)->name('start.paid');
        });

        /**
         * Payments & Invoices
         */
        Route::prefix('payments')->name('payments.')->group(function () {
            Route::post('process/{invoice}', [SubscriptionPaymentController::class, 'processPayment'])->name('process');

            // bKash callback
            Route::match(['get', 'post'], 'bkash/callback', [BkashPaymentController::class, 'callback'])->name('bkash.callback');

            // SSLCommerz
            Route::get('sslcommerz/pay/{invoice}', [SslcommerzPaymentController::class, 'pay'])->name('sslcommerz.pay');
        });

        Route::resource('invoices', InvoiceController::class)->only(['index', 'show'])->middleware('client.role:super-admin,admin');

        /**
         * Resources (require active subscription)
         */
        Route::middleware('subscription')->group(function () {

            // Standard resources
            Route::resources([
                'members' => MemberController::class,
                'projects' => ProjectController::class,
                'project-categories' => ProjectCategoryController::class,
                'ledger-categories' => LedgerCategoryController::class,
                'ledgers' => LedgerController::class,
                'tickets' => TicketController::class,
            ]);

            // Extra routes
            Route::get('ledgers-report', LedgerReportController::class)->name('ledgers.report');

            Route::prefix('tickets/{ticket}')->name('tickets.')->group(function () {
                Route::get('chat', [TicketChatController::class, 'chat'])->name('chat');
                Route::post('message', [TicketChatController::class, 'storeMessage'])->name('message.store');
            });

            Route::resource('payments', PaymentController::class)->only(['index', 'edit', 'update', 'show', 'destroy']);
            Route::resource('users', UserController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);

            Route::get('users-activities', UserActivityController::class)->name('users.activities');

            // Exports
            Route::get('members-export', MemberExportController::class)->name('members.export');
            Route::get('projects-export', ProjectExportController::class)->name('projects.export');
            Route::get('payments-export', PaymentExportController::class)->name('payments.export');
            Route::get('users-export', UserExportController::class)->name('users.export');
            Route::get('tickets-export', TicketExportController::class)->name('tickets.export');

            /**
             * Profile
             */
            Route::prefix('profile')->name('profile.')->group(function () {
                Route::get('/', [UserProfileController::class, 'edit'])->name('edit');
                Route::put('/', [UserProfileController::class, 'update'])->name('update');
            });

            /**
             * Settings
             */
            $settingsRoutes = [
                'general' => GeneralController::class,
                'share' => ShareController::class,
                'payment' => SettingsPaymentController::class,
                'notification' => NotificationController::class,
                'backup-security' => BackupSecurityController::class,
            ];

            foreach ($settingsRoutes as $uri => $controller) {
                Route::get("settings/{$uri}", [$controller, 'edit'])->name("settings.{$uri}.edit");
                Route::put("settings/{$uri}", [$controller, 'update'])->name("settings.{$uri}.update");
            }
        });

        // Checkout
        Route::get('checkout/{invoice}', [CheckoutController::class, 'create'])->name('checkout.create');
    });
});

/**
 * SSLCommerz Callback Routes (no CSRF)
 */
Route::name('client.payments.sslcommerz.')->group(function () {
    Route::match(['get', 'post'], '/sslcommerz/success/{invoice}', [SslcommerzPaymentController::class, 'success'])
        ->withoutMiddleware([VerifyCsrfToken::class])->name('success');

    Route::match(['get', 'post'], '/sslcommerz/fail/{invoice}', [SslcommerzPaymentController::class, 'fail'])
        ->withoutMiddleware([VerifyCsrfToken::class])->name('fail');

    Route::match(['get', 'post'], '/sslcommerz/cancel/{invoice}', [SslcommerzPaymentController::class, 'cancel'])
        ->withoutMiddleware([VerifyCsrfToken::class])->name('cancel');
});
