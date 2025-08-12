<?php

namespace App\Services;

use App\Models\Client;
use App\Models\ClientPackage;
use App\Models\Package;

class PackageService
{
    /**
     * Start a package for a client.
     * - If the package has a trial and trial_days > 0 → Assign trial.
     * - Otherwise → Start or renew a paid subscription.
     */
    public function startPackage(Client $client, Package $package): void
    {
        if ($package->has_trial && $package->trial_days > 0) {
            $this->assignTrial($package, $client);
        } else {
            $this->renewSubscription($client, $package);
        }
    }

    /**
     * Assign a trial package to a client.
     *
     * Steps:
     *  1. Check if the client has already used the trial for this package.
     *  2. If not used, create a ClientPackage record with trial details.
     *  3. The trial is marked as active and will expire after trial_days.
     */
    public function assignTrial(Package $package, Client $client): void
    {
        // Check if client already used the trial for this package
        $hasUsedTrial = ClientPackage::where('client_id', $client->id)
            ->where('package_id', $package->id)
            ->where('is_trial', true)
            ->exists();

        // If already used, do nothing
        if ($hasUsedTrial) {
            return;
        }

        // Assign trial
        ClientPackage::create([
            'client_id' => $client->id,
            'package_id' => $package->id,
            'starts_at' => now(),
            'ends_at' => now()->addDays($package->trial_days ?? 0),
            'is_trial' => true,
            'is_active' => true,
        ]);
    }

    /**
     * Start or renew a paid package subscription.
     *
     * Steps:
     *  1. Deactivate all previous packages for the client.
     *  2. Determine the new end date based on the billing cycle.
     *  3. Create a new ClientPackage record as active.
     */
    public function renewSubscription(Client $client, Package $package): void
    {
        // Deactivate all previous subscriptions for this client
        $client->ClientPackage()->update(['is_active' => false]);

        // Determine end date based on billing cycle
        $endsAt = match ($package->billing_cycle) {
            'yearly' => now()->addYear(),
            'monthly' => now()->addMonth(),
            default => now()->addMonth(),
        };

        // Create a new active subscription
        ClientPackage::create([
            'client_id' => $client->id,
            'package_id' => $package->id,
            'starts_at' => now(),
            'ends_at' => $endsAt,
            'is_trial' => false,
            'is_active' => true,
        ]);
    }
}
