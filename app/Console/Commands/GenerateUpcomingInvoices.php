<?php

namespace App\Console\Commands;

use App\Actions\Client\Auth\CreateClientInvoiceAction;
use App\Models\ClientPackage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateUpcomingInvoices extends Command
{
    protected $signature = 'saas:generate-upcoming-invoices';

    protected $description = 'Generate unpaid renewal invoices for paid subscriptions entering their final 7 days';

    public function handle(CreateClientInvoiceAction $createInvoice)
    {
        $invoiceWindowEndsAt = now()->addDays(7)->endOfDay();

        // Generate the next invoice as soon as a paid subscription enters the
        // final 7 days. The range (instead of one exact date) makes the job safe
        // if the scheduler misses a run. Trials are handled separately.
        $endingPackages = ClientPackage::with(['client', 'package'])
            ->where('is_active', true)
            ->where('is_trial', false)
            ->where('status', ClientPackage::STATUS_ACTIVE)
            ->where('ends_at', '>', now())
            ->where('ends_at', '<=', $invoiceWindowEndsAt)
            ->get();

        Log::info('Upcoming invoice generation started.');

        foreach ($endingPackages as $cp) {

            if (! $cp->client || ! $cp->package) {
                continue;
            }

            $client = $cp->client;
            $package = $cp->package;

            $billingStart = $cp->ends_at->copy()->startOfDay();
            $billingEnd = $package->billingEndDate($billingStart);

            // Avoid duplicate invoice creation
            $alreadyExists = $client->invoices()
                ->where('package_id', $package->id)
                ->whereDate('billing_start', $billingStart)
                ->whereDate('billing_end', $billingEnd)
                ->exists();

            if ($alreadyExists) {
                continue;
            }

            // Create invoice
            $invoice = $createInvoice->execute($client, $package, [
                'billing_start' => $billingStart,
                'billing_end' => $billingEnd,
                // The renewal invoice is issued up to 7 days early and is due
                // when the current subscription period ends.
                'due_date' => $billingStart,
            ]);

            $this->info("Invoice {$invoice->invoice_number} created for Client {$client->id}");
        }

        Log::info('Upcoming invoice generation completed.');

        return Command::SUCCESS;
    }
}
