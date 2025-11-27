<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ClineRoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $client = Auth::guard('client')->user();

        if (!$client) {
            return redirect()->route('client.login');
        }

        // Super admin always allowed
        if ($client->role === 'super-admin') {
            return $next($request);
        }

        // Check if user role is in allowed roles
        if (!in_array($client->role, $roles)) {
            abort(403, 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}
