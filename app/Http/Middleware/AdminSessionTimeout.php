<?php

namespace App\Http\Middleware;

use App\Models\AdminSetting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AdminSessionTimeout
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::guard('admin')->check()) {
            return $next($request);
        }

        $timeoutMinutes = $this->timeoutMinutes();
        $lastActivity = (int) $request->session()->get('admin_last_activity', 0);

        if ($lastActivity > 0 && now()->timestamp - $lastActivity > $timeoutMinutes * 60) {
            Auth::guard('admin')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('admin.login')
                ->with('error', 'Your admin session expired due to inactivity. Please sign in again.');
        }

        $request->session()->put('admin_last_activity', now()->timestamp);

        return $next($request);
    }

    private function timeoutMinutes(): int
    {
        try {
            return (int) Cache::remember('admin.session_timeout_minutes', now()->addMinutes(5), fn (): int => max(5, (int) (AdminSetting::query()->value('session_timeout_minutes') ?: 30))
            );
        } catch (Throwable) {
            return 30;
        }
    }
}
