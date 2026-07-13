<?php

namespace App\Console\Commands;

use App\Actions\Client\Auth\CreateClientInvoiceAction;
use App\Domain\Clients\Models\ClientPackage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateUpcomingInvoices extends Command
{
    protected $signature = 'saas:generate-upcoming-invoices';
    protected $description = 'Generate invoices for upcoming trial-ending or paid package-ending clients';

    public function handle(CreateClientInvoiceAction $createInvoice)
    {
        $daysBeforeExpiry = 3;
        $targetDate = now()->addDays($daysBeforeExpiry)->toDateString();

        // Query packages ending exactly after the given number of days
        $endingPackages = ClientPackage::with(['client', 'package'])
            ->where('is_active', true)
            ->whereDate('ends_at', '=', $targetDate)
            ->get();

        Log::info('Upcoming invoice generation started.');

        foreach ($endingPackages as $cp) {

            if (!$cp->client || !$cp->package) {
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
                'billing_end'   => $billingEnd,
            ]);

            $this->info("Invoice {$invoice->invoice_number} created for Client {$client->id}");
        }

        Log::info('Upcoming invoice generation completed.');

        return Command::SUCCESS;
    }
}
