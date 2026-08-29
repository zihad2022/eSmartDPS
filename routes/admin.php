<?php

use App\Http\Controllers\Admin\AdminBackupController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\ClientExportController;
use App\Http\Controllers\Admin\ClientImpersonateStartController;
use App\Http\Controllers\Admin\ClientImpersonateStopController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\InvoiceExportController;
use App\Http\Controllers\Admin\InvoiceSendToClientController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\PackageExportController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\Settings\BackupSecurityController;
use App\Http\Controllers\Admin\Settings\ContactInfoController;
use App\Http\Controllers\Admin\Settings\EmailController;
use App\Http\Controllers\Admin\Settings\GeneralController;
use App\Http\Controllers\Admin\Settings\PaymentController;
use App\Http\Controllers\Admin\Settings\SmsController;
use App\Http\Controllers\Admin\Settings\SocialMediaController;
use App\Http\Controllers\Admin\TicketChatController;
use App\Http\Controllers\Admin\TicketController;
use App\Http\Controllers\Admin\TicketExportController;
use App\Http\Controllers\Admin\User\ActivityController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UserExportController;
use App\Http\Controllers\Admin\UserProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {

    // Authentication Routes
    Route::controller(LoginController::class)->group(function () {
        Route::get('login', 'login')->name('login');
        Route::post('login', 'authenticate')->middleware('throttle:5,1')->name('authenticate');
        Route::post('logout', 'logout')->name('logout');
    });

    // Protected admin routes
    Route::middleware(['admin.auth', 'admin.session'])->group(function () {

        // Dashboard
        Route::get('/', DashboardController::class)
            ->name('dashboard')
            ->middleware('permission:view dashboard,admin');

        // Roles
        Route::prefix('roles')->name('roles.')->group(function () {
            Route::get('/', [RoleController::class, 'index'])->name('index')->middleware('permission:view roles,admin');
            Route::get('/create', [RoleController::class, 'create'])->name('create')->middleware('permission:create roles,admin');
            Route::post('/', [RoleController::class, 'store'])->name('store')->middleware('permission:create roles,admin');
            Route::get('/{role}', [RoleController::class, 'show'])->name('show')->middleware('permission:view roles,admin');
            Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('edit')->middleware('permission:edit roles,admin');
            Route::match(['put', 'patch'], '/{role}', [RoleController::class, 'update'])->name('update')->middleware('permission:edit roles,admin');
            Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy')->middleware('permission:delete roles,admin');
        });

        // Clients
        Route::prefix('clients')->name('clients.')->group(function () {
            Route::get('/', [ClientController::class, 'index'])->name('index')->middleware('permission:view clients,admin');
            Route::get('/create', [ClientController::class, 'create'])->name('create')->middleware('permission:create clients,admin');
            Route::post('/', [ClientController::class, 'store'])->name('store')->middleware('permission:create clients,admin');
            Route::get('/{client}', [ClientController::class, 'show'])->name('show')->middleware('permission:view clients,admin');
            Route::get('/{client}/edit', [ClientController::class, 'edit'])->name('edit')->middleware('permission:edit clients,admin');
            Route::match(['put', 'patch'], '/{client}', [ClientController::class, 'update'])->name('update')->middleware('permission:edit clients,admin');
            Route::delete('/{client}', [ClientController::class, 'destroy'])->name('destroy')->middleware('permission:delete clients,admin');
        });

        // Client Impersonation
        Route::post('/clients/{client}/impersonate', ClientImpersonateStartController::class)
            ->name('client.impersonate')
            ->middleware('permission:edit clients,admin');
        Route::post('/impersonate/stop', ClientImpersonateStopController::class)
            ->name('client.impersonate.stop');

        // Packages
        Route::prefix('packages')->name('packages.')->group(function () {
            Route::get('/', [PackageController::class, 'index'])->name('index')->middleware('permission:view packages,admin');
            Route::get('/create', [PackageController::class, 'create'])->name('create')->middleware('permission:create packages,admin');
            Route::post('/', [PackageController::class, 'store'])->name('store')->middleware('permission:create packages,admin');
            Route::get('/{package}', [PackageController::class, 'show'])->name('show')->middleware('permission:view packages,admin');
            Route::get('/{package}/edit', [PackageController::class, 'edit'])->name('edit')->middleware('permission:edit packages,admin');
            Route::match(['put', 'patch'], '/{package}', [PackageController::class, 'update'])->name('update')->middleware('permission:edit packages,admin');
            Route::delete('/{package}', [PackageController::class, 'destroy'])->name('destroy')->middleware('permission:delete packages,admin');
        });

        // Invoices
        Route::prefix('invoices')->name('invoices.')->group(function () {
            Route::get('/', [InvoiceController::class, 'index'])->name('index')->middleware('permission:view invoices,admin');
            Route::get('/create', [InvoiceController::class, 'create'])->name('create')->middleware('permission:create invoices,admin');
            Route::post('/', [InvoiceController::class, 'store'])->name('store')->middleware('permission:create invoices,admin');
            Route::get('/{invoice}', [InvoiceController::class, 'show'])->name('show')->middleware('permission:view invoices,admin');
            Route::get('/{invoice}/edit', [InvoiceController::class, 'edit'])->name('edit')->middleware('permission:edit invoices,admin');
            Route::match(['put', 'patch'], '/{invoice}', [InvoiceController::class, 'update'])->name('update')->middleware('permission:edit invoices,admin');
            Route::delete('/{invoice}', [InvoiceController::class, 'destroy'])->name('destroy')->middleware('permission:delete invoices,admin');
            Route::post('/{invoice}/send', InvoiceSendToClientController::class)
                ->name('send')
                ->middleware('permission:edit invoices,admin');
        });

        // Tickets
        Route::prefix('tickets')->name('tickets.')->group(function () {
            Route::get('/', [TicketController::class, 'index'])->name('index')->middleware('permission:view tickets,admin');
            Route::get('/create', [TicketController::class, 'create'])->name('create')->middleware('permission:create tickets,admin');
            Route::post('/', [TicketController::class, 'store'])->name('store')->middleware('permission:create tickets,admin');
            Route::get('/{ticket}', [TicketController::class, 'show'])->name('show')->middleware('permission:view tickets,admin');
            Route::get('/{ticket}/edit', [TicketController::class, 'edit'])->name('edit')->middleware('permission:edit tickets,admin');
            Route::match(['put', 'patch'], '/{ticket}', [TicketController::class, 'update'])->name('update')->middleware('permission:edit tickets,admin');
            Route::delete('/{ticket}', [TicketController::class, 'destroy'])->name('destroy')->middleware('permission:delete tickets,admin');

            // Ticket Chat
            Route::get('/{ticket}/chat', [TicketChatController::class, 'chat'])->name('chat')->middleware('permission:view ticket chats,admin');
        });

        // Users
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index')->middleware('permission:view users,admin');
            Route::get('/create', [UserController::class, 'create'])->name('create')->middleware('permission:create users,admin');
            Route::post('/', [UserController::class, 'store'])->name('store')->middleware('permission:create users,admin');
            Route::get('/{user}', [UserController::class, 'show'])->name('show')->middleware('permission:view users,admin');
            Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit')->middleware('permission:edit users,admin');
            Route::match(['put', 'patch'], '/{user}', [UserController::class, 'update'])->name('update')->middleware('permission:edit users,admin');
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy')->middleware('permission:delete users,admin');
        });

        // User Profile & Activities
        Route::get('users-activities', ActivityController::class)->name('users.activities')->middleware('permission:view user activities,admin');

        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [UserProfileController::class, 'edit'])->name('edit')->middleware('permission:view profile,admin');
            Route::put('/', [UserProfileController::class, 'update'])->name('update')->middleware('permission:edit profile,admin');
        });

        // Settings
        Route::prefix('settings')->name('settings.')->middleware('permission:view settings,admin')->group(function () {
            $settingsRoutes = [
                'general' => GeneralController::class,
                'payments' => PaymentController::class,
                'contact-info' => ContactInfoController::class,
                'social-media' => SocialMediaController::class,
                'sms' => SmsController::class,
                'email' => EmailController::class,
                'backup' => BackupSecurityController::class,
            ];

            foreach ($settingsRoutes as $uri => $controller) {
                Route::get($uri, [$controller, 'edit'])->name("$uri.edit");
                Route::put($uri, [$controller, 'update'])->name("$uri.update")->middleware('permission:edit settings,admin');
            }
        });

        Route::prefix('settings/backups')->name('settings.backups.')
            ->middleware('permission:edit settings,admin')
            ->group(function () {
                Route::post('/', [AdminBackupController::class, 'store'])->name('store');
                Route::get('/{backup}/download', [AdminBackupController::class, 'download'])->name('download');
                Route::delete('/{backup}', [AdminBackupController::class, 'destroy'])->name('destroy');
            });

        // Exports
        Route::get('clients-export', ClientExportController::class)->name('clients.export')->middleware('permission:export clients,admin');
        Route::get('packages-export', PackageExportController::class)->name('packages.export')->middleware('permission:export packages,admin');
        Route::get('invoices-export', InvoiceExportController::class)->name('invoices.export')->middleware('permission:export invoices,admin');
        Route::get('tickets-export', TicketExportController::class)->name('tickets.export')->middleware('permission:export tickets,admin');
        Route::get('users-export', UserExportController::class)->name('users.export')->middleware('permission:export users,admin');
    });
});
