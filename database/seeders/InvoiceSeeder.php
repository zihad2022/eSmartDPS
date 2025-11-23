<?php

namespace Database\Seeders;

use App\Domain\Clients\Models\Client;
use App\Domain\Invoices\Invoice;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Generates invoices for clients with active paid packages
     * and spreads them over past days for realistic testing.
     */
    public function run(): void
    {
        $clients = Client::whereNull('parent_id')
            ->with('activePaidClientPackage.package')
            ->get();

        if ($clients->isEmpty()) {
            $this->command->warn('No clients found. Skipping invoice seeding.');
            return;
        }

        foreach ($clients as $client) {
            $package = $client->activePaidClientPackage;

            if (! $package || ! $package->package) {
                $this->command->warn("No active paid package for client ID {$client->id}. Skipping.");
                continue;
            }

            // Generate 3-5 invoices per client with varying dates
            $invoiceCount = rand(3, 5);

            for ($i = 0; $i < $invoiceCount; $i++) {
                // Random past days (0 = today, up to 90 days ago)
                $daysAgo = rand(0, 90);
                $createdAt = Carbon::now()->subDays($daysAgo);
                
                // Due date 7-30 days after creation
                $dueDate = (clone $createdAt)->addDays(rand(7, 30));

                // Randomly decide if invoice is paid or unpaid
                $isPaid = rand(0, 1) === 1;
                $paidAt = $isPaid ? (clone $createdAt)->addDays(rand(1, 10)) : null;

                Invoice::create([
                    'client_id'           => $client->id,
                    'package_name'        => $package->package->name,
                    'package_description' => $package->package->description,
                    'invoice_number'      => generate_invoice_number(),
                    'invoice_amount'      => $package->package->price,
                    'status'              => $isPaid ? 2 : 1, // 2 = Paid, 1 = Unpaid (adjust to your Enum)
                    'payment_id'          => $isPaid ? 'PAY' . rand(1000, 9999) : null,
                    'trx_id'              => $isPaid ? 'TRX' . rand(1000, 9999) : null,
                    'payment_method'      => $isPaid ? 'card' : null,
                    'wallet_address'      => null,
                    'due_date'            => $dueDate,
                    'paid_at'             => $paidAt,
                    'created_at'          => $createdAt,
                    'updated_at'          => $createdAt,
                ]);
            }
        }
    }
}
