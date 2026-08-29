<?php

namespace App\Services;

use App\Actions\Client\Subscriptions\AssignTrialAction;
use App\Actions\Client\Subscriptions\RenewSubscriptionAction;
use App\Actions\Client\Subscriptions\StartPaidSubscriptionAction;
use App\Models\Client;
use App\Models\Package;

class PackageService
{
    public function __construct(
        protected AssignTrialAction $assignTrialAction,
        protected StartPaidSubscriptionAction $startPaidSubscriptionAction,
        protected RenewSubscriptionAction $renewSubscriptionAction
    ) {}

    /**
     * Start (trial or paid) package.
     */
    public function startPackage(Client $client, Package $package): string|bool
    {
        if ($package->has_trial && $package->trial_days > 0) {
            return $this->assignTrialAction->execute($client, $package);
        }

        $this->startPaidSubscriptionAction->execute($client, $package);

        return true;
    }

    /**
     * Renew subscription using Action
     */
    public function renewSubscription(Client $client, Package $package): void
    {
        $this->renewSubscriptionAction->execute($client, $package);
    }
}
