<?php

namespace App\Domain\Clients\Actions;

use App\Domain\Clients\Models\Client;
use App\Domain\Packages\Models\Package;
use Carbon\Carbon;

class StartPaidSubscriptionAction extends BaseSubscriptionAction
{
    public function execute(Client $client, Package $package)
    {
        $this->deactivateActiveSubscriptions($client);

        $startsAt = now();
        $endsAt   = $this->calculateEndDate($package->billing_cycle, $startsAt);

        // For testing only — remove later
        // $endsAt = now()->addMinutes(1);

        return $this->createSubscription($client, $package, $startsAt, $endsAt, false);
    }
}
