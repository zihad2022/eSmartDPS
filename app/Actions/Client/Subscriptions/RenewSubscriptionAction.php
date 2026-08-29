<?php

namespace App\Actions\Client\Subscriptions;

use App\Models\Client;
use App\Models\Package;
use Carbon\Carbon;

class RenewSubscriptionAction extends BaseSubscriptionAction
{
    public function execute(Client $client, Package $package)
    {
        $lastActive = $this->getLastActiveSubscription($client, $package);

        $startsAt = $this->determineStartDate($lastActive);

        $this->deactivateActiveSubscriptions($client);

        $endsAt = $this->calculateEndDate($package->billing_cycle, $startsAt);

        return $this->createSubscription($client, $package, $startsAt, $endsAt, false);
    }

    private function getLastActiveSubscription(Client $client, Package $package)
    {
        return $client->clientPackages()
            ->where('is_active', true)
            ->where('package_id', $package->id)
            ->latest('ends_at')
            ->first();
    }

    private function determineStartDate($lastActive): Carbon
    {
        if ($lastActive && $lastActive->ends_at) {
            return now()->greaterThan($lastActive->ends_at)
                ? now()
                : $lastActive->ends_at;
        }

        return now();
    }
}
