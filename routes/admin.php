<?php

use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\ClientExportController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\InvoiceExportController;
use App\Http\Controllers\Admin\PakageController;
use App\Http\Controllers\Admin\PakageExportController;
use App\Http\Controllers\Admin\Settings\BackupSecurityController;
use App\Http\Controllers\Admin\Settings\EmailController;
use App\Http\Controllers\Admin\Settings\GeneralController;
use App\Http\Controllers\Admin\Settings\PaymentController;
use App\Http\Controllers\Admin\Settings\SmsController;
use App\Http\Controllers\Admin\Settings\SocialMediaController;
use App\Http\Controllers\Admin\TicketChatController;
use App\Http\Controllers\Admin\TicketController;
use App\Http\Controllers\Admin\User\ActivityController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UserProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {

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
     * Protected Admin Panel Routes
     * ------------------------------
     */
    Route::middleware('admin.auth')->group(function () {

        // Dashboard
        Route::get('/', DashboardController::class)->name('dashboard');

        /**
         * Resource Controllers
         */
        Route::resources([
            'clients' => ClientController::class,
            'pakages' => PakageController::class,
            'invoices' => InvoiceController::class,
            'tickets' => TicketController::class,
            'users' => UserController::class,
        ]);

        /**
         * Ticket Chats
         */
        Route::prefix('tickets/{ticket}')->name('tickets.')->group(function () {
            Route::get('chat', [TicketChatController::class, 'chat'])->name('chat');
            Route::post('message', [TicketChatController::class, 'storeMessage'])->name('message.store');
        });

        /**
         * Settings Management
         */
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('general', [GeneralController::class, 'edit'])->name('general.edit');
            Route::put('general', [GeneralController::class, 'update'])->name('general.update');

            Route::get('payments', [PaymentController::class, 'edit'])->name('payments.edit');
            Route::put('payments', [PaymentController::class, 'update'])->name('payments.update');

            Route::get('social-media', [SocialMediaController::class, 'edit'])->name('social_media.edit');
            Route::put('social-media', [SocialMediaController::class, 'update'])->name('social_media.update');

            Route::get('sms', [SmsController::class, 'edit'])->name('sms.edit');
            Route::put('sms', [SmsController::class, 'update'])->name('sms.update');

            Route::get('email', [EmailController::class, 'edit'])->name('email.edit');
            Route::put('email', [EmailController::class, 'update'])->name('email.update');

            Route::get('backup', [BackupSecurityController::class, 'edit'])->name('backup.edit');
            Route::put('backup', [BackupSecurityController::class, 'update'])->name('backup.update');
        });

        /**
         * Data Exports
         */
        Route::get('clients-export', ClientExportController::class)->name('clients.export');
        Route::get('pakages-export', PakageExportController::class)->name('pakages.export');
        Route::get('invoices-export', InvoiceExportController::class)->name('invoices.export');
        Route::get('user-activities', ActivityController::class)->name('user.activities');

        /**
         * Admin Profile
         */
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [UserProfileController::class, 'edit'])->name('edit');
            Route::put('/', [UserProfileController::class, 'update'])->name('update');
        });
    });
});
