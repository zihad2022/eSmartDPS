<?php

use App\Http\Middleware\AdminAuthenticated;
use App\Http\Middleware\AdminSessionTimeout;
use App\Http\Middleware\ClientAuthenticated;
use App\Http\Middleware\ClineRoleMiddleware;
use App\Http\Middleware\MemberAuthenticated;
use App\Http\Middleware\SubscriptionMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin.auth' => AdminAuthenticated::class,
            'admin.session' => AdminSessionTimeout::class,
            'member' => MemberAuthenticated::class,
            'client' => ClientAuthenticated::class,
            'client.role' => ClineRoleMiddleware::class,
            'subscription' => SubscriptionMiddleware::class,

            // Role and Permission
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'sslcommerz/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
