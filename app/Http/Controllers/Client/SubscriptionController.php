<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Services\PackageService;

class SubscriptionController extends Controller
{
    public function expired()
    {
        return view('client.subscription.expired');
    }

    public function renew(PackageService $packageService)
    {
        $client = auth('client')->user();
        $lastSubscription = $client->lastSubscription?->load('subscription');

        if (! $lastSubscription || ! $lastSubscription->subscription) {
            abort(404, 'No valid subscription to renew.');
        }

        $packageService->renewSubscription($client, $lastSubscription->subscription);

        return redirect()->route('client.dashboard');
    }

    public function packages()
    {
        $packages = Package::active()->get();

        return view('client.subscription.packages', compact('packages'));
    }
}
