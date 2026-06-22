<?php

namespace App\Http\Middleware;

use App\Domain\Clients\Models\Client;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SubscriptionMiddleware
{
    public function handle(Request $request, Closure $next, ?string $feature = null)
    {
        // Get the owner client ID (custom helper in this project)
        $clientId = owner_client_id();
        $client = \App\Domain\Clients\Models\Client::find($clientId);

        if (! $client) {
            return redirect()->route('client.login');
        }

        $subscription = new \App\Services\SubscriptionService($client);

        // Global active check
        if (! $subscription->isActive()) {
            Log::info('[SubscriptionMiddleware] Access denied: subscription expired or inactive.', [
                'client_id' => $client->id,
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
            ]);

            return redirect()->route('client.subscription.expired');
        }

        // Specific feature check if provided
        if ($feature && ! $subscription->canAccessFeature($feature)) {
            Log::info('[SubscriptionMiddleware] Feature access denied.', [
                'client_id' => $client->id,
                'feature' => $feature,
            ]);

            abort(403, 'Your current package does not include this feature.');
        }

        return $next($request);
    }
}
