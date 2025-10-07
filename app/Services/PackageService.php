<?php

namespace App\Services;

use App\Enums\Package\BillingCycle;
use App\Models\Client;
use App\Models\Package;
use Illuminate\Support\Facades\Log;

class PackageService
{
    /**
     * Start a package for the given client.
     */
    public function startPackage(Client $client, Package $package): string|bool
    {
        if ($package->has_trial && $package->trial_days > 0) {
            return $this->assignTrial($package, $client);
        }

        $this->startPaidSubscription($client, $package);
        return true;
    }

    /**
     * Assign a trial package to the client.
     */
    public function assignTrial(Package $package, Client $client): string|bool
    {
        // Check if the client has already used a trial for this package
        $hasUsedTrial = $client->clientPackages()
            ->where('package_id', $package->id)
            ->where('is_trial', true)
            ->exists();

        if ($hasUsedTrial) {
            return 'You have already used the trial for this package.';
        }

        // Deactivate any existing active packages
        $client->clientPackages()->update(['is_active' => false]);

        // Create a new trial package
        $client->clientPackages()->create([
            'package_id' => $package->id,
            'starts_at'  => now(),
            'ends_at'    => now()->addMinutes(1), // temporary for testing
            'is_trial'   => true,
            'is_active'  => true,
        ]);

        return true;
    }

    /**
     * Start a paid subscription for the client.
     */
    public function startPaidSubscription(Client $client, Package $package): void
    {
        $client->clientPackages()->update(['is_active' => false]);

        $endsAt = match ($package->billing_cycle) {
            BillingCycle::MONTHLY => now()->addMonth(),
            BillingCycle::YEARLY  => now()->addYear(),
            default => now()->addDays(7),
        };

        $client->clientPackages()->create([
            'package_id' => $package->id,
            'starts_at'  => now(),
            'ends_at'    => $endsAt,
            'is_trial'   => false,
            'is_active'  => true,
        ]);
    }

    /**
     * Renew or switch the client’s package subscription.
     */
    public function renewSubscription(Client $client, Package $package): void
    {
        Log::info('Subscription renewal started', [
            'client_id' => $client->id,
            'package_id' => $package->id,
        ]);

        $lastActive = $client->clientPackages()
            ->where('is_active', true)
            ->latest('ends_at')
            ->first();

        $client->clientPackages()->where('is_active', true)->update(['is_active' => false]);

        // Determine the new subscription start date
        if ($lastActive && $lastActive->package_id === $package->id && $lastActive->ends_at) {
            $startDate = now()->greaterThan($lastActive->ends_at)
                ? now()
                : $lastActive->ends_at;
        } else {
            $startDate = now();
        }

        // Determine subscription end date
        $endsAt = match ($package->billing_cycle) {
            BillingCycle::MONTHLY => $startDate->copy()->addMonth(),
            BillingCycle::YEARLY  => $startDate->copy()->addYear(),
            default => $startDate->copy()->addDays(7),
        };

        // Create the new subscription record
        $subscription = $client->clientPackages()->create([
            'package_id' => $package->id,
            'starts_at'  => $startDate,
            'ends_at'    => $endsAt,
            'is_trial'   => false,
            'is_active'  => true,
        ]);

        Log::info('Subscription renewed successfully', [
            'subscription_id' => $subscription->id,
            'client_id'       => $client->id,
            'package_id'      => $package->id,
            'starts_at'       => $subscription->starts_at,
            'ends_at'         => $subscription->ends_at,
        ]);
    }
}
