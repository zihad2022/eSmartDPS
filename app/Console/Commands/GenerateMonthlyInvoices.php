<?php

namespace App\Console\Commands;

use App\Enums\InvoiceStatus;
use App\Models\Client;
use App\Models\Invoice;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class GenerateMonthlyInvoices extends Command
{
    protected $signature = 'invoices:generate';
    protected $description = 'Generate invoices for clients with active paid packages or expired trials.';

    public function handle(): int
    {
        Log::info('🔄 [InvoiceGen] Starting invoice generation...');

        $lock = Cache::lock('invoices:generate:lock', 600);
        if (!$lock->get()) {
            $this->warn('Invoice generation already running in another process.');
            Log::warning('[InvoiceGen] Skipped - lock active.');
            return Command::SUCCESS;
        }

        try {
            $now = now();
            $year = $now->year;
            $month = $now->month;
            $hasPackageIdColumn = Schema::hasColumn('invoices', 'package_id');

            // Fetch root-level active clients
            $clients = Client::parents()->active()->with([
                'activePaidClientPackage.package',
                'expiredTrialPackages.package'
            ])->get();

            if ($clients->isEmpty()) {
                $this->info('No active clients found.');
                return Command::SUCCESS;
            }

            foreach ($clients as $client) {

                // ---------------------------
                // Handle Expired Trials
                // ---------------------------
                foreach ($client->expiredTrialPackages as $trial) {
                    $package = $trial->package;
                    if (!$package) continue;

                    DB::transaction(function () use ($client, $trial, $package) {
                        Invoice::create([
                            'client_id'           => $client->id,
                            'package_id'          => $package->id,
                            'package_name'        => $package->name,
                            'package_description' => $package->description,
                            'invoice_number'      => generate_invoice_number(),
                            'invoice_amount'      => $package->price,
                            'status'              => InvoiceStatus::UNPAID->value,
                            'due_date'            => now()->addDays(7),
                        ]);

                        $trial->update(['is_active' => false]);

                        Log::info("[InvoiceGen] Expired trial → Invoice created & trial deactivated for Client {$client->id}");
                    });
                }

                // ---------------------------
                // Handle Paid Packages
                // ---------------------------
                $activePackage = $client->activePaidClientPackage;
                if (!$activePackage || !$activePackage->package) continue;

                $package = $activePackage->package;

                DB::transaction(function () use ($client, $year, $month, $package, $hasPackageIdColumn) {
                    $invoiceQuery = $client->invoices()
                        ->whereYear('created_at', $year)
                        ->whereMonth('created_at', $month);

                    $exists = $hasPackageIdColumn
                        ? $invoiceQuery->where('package_id', $package->id)->exists()
                        : $invoiceQuery->where('package_name', $package->name)->exists();

                    if ($exists) {
                        Log::info("[InvoiceGen] Invoice already exists for Client {$client->id}, skipping.");
                        return;
                    }

                    Invoice::create([
                        'client_id'           => $client->id,
                        'package_id'          => $package->id,
                        'package_name'        => $package->name,
                        'package_description' => $package->description,
                        'invoice_number'      => generate_invoice_number(),
                        'invoice_amount'      => $package->price,
                        'status'              => InvoiceStatus::UNPAID->value,
                        'due_date'            => now()->addDays(7),
                    ]);

                    Log::info("[InvoiceGen] Monthly invoice generated for Client {$client->id}, Package {$package->name}");
                });
            }

            $this->info('✅ Invoice generation completed successfully.');
            Log::info('[InvoiceGen] Invoice generation completed.');
            return Command::SUCCESS;

        } catch (\Throwable $e) {
            Log::error('❌ [InvoiceGen] Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->error("Error: {$e->getMessage()}");
            return Command::FAILURE;

        } finally {
            $lock->release();
            Log::info('[InvoiceGen] Lock released.');
        }
    }
}
