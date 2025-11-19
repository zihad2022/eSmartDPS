<?php

namespace App\Domain\Clients\Services;

use App\Domain\Clients\Models\Client;
use App\Domain\Packages\Models\Package;

use App\Domain\Clients\Actions\AssignTrialAction;
use App\Domain\Clients\Actions\StartPaidSubscriptionAction;
use App\Domain\Clients\Actions\RenewSubscriptionAction;

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
