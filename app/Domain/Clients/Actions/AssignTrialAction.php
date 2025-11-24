<?php

namespace App\Domain\Clients\Actions;

use App\Domain\Billing\Actions\CreateInvoiceAction;
use App\Domain\Clients\Models\Client;
use App\Domain\Packages\Models\Package;
use Illuminate\Support\Facades\Log;

class AssignTrialAction
{
    public function execute(Client $client, Package $package): string|bool
    {
        $hasUsedTrial = $client->clientPackages()
            ->where('package_id', $package->id)
            ->where('is_trial', true)
            ->exists();

        if ($hasUsedTrial) {
            return 'You have already used the trial for this package.';
        }

        $client->clientPackages()->update(['is_active' => false]);

        $client->clientPackages()->create([
            'package_id' => $package->id,
            'starts_at'  => now(),
            'ends_at'    => now()->addDays($package->trial_days),
            'is_trial'   => true,
            'is_active'  => true,
        ]);

        Log::info('store exect trial days');
        // app(CreateInvoiceAction::class)->execute($client, $package);

        return true;
    }
}
