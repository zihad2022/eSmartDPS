<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckClientRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = auth('client')->user();

        if (! $user || ! in_array($user->role, $roles)) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
