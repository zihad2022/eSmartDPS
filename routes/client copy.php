<?php

use App\Http\Controllers\Admin\TicketExportController;
use App\Http\Controllers\Client\Auth\ForgotPasswordPhoneController;
use App\Http\Controllers\Client\Auth\LoginController;
use App\Http\Controllers\Client\Auth\OtpVerifyController;
use App\Http\Controllers\Client\Auth\RegisterController;
use App\Http\Controllers\Client\Auth\ResetPasswordPhoneController;
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
use App\Http\Controllers\Client\StartSubscriptionController;
use App\Http\Controllers\Client\SubscriptionController;
use App\Http\Controllers\Client\TicketChatController;
use App\Http\Controllers\Client\TicketController;
use App\Http\Controllers\Client\UserActivityController;
use App\Http\Controllers\Client\UserController;
use App\Http\Controllers\Client\UserExportController;
use App\Http\Controllers\Client\UserProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Client Routes
|--------------------------------------------------------------------------
| Organized and optimized routes for the client panel.
| - Authentication routes
| - Protected routes with 'client' middleware
| - Subscription check applied only where necessary
| - Clean grouping and readable comments
*/

Route::prefix('client')->name('client.')->group(function () {

    /** -------------------------
     * Client Authentication
     * -------------------------
     */
    Route::controller(LoginController::class)->group(function () {
        Route::get('login', 'login')->name('login');
        Route::post('login', 'authenticate')->name('authenticate');
        Route::post('logout', 'logout')->name('logout');

        Route::get('register', [RegisterController::class, 'create'])->name('register');
        Route::post('register', [RegisterController::class, 'store'])->name('register.store');
        Route::get('success/{id}', [RegisterController::class, 'success'])->name('auth.success');
        Route::get('forgot-password', [ForgotPasswordPhoneController::class, 'create'])->name('forgot.password.phone');
        Route::post('forgot-password', [ForgotPasswordPhoneController::class, 'store'])->name('forgot.password.phone.store');
        Route::get('reset-password', [ResetPasswordPhoneController::class, 'create'])->name('reset.password.phone');
        Route::post('reset-password', [ResetPasswordPhoneController::class, 'store'])->name('reset.password.phone.store');
        Route::get('otp-verify/{phone}', [OtpVerifyController::class, 'create'])->name('otp.verify');
        Route::post('otp-verify/{phone}', [OtpVerifyController::class, 'verify'])->name('otp.verify.post');
    });

    /** -------------------------
     * Protected Client Routes
     * -------------------------
     */
    Route::middleware('client')->group(function () {

        // Dashboard requires subscription
        Route::get('/', DashboardController::class)
            ->name('dashboard')
            ->middleware('subscription');

        /** -------------------------
         * Subscription Management
         * -------------------------
         */
        Route::prefix('subscription')->name('subscription.')->group(function () {
            // Expired page should NOT use subscription middleware to prevent redirect loops
            Route::get('expired', [SubscriptionController::class, 'expired'])->name('expired');
            Route::get('renew', [SubscriptionController::class, 'renew'])->name('renew');
            Route::get('packages', [SubscriptionController::class, 'packages'])->name('packages');
        });
        Route::resource('invoices', InvoiceController::class)->only(['index', 'show']);

        // Start subscription route
        Route::post('start-subscription', StartSubscriptionController::class)->name('start.subscription');

        /** -------------------------
         * Resource Controllers (Protected by Subscription)
         * -------------------------
         */
        Route::middleware('subscription')->group(function () {
            Route::resources([
                'members' => MemberController::class,
                'projects' => ProjectController::class,
                'project-categories' => ProjectCategoryController::class,
                'ledger-categories' => LedgerCategoryController::class,
                'ledgers' => LedgerController::class,
                'tickets' => TicketController::class,
            ]);

            // Ledger report
            Route::get('ledgers-report', LedgerReportController::class)->name('ledgers.report');

            // Ticket chats
            Route::prefix('tickets/{ticket}')->name('tickets.')->group(function () {
                Route::get('chat', [TicketChatController::class, 'chat'])->name('chat');
                Route::post('message', [TicketChatController::class, 'storeMessage'])->name('message.store');
            });

            // Payments (limited actions)
            Route::resource('payments', PaymentController::class)
                ->only(['index', 'edit', 'update', 'show', 'destroy']);

            // Users
            Route::resource('users', UserController::class)
                ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);

            // User activities
            Route::get('users-activities', UserActivityController::class)
                ->name('users.activities');

            // Data exports
            Route::get('members-export', MemberExportController::class)->name('members.export');
            Route::get('projects-export', ProjectExportController::class)->name('projects.export');
            Route::get('payments-export', PaymentExportController::class)->name('payments.export');
            Route::get('users-export', UserExportController::class)->name('users.export');
            Route::get('tickets-export', TicketExportController::class)->name('tickets.export');

            // Profile routes with permission middleware
            Route::prefix('profile')->name('profile.')->group(function () {
                Route::get('/', [UserProfileController::class, 'edit'])
                    ->name('edit')
                    ->middleware('permission:view profile,admin');
                Route::put('/', [UserProfileController::class, 'update'])
                    ->name('update')
                    ->middleware('permission:edit profile,admin');
            });

            /** -------------------------
             * Client Settings
             * -------------------------
             */
            Route::prefix('settings')->name('settings.')->group(function () {
                // General
                Route::get('general', [GeneralController::class, 'edit'])->name('general.edit');
                Route::put('general', [GeneralController::class, 'update'])->name('general.update');

                // Share
                Route::get('share', [ShareController::class, 'edit'])->name('share.edit');
                Route::put('share', [ShareController::class, 'update'])->name('share.update');

                // Payment
                Route::get('payment', [SettingsPaymentController::class, 'edit'])->name('payment.edit');
                Route::put('payment', [SettingsPaymentController::class, 'update'])->name('payment.update');

                // Notification
                Route::get('notification', [NotificationController::class, 'edit'])->name('notification.edit');
                Route::put('notification', [NotificationController::class, 'update'])->name('notification.update');

                // Backup & Security
                Route::get('backup-security', [BackupSecurityController::class, 'edit'])->name('backup-security.edit');
                Route::put('backup-security', [BackupSecurityController::class, 'update'])->name('backup-security.update');
            });
        });
    });
});

// Design route (testing / static page)
Route::get('design', fn() => view('design'))->name('design');
