<?php

namespace App\Http\Middleware;

use App\Domain\Clients\Models\Client;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SubscriptionMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // 1️⃣ Get the authenticated client or fallback to parent
        $client = Client::find(owner_client_id());

        if (! $client) {
            Log::warning('[SubscriptionMiddleware] Unauthorized access: no client found.', [
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
            ]);
            return redirect()->route('client.login');
        }

        $now = now();

        // 2️⃣ Check active trial package
        $trial = $client->activeClientPackage()
            ->where('is_trial', true)
            ->where('ends_at', '>', $now)
            ->first();

        if ($trial) {
            return $next($request);
        }

        // 3️⃣ Check active paid package
        $paid = $client->activeClientPackage()
            ->where('is_trial', false)
            ->where('ends_at', '>', $now)
            ->first();

        if ($paid) {
            return $next($request);
        }

        // 4️⃣ Subscription expired
        Log::info('[SubscriptionMiddleware] Access denied: subscription expired.', [
            'client_id' => $client->id,
            'ip' => $request->ip(),
            'url' => $request->fullUrl(),
        ]);

        return redirect()->route('client.subscription.expired');
    }
}
