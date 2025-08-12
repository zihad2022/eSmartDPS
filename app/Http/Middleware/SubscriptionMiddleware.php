<?php

namespace App\Http\Middleware;

use App\Models\Client;
use Closure;

class SubscriptionMiddleware
{
    // public function handle($request, Closure $next)
    // {
    //     $client = auth('client')->user();

    //     if (! $client) {
    //         return redirect()->route('client.login');
    //     }

    //     if ($client->activeTrialClientSubscription) {
    //         return $next($request);
    //     }

    //     if ($client->activePaidClientSubscription) {
    //         return $next($request);
    //     }

    //     return redirect()->route('client.subscription.expired');
    // }

    public function handle($request, Closure $next)
    {
        // Get the main client (parent)
        $client = Client::find(owner_client_id());

        if (! $client) {
            return redirect()->route('client.login');
        }

        $trial = $client->activeTrialClientPackage;
        $paid = $client->activePaidClientPackage;

        if ($trial && $trial->ends_at->isFuture()) {
            return $next($request);
        }

        if ($paid && $paid->ends_at->isFuture()) {
            return $next($request);
        }

        return redirect()->route('client.subscription.expired');
    }
}
