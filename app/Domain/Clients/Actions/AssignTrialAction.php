<?php

namespace App\Domain\Clients\Actions;

use App\Domain\Invoices\Actions\CreateInvoiceAction;
use App\Domain\Clients\Models\Client;
use App\Domain\Clients\Models\ClientPackage;
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

        $client->clientPackages()->where('is_active', true)->update([
            'is_active' => false,
            'status' => ClientPackage::STATUS_CANCELLED,
        ]);

        $client->clientPackages()->create([
            'package_id' => $package->id,
            'starts_at'  => now(),
            'ends_at'    => now()->addDays($package->trial_days),
            'is_trial'   => true,
            'is_active'  => true,
        ]);

        Log::info('Trial package assigned.', [
            'client_id' => $client->id,
            'package_id' => $package->id,
            'trial_days' => $package->trial_days,
        ]);
        // app(CreateInvoiceAction::class)->execute($client, $package);

        return true;
    }
}
