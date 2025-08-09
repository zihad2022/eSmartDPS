<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Invoice;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        $clients = Client::whereNull('parent_id')->get();
        $clients->load('clientPakage.pakage');
        if ($clients->isEmpty()) {
            $this->command->warn('No clients found. Skipping invoice seeding.');

            return;
        }

        foreach ($clients as $client) {
            Invoice::create([
                'client_id' => $client->id,
                'invoice_number' => generate_invoice_number(),
                'invoice_amount' => $client->clientPakage->pakage->price,
                'status' => rand(1, 5),
                'payment_id' => null,
                'trx_id' => null,
                'payment_method' => null,
                'wallet_address' => null,
            ]);
        }
    }
}
