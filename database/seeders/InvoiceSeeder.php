<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Invoice;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
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
            $package = $client->activePaidClientPackage; // Single package

            if (! $package || ! $package->package) {
                $this->command->warn("No active paid package for client ID {$client->id}. Skipping.");

                continue;
            }

            Invoice::create([
                'client_id' => $client->id,
                'invoice_number' => generate_invoice_number(),
                'invoice_amount' => $package->package->price,
                'status' => rand(1, 5),
                'payment_id' => null,
                'trx_id' => null,
                'payment_method' => null,
                'wallet_address' => null,
            ]);
        }
    }
}
