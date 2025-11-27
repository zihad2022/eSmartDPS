<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin.auth' => App\Http\Middleware\AdminAuthenticated::class,
            'member' => App\Http\Middleware\MemberAuthenticated::class,
            'client' => App\Http\Middleware\ClientAuthenticated::class,
            'client.role' => App\Http\Middleware\ClineRoleMiddleware::class,
            'subscription' => App\Http\Middleware\SubscriptionMiddleware::class,

            // Role and Permission
            'role' => Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);

         $middleware->validateCsrfTokens(except: [
            'sslcommerz/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
