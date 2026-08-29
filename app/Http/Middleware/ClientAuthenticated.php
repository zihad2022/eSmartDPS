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
            if (auth()->guard('client')->user()->status == false) {
                auth()->guard('client')->logout();

                return redirect()->route('client.login');
            }

            return $next($request);
        } else {
            return redirect()->route('client.login');
        }
    }
}
