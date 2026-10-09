<?php

namespace App\Actions\Client\Subscriptions;

use App\Models\Client;
use App\Models\ClientPackage;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ActivateSubscriptionFromInvoiceAction
{
    /**
     * Activate or schedule the subscription represented by a paid invoice.
     *
     * Renewal invoices may be paid before the current subscription expires. In
     * that case the next period is created as a future subscription and the
     * current subscription is left untouched until its original end date.
     */
    public function execute(Client $client, Invoice $invoice): ClientPackage
    {
        $package = $invoice->package;

        if (! $package) {
            throw new \RuntimeException('The invoice package no longer exists.');
        }

        $startsAt = Carbon::parse($invoice->billing_start)->startOfDay();
        $endsAt = Carbon::parse($invoice->billing_end)->startOfDay();

        return DB::transaction(function () use ($client, $package, $invoice, $startsAt, $endsAt): ClientPackage {
            $existing = $client->clientPackages()
                ->where('package_id', $package->id)
                ->whereDate('starts_at', $startsAt)
                ->whereDate('ends_at', $endsAt)
                ->first();

            if ($existing) {
                if (! $existing->is_active || $existing->status !== ClientPackage::STATUS_ACTIVE) {
                    $existing->update([
                        'is_active' => true,
                        'status' => ClientPackage::STATUS_ACTIVE,
                        'is_trial' => false,
                    ]);
                }

                return $existing->refresh();
            }

            // Only cancel the current subscription when the newly-paid period
            // begins now (or has already begun). Future renewals must not cut the
            // current paid period short.
            if ($startsAt->lte(now())) {
                $client->clientPackages()
                    ->where('is_active', true)
                    ->where('starts_at', '<=', now())
                    ->where('ends_at', '>', now())
                    ->update([
                        'is_active' => false,
                        'status' => ClientPackage::STATUS_CANCELLED,
                    ]);
            }

            return $client->clientPackages()->create([
                'package_id' => $package->id,
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'is_trial' => false,
                'is_active' => true,
                'status' => ClientPackage::STATUS_ACTIVE,
            ]);
        });
    }
}
