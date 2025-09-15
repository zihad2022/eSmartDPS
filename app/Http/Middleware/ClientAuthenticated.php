<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ClientAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->guard('client')->check()) {
            return $next($request);
        } else {
            return redirect()->route('client.login');
        }
    }
}
