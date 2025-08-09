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

    protected $description = 'Generate monthly invoices for clients who do not yet have an invoice for the current month.';

    public function handle(): int
    {
        // Prevent concurrent command execution
        $lock = Cache::lock('invoices:generate:lock', 600); // 10-minute lock
        if (! $lock->get()) {
            $this->error('⚠️ Command is already running in another process.');

            return Command::FAILURE;
        }

        try {
            $now = Carbon::now();
            $year = $now->year;
            $month = $now->month;

            $clients = Client::whereNull('parent_id')->where('status', true)->get();

            if ($clients->isEmpty()) {
                $this->warn('⚠️ No root-level clients found.');

                return Command::SUCCESS;
            }

            foreach ($clients as $client) {
                DB::transaction(function () use ($client, $year, $month) {
                    // Atomic check for existing invoice
                    $exists = Invoice::where('client_id', $client->id)
                        ->whereRaw('YEAR(created_at) = ? AND MONTH(created_at) = ?', [$year, $month])
                        ->exists();

                    if ($exists) {
                        $this->line("ℹ️ Invoice already exists for Client ID {$client->id}.");

                        return;
                    }

                    // Generate invoice
                    $client->loadMissing('clientPackage.package');
                    $package = $client->clientPackage->package ?? null;

                    if (! $package) {
                        throw new \Exception("No active package found for Client ID {$client->id}");
                    }

                    Invoice::create([
                        'client_id' => $client->id,
                        'invoice_number' => generate_invoice_number(),
                        'invoice_amount' => $package->price,
                        'status' => InvoiceStatus::UNPAID->value,
                        'payment_id' => null,
                        'trx_id' => null,
                        'payment_method' => null,
                        'wallet_address' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $this->info("✅ Invoice generated for Client ID {$client->id}");
                });
            }

            $this->info('🎉 Monthly invoice generation completed.');

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            Log::error('CRITICAL: Invoice generation failed - '.$e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
            ]);
            $this->error('❌ Critical error: '.$e->getMessage());

            return Command::FAILURE;
        } finally {
            $lock->release();
        }
    }
}
