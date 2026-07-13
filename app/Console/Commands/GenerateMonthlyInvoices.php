<?php

namespace App\Console\Commands;

use App\Domain\Clients\Models\Client;
use App\Domain\Clients\Models\ClientPackage;
use App\Domain\Invoices\Actions\CreateInvoiceAction;
use App\Domain\Invoices\Models\Invoice;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GenerateMonthlyInvoices extends Command
{
    protected $signature = 'invoices:generate';

    protected $description = 'Generate missing invoices for active paid subscriptions and newly expired trials.';

    public function handle(CreateInvoiceAction $createInvoice): int
    {
        $lock = Cache::lock('invoices:generate:lock', 600);

        if (! $lock->get()) {
            $this->warn('Invoice generation is already running.');

            return Command::SUCCESS;
        }

        try {
            $clients = Client::query()
                ->parents()
                ->active()
                ->with([
                    'activePaidClientPackage.package',
                    'expiredTrialPackages.package',
                ])
                ->get();

            foreach ($clients as $client) {
                foreach ($client->expiredTrialPackages as $trial) {
                    $package = $trial->package;

                    if (! $package) {
                        continue;
                    }

                    $billingStart = $trial->ends_at->copy()->startOfDay();
                    $billingEnd = $package->billingEndDate($billingStart);

                    DB::transaction(function () use ($client, $trial, $package, $billingStart, $billingEnd, $createInvoice): void {
                        $exists = Invoice::query()
                            ->where('client_id', $client->id)
                            ->where('package_id', $package->id)
                            ->whereDate('billing_start', $billingStart)
                            ->whereDate('billing_end', $billingEnd)
                            ->exists();

                        if (! $exists) {
                            $createInvoice->execute(
                                client: $client,
                                package: $package,
                                billingStart: $billingStart,
                                billingEnd: $billingEnd,
                                dueDate: $billingStart->copy()->addDays(7),
                            );
                        }

                        $trial->update([
                            'is_active' => false,
                            'status' => ClientPackage::STATUS_EXPIRED,
                        ]);
                    });
                }

                $subscription = $client->activePaidClientPackage;
                $package = $subscription?->package;

                if (! $subscription || ! $package) {
                    continue;
                }

                $billingStart = $subscription->starts_at->copy()->startOfDay();
                $billingEnd = $subscription->ends_at->copy()->startOfDay();

                $exists = Invoice::query()
                    ->where('client_id', $client->id)
                    ->where('package_id', $package->id)
                    ->whereDate('billing_start', $billingStart)
                    ->whereDate('billing_end', $billingEnd)
                    ->exists();

                if ($exists) {
                    continue;
                }

                $createInvoice->execute(
                    client: $client,
                    package: $package,
                    billingStart: $billingStart,
                    billingEnd: $billingEnd,
                    dueDate: $billingStart->copy()->addDays(7),
                );
            }

            $this->info('Invoice generation completed successfully.');

            return Command::SUCCESS;
        } catch (\Throwable $exception) {
            Log::error('[InvoiceGen] Failed.', [
                'error' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);

            $this->error($exception->getMessage());

            return Command::FAILURE;
        } finally {
            $lock->release();
        }
    }
}
