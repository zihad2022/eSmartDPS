<?php

namespace App\Console\Commands;

use App\Enums\InvoiceStatus;
use App\Models\Client;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class GenerateMonthlyInvoices extends Command
{
    protected $signature = 'invoices:generator';
    protected $description = 'Generate monthly invoices for clients with active paid packages.';

    public function handle(): int
    {
        // Acquire lock (TTL 10 minutes)
        $lock = Cache::lock('invoices:generate:lock', 600);
        $acquired = $lock->get();

        if (! $acquired) {
            $this->warn('Invoice generation already running in another process.');
            return Command::SUCCESS;
        }

        try {
            $now = Carbon::now();
            $year = $now->year;
            $month = $now->month;

            $hasPackageIdColumn = Schema::hasColumn('invoices', 'package_id');

            // Load clients with their active paid package
            $clients = Client::parents()
                ->active()
                ->with('activePaidClientPackage.package')
                ->get();

            if ($clients->isEmpty()) {
                $this->info('No active root-level clients found.');
                return Command::SUCCESS;
            }

            foreach ($clients as $client) {
                // get package early (so we can log/skip before transaction)
                $package = $client->activePaidClientPackage->package ?? null;

                if (! $package) {
                    $this->warn("No active paid package for Client ID {$client->id}, skipping.");
                    continue;
                }

                DB::transaction(function () use ($client, $year, $month, $package, $hasPackageIdColumn) {
                    // Base query: invoices for this client & month
                    $invoiceQuery = $client->invoices()
                        ->whereYear('created_at', $year)
                        ->whereMonth('created_at', $month);

                    // Check existence for the *same package* (preferred by package_id)
                    if ($hasPackageIdColumn) {
                        $invoiceExists = $invoiceQuery->where('package_id', $package->id)->exists();
                    } else {
                        // Fallback to package_name match if package_id column not present
                        $invoiceExists = $invoiceQuery->where('package_name', $package->name)->exists();
                    }

                    if ($invoiceExists) {
                        // already generated invoice for this client + package + month
                        $this->line("Invoice exists for Client ID {$client->id} (package: {$package->name}), skipping.");
                        return;
                    }

                    // Create invoice for this package this month
                    $payload = [
                        'client_id' => $client->id,
                        'package_id' => $package->id,
                        'package_name' => $package->name,
                        'package_description' => $package->description,
                        'invoice_number' => generate_invoice_number(),
                        'invoice_amount' => $package->price,
                        'status' => InvoiceStatus::UNPAID->value,
                        'due_date' => now()->addDays(7),
                    ];

                    // attach package_id if DB supports it
                    if ($hasPackageIdColumn) {
                        $payload['package_id'] = $package->id;
                    }

                    Invoice::create($payload);

                    $this->info("Invoice generated for Client ID {$client->id} (Package: {$package->name}).");
                });
            }

            $this->info('Monthly invoice generation completed.');
            return Command::SUCCESS;

        } catch (\Throwable $e) {
            Log::error('Invoice generation failed', [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
            ]);

            $this->error("Critical error: {$e->getMessage()}");
            return Command::FAILURE;

        } finally {
            // Only release if we actually acquired the lock
            if ($acquired) {
                $lock->release();
            }
        }
    }
}
