<?php

use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\ClientsExportController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\PakageController;
use App\Http\Controllers\Admin\Settings\BackupSecurityController;
use App\Http\Controllers\Admin\Settings\EmailController;
use App\Http\Controllers\Admin\Settings\GeneralController;
use App\Http\Controllers\Admin\Settings\PaymentController;
use App\Http\Controllers\Admin\Settings\SmsController;
use App\Http\Controllers\Admin\TicketChatController;
use App\Http\Controllers\Admin\TicketController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UserProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {

    /** --------------------
     * 🔐 Authentication
     * -------------------- */
    Route::controller(LoginController::class)->group(function () {
        Route::get('login', 'login')->name('login');
        Route::post('login', 'authenticate')->name('authenticate');
        Route::post('logout', 'logout')->name('logout');
    });

    /** --------------------
     * 📊 Protected Admin Routes
     * -------------------- */
    Route::middleware('admin')->group(function () {

        // Dashboard
        Route::get('/', DashboardController::class)->name('dashboard');

        /** 📦 Resource Routes */
        Route::resources([
            'clients' => ClientController::class,
            'pakages' => PakageController::class,
            'invoices' => InvoiceController::class,
            'tickets' => TicketController::class,
            'users' => UserController::class,
        ]);

        /** 💬 Ticket Chats */
        Route::prefix('tickets/{ticket}')->name('tickets.')->group(function () {
            Route::get('chat', [TicketChatController::class, 'chat'])->name('chat');
            Route::post('message', [TicketChatController::class, 'storeMessage'])->name('message.store');
        });

        /** ⚙️ Settings */
        Route::prefix('settings')->name('settings.')->group(function () {
            // General
            Route::get('general', [GeneralController::class, 'edit'])->name('general.edit');
            Route::put('general', [GeneralController::class, 'update'])->name('general.update');
            // Shares
            // Route::get('shares', [ShareController::class, 'edit'])->name('shares.edit');
            // Route::put('shares', [ShareController::class, 'update'])->name('shares.update');
            // Payments
            Route::get('payments', [PaymentController::class, 'edit'])->name('payments.edit');
            Route::put('payments', [PaymentController::class, 'update'])->name('payments.update');
            // sms
            Route::get('sms', [SmsController::class, 'edit'])->name('sms.edit');
            Route::put('sms', [SmsController::class, 'update'])->name('sms.update');
            // email
            Route::get('email', [EmailController::class, 'edit'])->name('email.edit');
            Route::put('email', [EmailController::class, 'update'])->name('email.update');
            // Notifications
            // Route::get('notifications', [NotificationController::class, 'edit'])->name('notifications.edit');
            // Route::put('notifications', [NotificationController::class, 'update'])->name('notifications.update');
            // Backup & Security
            Route::get('backup', [BackupSecurityController::class, 'edit'])->name('backup.edit');
            Route::put('backup', [BackupSecurityController::class, 'update'])->name('backup.update');
        });

        /** 📤 Exports */
        Route::get('clients-export', ClientsExportController::class)->name('clients.export');

        /** 👤 Profile */
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [UserProfileController::class, 'edit'])->name('edit');
            Route::put('/', [UserProfileController::class, 'update'])->name('update');
        });
    });
});
