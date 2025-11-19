<?php

namespace App\Domain\Clients\Actions;

use App\Domain\Clients\Models\Client;
use App\Domain\Packages\Models\Package;

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