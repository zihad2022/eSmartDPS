<?php

namespace App\Domain\Clients\Actions;

use App\Domain\Clients\Models\Client;
use App\Domain\Packages\Models\Package;
use App\Enums\Package\BillingCycle;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

abstract class BaseSubscriptionAction
{
    /**
     * Calculate subscription end date
     */
    protected function calculateEndDate(BillingCycle $billingCycle, Carbon $startDate): Carbon
    {
        return match ($billingCycle) {
            BillingCycle::MONTHLY => $startDate->copy()->addMonth(),
            BillingCycle::YEARLY  => $startDate->copy()->addYear(),
            default => $startDate->copy()->addDays(7),
        };
    }

    /**
     * Deactivate all active subscriptions
     */
    protected function deactivateActiveSubscriptions(Client $client): void
    {
        $client->clientPackages()
            ->where('is_active', true)
            ->update(['is_active' => false]);
    }

    /**
     * Create a new subscription
     */
    protected function createSubscription(
        Client $client,
        Package $package,
        Carbon $startsAt,
        Carbon $endsAt,
        bool $isTrial = false
    ) {
        return $client->clientPackages()->create([
            'package_id' => $package->id,
            'starts_at'  => $startsAt,
            'ends_at'    => $endsAt,
            'is_trial'   => $isTrial,
            'is_active'  => true,
        ]);
    }
}
