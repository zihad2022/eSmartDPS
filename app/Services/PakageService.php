<?php

namespace App\Services;

use App\Models\Client;
use App\Models\ClientPakage;
use App\Models\Pakage;

class PakageService
{
    /**
     * Assign trial or paid pakage based on the plan.
     */
    public function startPakage(Client $client, Pakage $pakage): void
    {
        if ($pakage->has_trial == true && $pakage->trial_days > 0) {
            $this->assignTrial($pakage, $client);
        } else {
            $this->renewSubscription($client, $pakage);
        }
    }

    /**
     * Assign a trial pakage to a client.
     */
    public function assignTrial(Pakage $pakage, Client $client): void
    {
        $hasUsedTrial = ClientPakage::where('client_id', $client->id)
            ->where('pakage_id', $pakage->id)
            ->where('is_trial', true)
            ->exists();

        if ($hasUsedTrial) {
            return;
        }

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
     * Start or renew a paid pakage (disables all previous).
     */
    public function renewSubscription(Client $client, Pakage $pakage): void
    {
        // Deactivate previous pakages
        $client->ClientPakage()->update(['is_active' => false]);

        // Calculate new end date based on billing cycle
        $endsAt = match ($pakage->billing_cycle) {
            'yearly' => now()->addYear(),
            'monthly' => now()->addMonth(),
            default => now()->addMonth(),
        };

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
