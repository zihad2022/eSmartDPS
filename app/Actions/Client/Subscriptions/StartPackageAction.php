<?php

namespace App\Actions\Client\Subscriptions;

use App\Models\Client;
use App\Models\Package;

class StartPackageAction
{
    public function execute(Client $client, Package $package): string|bool
    {
        if ($package->has_trial && $package->trial_days > 0) {
            return app(AssignTrialAction::class)->execute($client, $package);
        }

        app(StartPaidSubscriptionAction::class)->execute($client, $package);

        return true;
    }
}
