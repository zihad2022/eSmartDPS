<?php

namespace App\Services;

use App\Actions\Client\Subscriptions\ActivateSubscriptionFromInvoiceAction;
use App\Actions\Client\Subscriptions\AssignTrialAction;
use App\Actions\Client\Subscriptions\RenewSubscriptionAction;
use App\Actions\Client\Subscriptions\StartPaidSubscriptionAction;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Package;

class PackageService
{
    public function __construct(
        protected AssignTrialAction $assignTrialAction,
        protected StartPaidSubscriptionAction $startPaidSubscriptionAction,
        protected RenewSubscriptionAction $renewSubscriptionAction,
        protected ActivateSubscriptionFromInvoiceAction $activateSubscriptionFromInvoiceAction
    ) {}

    /**
     * Start (trial or paid) package.
     */
    public function startPackage(Client $client, Package $package): string|bool
    {
        if ($package->has_trial && $package->trial_days > 0) {
            return $this->assignTrialAction->execute($client, $package);
        }

        $this->startPaidSubscriptionAction->execute($client, $package);

        return true;
    }

    /**
     * Renew subscription using Action
     */
    public function renewSubscription(Client $client, Package $package): void
    {
        $this->renewSubscriptionAction->execute($client, $package);
    }

    /**
     * Activate/schedule the exact package period represented by a paid invoice.
     */
    public function activateSubscriptionFromInvoice(Client $client, Invoice $invoice): void
    {
        $this->activateSubscriptionFromInvoiceAction->execute($client, $invoice);
    }

    /**
     * Return the resource-limit issues that prevent a client from switching to a package.
     * A limit of 0 or null means unlimited.
     *
     * @return array<int, array{label:string,current:int,limit:int}>
     */
    public function packageSwitchIssues(Client $client, Package $package): array
    {
        // Renewing the same current/most-recent package is not a downgrade.
        $currentPackageId = ($client->activeClientPackage ?? $client->latestClientPackage)?->package_id;
        if ((int) $currentPackageId === (int) $package->id) {
            return [];
        }

        $usage = [
            'Members' => [
                'current' => $client->members()->count(),
                'limit' => $package->member_limit,
            ],
            'Users' => [
                'current' => $client->accountUserCount(),
                'limit' => $package->user_limit,
            ],
            'Projects' => [
                'current' => $client->projects()->count(),
                'limit' => $package->project_limit,
            ],
        ];

        $issues = [];

        foreach ($usage as $label => $values) {
            $limit = $values['limit'];

            // Package convention: null/0 = unlimited.
            if ($limit === null || (int) $limit === 0) {
                continue;
            }

            $current = (int) $values['current'];
            $limit = (int) $limit;

            if ($current > $limit) {
                $issues[] = [
                    'label' => $label,
                    'current' => $current,
                    'limit' => $limit,
                ];
            }
        }

        return $issues;
    }

    /**
     * Validate whether a client can switch/downgrade to a package.
     */
    public function validatePackageSwitch(Client $client, Package $package): ?string
    {
        $issues = $this->packageSwitchIssues($client, $package);

        if ($issues === []) {
            return null;
        }

        $details = collect($issues)
            ->map(fn (array $issue) => sprintf(
                '%s: %d in use, plan limit %d',
                $issue['label'],
                $issue['current'],
                $issue['limit']
            ))
            ->implode('; ');

        return "You cannot switch to {$package->name} because your current usage exceeds this plan's limits. {$details}. Please reduce your usage or choose a larger plan.";
    }
}
