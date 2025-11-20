<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        // i need logic like i have status field on admins tbale so if status is false than logout the admin
        if (auth()->guard('admin')->check()) {
            if (auth()->guard('admin')->user()->status == false) {
                auth()->guard('admin')->logout();
                return redirect()->route('admin.login');
            }
            return $next($request);
        } else {
            return redirect()->route('admin.login');
        }
    }
}
