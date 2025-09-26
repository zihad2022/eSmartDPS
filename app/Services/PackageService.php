<?php

namespace App\Services;

use App\Enums\Package\BillingCycle;
use App\Models\Client;
use App\Models\Package;
use Illuminate\Support\Facades\Log;

/**
 * Service class for handling package assignments and subscriptions for clients.
 *
 * Responsibilities:
 *  - Assign trial packages if available.
 *  - Start new paid subscriptions.
 *  - Renew expired subscriptions.
 */
class PackageService
{
    /**
     * Start a package for a given client.
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
     * Assign a trial package to a client.
     */
    public function assignTrial(Package $package, Client $client): string|bool
    {
        // -----------------------------
        // 1. Check if trial already used
        // -----------------------------
        $hasUsedTrial = $client->clientPackages()
            ->where('package_id', $package->id)
            ->where('is_trial', true)
            ->exists();

        if ($hasUsedTrial) {
            return 'You have already used the trial for this package.';
        }

        // -----------------------------
        // 2. Deactivate existing packages
        // -----------------------------
        $client->clientPackages()->update(['is_active' => false]);

        // -----------------------------
        // 3. Assign trial package
        // -----------------------------
        $client->clientPackages()->create([
            'package_id' => $package->id,
            'starts_at'  => now(),
            'ends_at'    => now()->addDays($package->trial_days ?? 0),
            'is_trial'   => true,
            'is_active'  => true,
        ]);

        return true;
    }

    /**
     * Start a new paid subscription for a client.
     */
    public function startPaidSubscription(Client $client, Package $package): void
    {
        // -----------------------------
        // 1. Deactivate existing packages
        // -----------------------------
        $client->clientPackages()->update(['is_active' => false]);

        // -----------------------------
        // 2. Calculate subscription end date
        // -----------------------------
        $endsAt = match ($package->billing_cycle) {
            BillingCycle::MONTHLY => now()->addMonth(),
            BillingCycle::YEARLY  => now()->addYear(),
            default => now()->addDays(7), // fallback
        };

        // -----------------------------
        // 3. Create new subscription
        // -----------------------------
        $client->clientPackages()->create([
            'package_id' => $package->id,
            'starts_at'  => now(),
            'ends_at'    => $endsAt,
            'is_trial'   => false,
            'is_active'  => true,
        ]);
    }

    /**
     * Renew an expired package for a client.
     */
    // public function renewSubscription(Client $client, Package $package): void
    // {
    //     // -----------------------------
    //     // 1. Find last subscription
    //     // -----------------------------
    //     $lastSubscription = $client->clientPackages()
    //         ->where('package_id', $package->id)
    //         ->latest('ends_at')
    //         ->first();

    //     // -----------------------------
    //     // 2. Decide new start date
    //     // -----------------------------
    //     if ($lastSubscription && $lastSubscription->ends_at) {
    //         $startDate = now()->greaterThan($lastSubscription->ends_at)
    //             ? now()
    //             : $lastSubscription->ends_at;
    //     } else {
    //         $startDate = now();
    //     }

    //     // -----------------------------
    //     // 3. Calculate new end date
    //     // -----------------------------
    //     $endsAt = match ($package->billing_cycle) {
    //         BillingCycle::MONTHLY => $startDate->copy()->addMonth(),
    //         BillingCycle::YEARLY  => $startDate->copy()->addYear(),
    //         default => $startDate->copy()->addDays(7),
    //     };

    //     // -----------------------------
    //     // 4. Create renewed subscription
    //     // -----------------------------
    //     $client->clientPackages()->create([
    //         'package_id' => $package->id,
    //         'starts_at'  => $startDate,
    //         'ends_at'    => $endsAt,
    //         'is_trial'   => false,
    //         'is_active'  => true,
    //     ]);
    // }
    public function renewSubscription(Client $client, Package $package): void
    {
        Log::info('🔄 Subscription start/renew initiated', [
            'client_id' => $client->id,
            'package_id' => $package->id,
        ]);
    
        // -----------------------------
        // 1. Find last active subscription (any package)
        // -----------------------------
        $lastActive = $client->clientPackages()
            ->where('is_active', true)
            ->latest('ends_at')
            ->first();
    
        // -----------------------------
        // 2. Deactivate currently active subscriptions
        // -----------------------------
        $client->clientPackages()
            ->where('is_active', true)
            ->update(['is_active' => false]);
    
        Log::info('📦 Last active subscription found', [
            'exists'   => (bool) $lastActive,
            'package_id' => $lastActive?->package_id,
            'ends_at'  => $lastActive?->ends_at,
        ]);
    
        // -----------------------------
        // 3. Decide new start date
        // -----------------------------
        if ($lastActive && $lastActive->package_id === $package->id && $lastActive->ends_at) {
            // Renewal → continue from last end date
            $startDate = now()->greaterThan($lastActive->ends_at)
                ? now()
                : $lastActive->ends_at;
    
            Log::info('🕒 Renewal detected - start date decided', [
                'now'       => now(),
                'last_end'  => $lastActive->ends_at,
                'startDate' => $startDate,
            ]);
        } else {
            // Switch → always start fresh
            $startDate = now();
    
            Log::info('🕒 Package switch detected - start date reset to now', [
                'startDate' => $startDate,
            ]);
        }
    
        // -----------------------------
        // 4. Calculate new end date
        // -----------------------------
        $endsAt = match ($package->billing_cycle) {
            BillingCycle::MONTHLY => $startDate->copy()->addMonth(),
            BillingCycle::YEARLY  => $startDate->copy()->addYear(),
            default => $startDate->copy()->addDays(7),
        };
    
        Log::info('📅 End date calculated', [
            'billing_cycle' => $package->billing_cycle,
            'endsAt'        => $endsAt,
        ]);
    
        // -----------------------------
        // 5. Create subscription
        // -----------------------------
        $subscription = $client->clientPackages()->create([
            'package_id' => $package->id,
            'starts_at'  => $startDate,
            'ends_at'    => $endsAt,
            'is_trial'   => false,
            'is_active'  => true,
        ]);
    
        Log::info('✅ Subscription created successfully', [
            'subscription_id' => $subscription->id,
            'client_id'       => $client->id,
            'package_id'      => $package->id,
            'starts_at'       => $subscription->starts_at,
            'ends_at'         => $subscription->ends_at,
        ]);
    }
    
}
