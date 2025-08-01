<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MemberAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->guard('member')->check()) {
            return $next($request);
        } else {
            return redirect()->route('landing');
        }
    }
}
