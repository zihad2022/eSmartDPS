<?php

namespace App\Services;

use App\Models\Client;
use App\Models\ClientPakage;
use App\Models\Pakage;

class PakageService
{
    /**
     * Start a package for a client.
     * - If the package has a trial and trial_days > 0 → Assign trial.
     * - Otherwise → Start or renew a paid subscription.
     */
    public function startPakage(Client $client, Pakage $pakage): void
    {
        if ($pakage->has_trial && $pakage->trial_days > 0) {
            $this->assignTrial($pakage, $client);
        } else {
            $this->renewSubscription($client, $pakage);
        }
    }

    /**
     * Assign a trial package to a client.
     *
     * Steps:
     *  1. Check if the client has already used the trial for this package.
     *  2. If not used, create a ClientPakage record with trial details.
     *  3. The trial is marked as active and will expire after trial_days.
     */
    public function assignTrial(Pakage $pakage, Client $client): void
    {
        // Check if client already used the trial for this package
        $hasUsedTrial = ClientPakage::where('client_id', $client->id)
            ->where('pakage_id', $pakage->id)
            ->where('is_trial', true)
            ->exists();

        // If already used, do nothing
        if ($hasUsedTrial) {
            return;
        }

        // Assign trial
        ClientPakage::create([
            'client_id' => $client->id,
            'pakage_id' => $pakage->id,
            'starts_at' => now(),
            'ends_at' => now()->addDays($pakage->trial_days ?? 0),
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
     *  3. Create a new ClientPakage record as active.
     */
    public function renewSubscription(Client $client, Pakage $pakage): void
    {
        // Deactivate all previous subscriptions for this client
        $client->ClientPakage()->update(['is_active' => false]);

        // Determine end date based on billing cycle
        $endsAt = match ($pakage->billing_cycle) {
            'yearly' => now()->addYear(),
            'monthly' => now()->addMonth(),
            default => now()->addMonth(),
        };

        // Create a new active subscription
        ClientPakage::create([
            'client_id' => $client->id,
            'pakage_id' => $pakage->id,
            'starts_at' => now(),
            'ends_at' => $endsAt,
            'is_trial' => false,
            'is_active' => true,
        ]);
    }
}
