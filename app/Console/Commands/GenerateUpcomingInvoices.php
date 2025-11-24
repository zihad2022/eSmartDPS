<?php

namespace App\Console\Commands;

use App\Actions\Client\Auth\CreateClientInvoiceAction;
use App\Domain\Clients\Models\ClientPackage;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateUpcomingInvoices extends Command
{
    protected $signature = 'saas:generate-upcoming-invoices';
    protected $description = 'Generate invoices for upcoming trial-ending or paid package-ending clients';

    public function handle(CreateClientInvoiceAction $createInvoice)
    {
        $today = Carbon::now();
        $daysBeforeExpiry = 1;
        $targetDate = $today->copy()->addDays($daysBeforeExpiry);

        $endingPackages = ClientPackage::with(['client', 'package'])
            ->where('is_active', true)
            ->whereDate('ends_at', $targetDate)
            ->get();

        Log::info('command is running');
        foreach ($endingPackages as $cp) {
            Log::info($cp);
            if (!$cp->client || !$cp->package) {
                continue;
            }

            $client = $cp->client;
            $package = $cp->package;

            $billingStart = Carbon::parse($cp->ends_at)->startOfDay();
            $billingEnd   = $billingStart->copy()->addDays($package->duration_days ?? 30);

            // Prevent duplicate invoice creation
            $alreadyExists = $client->invoices()
                ->where('package_id', $package->id)
                ->whereDate('billing_start', $billingStart)
                ->whereDate('billing_end', $billingEnd)
                ->exists();

            if ($alreadyExists) {
                continue;
            }

            // Generate invoice
            $invoice = $createInvoice->execute($client, $package, [
                'billing_start' => $billingStart,
                'billing_end'   => $billingEnd,
            ]);

            $this->info("Invoice {$invoice->invoice_number} created for Client {$client->id}");
        }

        return Command::SUCCESS;
    }
}
