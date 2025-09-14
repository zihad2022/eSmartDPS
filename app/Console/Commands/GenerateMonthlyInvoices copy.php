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
    // Command signature (how to call this command)
    protected $signature = 'invoices:generator';

    // Command description
    protected $description = 'Generate monthly invoices for clients who do not yet have an invoice for the current month.';

    public function handle(): int
    {
        // Prevent multiple instances of this command running concurrently
        $lock = Cache::lock('invoices:generate:lock', 600); // 10 minutes lock

        if (! $lock->get()) {
            // If lock is already acquired by another process, exit gracefully
            $this->error('Command is already running in another process.');
            return Command::FAILURE;
        }

        try {
            // Get current year and month
            $now = Carbon::now();
            $year = $now->year;
            $month = $now->month;

            // Fetch all root-level active clients
            $clients = Client::parents()->active()->get();

            if ($clients->isEmpty()) {
                // Exit if no clients found
                $this->warn('No root-level clients found.');
                return Command::SUCCESS;
            }

            // Loop through each client to generate invoice
            foreach ($clients as $client) {

                // Wrap each invoice generation in a database transaction for safety
                DB::transaction(function () use ($client, $year, $month) {

                    // Check if an invoice already exists for this client in the current month
                    $exists = Invoice::where('client_id', $client->id)
                        ->whereRaw('YEAR(created_at) = ? AND MONTH(created_at) = ?', [$year, $month])
                        ->exists();

                    if ($exists) {
                        // Skip client if invoice already exists
                        $this->line("Invoice already exists for Client ID {$client->id}.");
                        return;
                    }

                    // Load client's active paid package relationship if not loaded
                    $client->loadMissing('activePaidClientPackage.package');
                    $package = $client->activePaidClientPackage->package ?? null;

                    if (! $package) {
                        // Throw exception if client has no active package
                        throw new \Exception("No active package found for Client ID {$client->id}");
                    }

                    // Create a new invoice record for the client
                    Invoice::create([
                        'client_id' => $client->id,
                        'package_name' => $package->name,
                        'package_description' => $package->description,
                        'invoice_number' => generate_invoice_number(), // Custom helper for invoice numbers
                        'invoice_amount' => $package->price,
                        'status' => InvoiceStatus::UNPAID->value,
                        'payment_id' => null,
                        'trx_id' => null,
                        'payment_method' => null,
                        'due_date' => now()->addDays(7),
                        'paid_at' => null,
                        'wallet_address' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Inform in console that invoice is generated
                    $this->info("Invoice generated for Client ID {$client->id}");
                });
            }

            // All invoices processed
            $this->info('Monthly invoice generation completed.');

            return Command::SUCCESS;

        } catch (\Throwable $e) {
            // Log detailed error for debugging
            Log::error('Invoice generation failed', [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
            ]);

            // Show error in console
            $this->error('Critical error: '.$e->getMessage());
            return Command::FAILURE;

        } finally {
            // Always release lock even if error occurs
            $lock->release();
        }
    }
}
