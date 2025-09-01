<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Invoice;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * This seeder generates invoices for clients that
     * have an active paid package.
     */
    public function run(): void
    {
        // Fetch all clients who do not have a parent (main clients only)
        // and load their active paid package with package details
        $clients = Client::whereNull('parent_id')
            ->with('activePaidClientPackage.package')
            ->get();

        // If no clients are found, display a warning in the console
        if ($clients->isEmpty()) {
            $this->command->warn('No clients found. Skipping invoice seeding.');
            return;
        }

        // Loop through each client and create invoices
        foreach ($clients as $client) {
            $package = $client->activePaidClientPackage; // Get the client’s active package

            // If client has no active paid package, skip invoice creation
            if (! $package || ! $package->package) {
                $this->command->warn("No active paid package for client ID {$client->id}. Skipping.");
                continue;
            }

            // Create a new invoice for the client
            Invoice::create([
                'client_id'          => $client->id,                          // The client who owns the invoice
                'package_name'       => $package->package->name,              // Name of the package
                'package_description'=> $package->package->description,       // Description of the package
                'invoice_number'     => generate_invoice_number(),            // Unique invoice number generator
                'invoice_amount'     => $package->package->price,             // Package price as invoice amount
                'status'             => rand(1, 5),                           // Random status (for demo/testing)
                'payment_id'         => null,                                 // Initially no payment ID
                'trx_id'             => null,                                 // Initially no transaction ID
                'payment_method'     => rand(1, 5),                           // Random payment method (for demo)
                'wallet_address'     => null,                                 // Empty wallet address by default
            ]);
        }
    }
}
