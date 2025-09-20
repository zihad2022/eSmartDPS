<?php

namespace App\Services;

use App\Enums\Package\BillingCycle;
use App\Models\Client;
use App\Models\ClientPackage;
use App\Models\Package;
use Illuminate\Support\Facades\Log;

/**
 * Service class for handling package assignments and subscriptions for clients.
 *
 * Responsibilities:
 *  - Start a package (trial or paid) for a client.
 *  - Assign trial packages if available.
 *  - Renew or start paid subscriptions.
 */
class PackageService
{
    /**
     * Start a package for a given client.
     *
     * Logic:
     *  1. If the package has a trial and `trial_days > 0`, assign trial.
     *  2. Otherwise, start or renew a paid subscription.
     *
     * @param  Client  $client   The client receiving the package.
     * @param  Package $package  The package to assign.
     */
    public function startPackage(Client $client, Package $package): void
    {
        if ($package->has_trial === true && $package->trial_days > 0) {
            $this->assignTrial($package, $client);
        } else {
            $this->renewSubscription($client, $package);
        }
    }

    /**
     * Assign a trial package to a client.
     *
     * Steps:
     *  1. Ensure the client has not already used a trial for this package.
     *  2. Deactivate any currently active packages.
     *  3. Create a new `ClientPackage` record with trial details.
     *
     * @param  Package $package  The trial package to assign.
     * @param  Client  $client   The client receiving the trial.
     */
    public function assignTrial(Package $package, Client $client): void
    {
        // 1. Check if trial already used for this package
        $hasUsedTrial = $client->clientPackages()
            ->where('package_id', $package->id)
            ->where('is_trial', true)
            ->exists();

        if ($hasUsedTrial) {
            return; // Exit: Client has already used the trial
        }

        // 2. Deactivate all existing packages for this client
        $client->clientPackages()->update(['is_active' => false]);

        // 3. Assign new trial package with start/end dates
        $client->clientPackages()->create([
            'package_id' => $package->id,
            'starts_at' => now(),
            'ends_at' => now()->addDays($package->trial_days ?? 0),
            'is_trial' => true,
            'is_active' => true,
        ]);
    }

    /**
     * Start or renew a paid subscription package for a client.
     *
     * Steps:
     *  1. Deactivate any active packages for the client.
     *  2. Calculate new `ends_at` date based on billing cycle.
     *  3. Create a new active subscription record.
     *
     * @param  Client  $client   The client subscribing to the package.
     * @param  Package $package  The paid package to assign.
     */
    public function renewSubscription(Client $client, Package $package): void
    {
        // 1. Deactivate any existing subscriptions
        $client->clientPackages()->update(['is_active' => false]);

        // 2. Determine subscription end date from billing cycle
        $endsAt = match ($package->billing_cycle) {
            BillingCycle::MONTHLY => now()->addMonth(),
            BillingCycle::YEARLY  => now()->addYear(),
            default => now()->addDays(7), // fallback if billing cycle is unknown
        };

        // 3. Create new active subscription
        $client->clientPackages()->create([
            'package_id' => $package->id,
            'starts_at' => now(),
            // 'ends_at' => $endsAt,
            'ends_at' => now()->addMinutes(10),
            'is_trial' => false,
            'is_active' => true,
        ]);
    }
}
