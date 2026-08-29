<?php

namespace App\Actions\Client\Subscriptions;

use App\Models\Client;
use App\Models\Package;

class StartPaidSubscriptionAction extends BaseSubscriptionAction
{
    public function execute(Client $client, Package $package)
    {
        $this->deactivateActiveSubscriptions($client);

        $startsAt = now();
        $endsAt = $this->calculateEndDate($package->billing_cycle, $startsAt);

        return $this->createSubscription($client, $package, $startsAt, $endsAt, false);
    }
}
