<?php

namespace App\Services;

use App\Models\Client;
use App\Models\ClientPackage;

class SubscriptionService
{
    protected ?Client $client;

    protected ?ClientPackage $subscription;

    public function __construct(?Client $client = null)
    {
        $this->client = $client;
        $this->subscription = $client ? $client->currentSubscription() : null;
    }

    /**
     * Check if the client has an active subscription.
     */
    public function isActive(): bool
    {
        return ! is_null($this->subscription) && $this->subscription->isActive();
    }

    /**
     * Check if the current subscription allows access to a specific feature.
     */
    public function canAccessFeature(string $featureSlug): bool
    {
        if (! $this->isActive()) {
            return false;
        }

        return $this->subscription->package->hasFeature($featureSlug);
    }

    /**
     * Check if a resource limit has been reached.
     */
    public function isLimitReached(string $limitField, int $currentCount): bool
    {
        if (! $this->isActive()) {
            return true;
        }

        $limit = $this->subscription->package->{$limitField};

        // If limit is 0 or null, it's unlimited
        if (is_null($limit) || $limit == 0) {
            return false;
        }

        return $currentCount >= $limit;
    }

    /**
     * Get the remaining days of the subscription.
     */
    public function getRemainingDays(): int
    {
        if (! $this->subscription) {
            return 0;
        }

        return (int) max(0, now()->diffInDays($this->subscription->ends_at, false));
    }
}
