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

class GenerateMonthlyInvoices extends Command
{
    protected $signature = 'invoices:generator';
    protected $description = 'Generate monthly invoices for clients with active paid packages.';

    public function handle(): int
    {
        // Prevent concurrent execution
        $lock = Cache::lock('invoices:generate:lock', 600);

        if (! $lock->get()) {
            $this->warn('Invoice generation already running in another process.');
            return Command::SUCCESS;
        }

        try {
            $now = Carbon::now();
            $year = $now->year;
            $month = $now->month;

            // Load clients with their active paid package in a single query
            $clients = Client::parents()
                ->active()
                ->with('activePaidClientPackage.package')
                ->get();

            if ($clients->isEmpty()) {
                $this->info('No active root-level clients found.');
                return Command::SUCCESS;
            }

            foreach ($clients as $client) {
                DB::transaction(function () use ($client, $year, $month) {
                    // Skip if invoice already exists
                    if ($client->invoices()
                        ->whereYear('created_at', $year)
                        ->whereMonth('created_at', $month)
                        ->exists()) {
                        $this->line("Invoice exists for Client ID {$client->id}, skipping.");
                        return;
                    }

                    $package = $client->activePaidClientPackage->package ?? null;

                    if (! $package) {
                        $this->warn("No active paid package for Client ID {$client->id}, skipping.");
                        return;
                    }

                    // Create invoice
                    Invoice::create([
                        'client_id' => $client->id,
                        'package_name' => $package->name,
                        'package_description' => $package->description,
                        'invoice_number' => generate_invoice_number(),
                        'invoice_amount' => $package->price,
                        'status' => InvoiceStatus::UNPAID->value,
                        'due_date' => now()->addDays(7),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $this->info("Invoice generated for Client ID {$client->id}");
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
            $lock->release();
        }
    }
}