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

    /**
     * Validate whether a client can switch/downgrade to a given package based on resource limits.
     */
    public function validatePackageSwitch(Client $client, Package $package): ?string
    {
        $activePackage = $client->activeClientPackage?->package;

        if (! $activePackage) {
            return null;
        }

        if ($client->members()->count() > $package->member_limit) {
            return 'Cannot switch: the selected package allows fewer members than you currently have.';
        }

        if ($client->users()->count() > $package->user_limit) {
            return 'Cannot switch: the selected package allows fewer users than you currently have.';
        }

        if ($client->projects()->count() > $package->project_limit) {
            return 'Cannot switch: the selected package allows fewer projects than you currently have.';
        }

        return null;
    }
}
