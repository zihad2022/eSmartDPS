<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as VerifyCsrfToken;
use App\Http\Controllers\Client\Auth\{
    ForgotPasswordPhoneController,
    LoginController,
    OtpVerifyController,
    RegisterController,
    ResetPasswordPhoneController
};
use App\Http\Controllers\Client\{
    BkashPaymentController,
    CheckoutController,
    DashboardController,
    InvoiceController,
    LedgerCategoryController,
    LedgerController,
    LedgerReportController,
    MemberController,
    MemberExportController,
    PaymentController,
    PaymentExportController,
    ProjectCategoryController,
    ProjectController,
    ProjectExportController,
    Settings\BackupSecurityController,
    Settings\GeneralController,
    Settings\NotificationController,
    Settings\PaymentController as SettingsPaymentController,
    Settings\ShareController,
    SslcommerzPaymentController,
    StartPaidSubscriptionController,
    StartTrailSubscriptionController,
    SubscriptionController,
    SubscriptionPaymentController,
    TicketChatController,
    TicketController,
    TicketExportController,
    UserActivityController,
    UserController,
    UserExportController,
    UserProfileController
};

/*
|--------------------------------------------------------------------------
| Client Routes
|--------------------------------------------------------------------------
*/

Route::prefix('client')->name('client.')->group(function () {

    /**
     * -------------------------
     * Authentication
     * -------------------------
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
     * -------------------------
     * OTP & Password Reset
     * -------------------------
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
     * -------------------------
     * Protected Client Routes
     * -------------------------
     */
    Route::middleware('client')->group(function () {

        // Dashboard (requires active subscription)
        Route::get('/', DashboardController::class)->name('dashboard')->middleware('subscription');

        /**
         * -------------------------
         * Subscription Management
         * -------------------------
         */
        Route::prefix('subscription')->name('subscription.')->group(function () {
            Route::get('expired', [SubscriptionController::class, 'expired'])->name('expired');
            Route::get('renew/{invoice?}', [SubscriptionController::class, 'renew'])->name('renew');
            Route::get('packages', [SubscriptionController::class, 'packages'])->name('packages');

            Route::get('start-trial/{package}', StartTrailSubscriptionController::class)->name('start.trial');
            Route::get('start-paid/{package}', StartPaidSubscriptionController::class)->name('start.paid');
        });

        /**
         * -------------------------
         * Payments & Invoices
         * -------------------------
         */
        Route::prefix('payments')->name('payments.')->group(function () {
            Route::get('select/{invoice}', [SubscriptionPaymentController::class, 'selectMethod'])->name('select');
            Route::post('process/{invoice}', [SubscriptionPaymentController::class, 'processPayment'])->name('process');

            // bKash callback
            Route::match(['get', 'post'], 'bkash/callback', [BkashPaymentController::class, 'callback'])->name('bkash.callback');

            // SSLCommerz
            Route::get('sslcommerz/pay/{invoice}', [SslcommerzPaymentController::class, 'pay'])->name('sslcommerz.pay');
        });

        // Invoices (read-only)
        Route::resource('invoices', InvoiceController::class)->only(['index', 'show']);

        /**
         * -------------------------
         * Resource Management (requires active subscription)
         * -------------------------
         */
        Route::middleware('subscription')->group(function () {
            Route::resources([
                'members'            => MemberController::class,
                'projects'           => ProjectController::class,
                'project-categories' => ProjectCategoryController::class,
                'ledger-categories'  => LedgerCategoryController::class,
                'ledgers'            => LedgerController::class,
                'tickets'            => TicketController::class,
            ]);

            // Additional routes
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
             * -------------------------
             * Profile
             * -------------------------
             */
            Route::prefix('profile')->name('profile.')->group(function () {
                Route::get('/', [UserProfileController::class, 'edit'])
                    ->name('edit')
                    ->middleware('permission:view profile,admin');
                Route::put('/', [UserProfileController::class, 'update'])
                    ->name('update')
                    ->middleware('permission:edit profile,admin');
            });

            /**
             * -------------------------
             * Settings
             * -------------------------
             */
            Route::prefix('settings')->name('settings.')->group(function () {
                Route::get('general', [GeneralController::class, 'edit'])->name('general.edit');
                Route::put('general', [GeneralController::class, 'update'])->name('general.update');

                Route::get('share', [ShareController::class, 'edit'])->name('share.edit');
                Route::put('share', [ShareController::class, 'update'])->name('share.update');

                Route::get('payment', [SettingsPaymentController::class, 'edit'])->name('payment.edit');
                Route::put('payment', [SettingsPaymentController::class, 'update'])->name('payment.update');

                Route::get('notification', [NotificationController::class, 'edit'])->name('notification.edit');
                Route::put('notification', [NotificationController::class, 'update'])->name('notification.update');

                Route::get('backup-security', [BackupSecurityController::class, 'edit'])->name('backup-security.edit');
                Route::put('backup-security', [BackupSecurityController::class, 'update'])->name('backup-security.update');
            });
        });

        // Checkout 
        Route::get('checkout/{invoice}', [CheckoutController::class, 'create'])->name('checkout.create');
    });
});

/**
 * -------------------------
 * SSLCommerz Callback Routes
 * -------------------------
 * Grouped by route name only to avoid changing public callback URLs.
 */
Route::name('client.payments.sslcommerz.')->group(function () {
    Route::match(['get', 'post'], '/sslcommerz/success/{invoice}', [SslcommerzPaymentController::class, 'success'])
        ->withoutMiddleware([VerifyCsrfToken::class])
        ->name('success');

    Route::match(['get', 'post'], '/sslcommerz/fail/{invoice}', [SslcommerzPaymentController::class, 'fail'])
        ->withoutMiddleware([VerifyCsrfToken::class])
        ->name('fail');

    Route::match(['get', 'post'], '/sslcommerz/cancel/{invoice}', [SslcommerzPaymentController::class, 'cancel'])
        ->withoutMiddleware([VerifyCsrfToken::class])
        ->name('cancel');
});
