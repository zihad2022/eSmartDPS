<?php

namespace Database\Seeders;

use App\Domain\Clients\Models\Client;
use App\Domain\Invoices\Models\Invoice;
use App\Enums\InvoiceStatus;
use App\Enums\PaymentMethod;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        $clients = Client::query()
            ->parents()
            ->with('activePaidClientPackage.package')
            ->get();

        if ($clients->isEmpty()) {
            $this->command?->warn('No clients found. Skipping invoice seeding.');

            return;
        }

        foreach ($clients as $client) {
            $subscription = $client->activePaidClientPackage;
            $package = $subscription?->package;

            if (! $subscription || ! $package) {
                $this->command?->warn("No active paid package for client ID {$client->id}. Skipping.");

                continue;
            }

            for ($index = 0; $index < random_int(3, 5); $index++) {
                $billingStart = Carbon::now()->startOfMonth()->subMonths($index);
                $billingEnd = $package->billingEndDate($billingStart);
                $createdAt = $billingStart->copy()->subDays(random_int(0, 5));
                $dueDate = $billingStart->copy()->addDays(7);
                $isPaid = (bool) random_int(0, 1);
                $paidAt = $isPaid ? $createdAt->copy()->addDays(random_int(1, 6)) : null;

                Invoice::updateOrCreate(
                    [
                        'client_id' => $client->id,
                        'package_id' => $package->id,
                        'billing_start' => $billingStart->toDateString(),
                        'billing_end' => $billingEnd->toDateString(),
                    ],
                    [
                        'package_name' => $package->name,
                        'package_description' => $package->description,
                        'invoice_number' => generate_invoice_number(),
                        'invoice_amount' => $package->final_price,
                        'status' => $isPaid ? InvoiceStatus::PAID : InvoiceStatus::UNPAID,
                        'payment_id' => $isPaid ? 'PAY'.random_int(1000, 9999) : null,
                        'trx_id' => $isPaid ? 'TRX'.random_int(1000, 9999) : null,
                        'payment_method' => $isPaid ? PaymentMethod::ONLINE : null,
                        'wallet_address' => null,
                        'due_date' => $dueDate,
                        'paid_at' => $paidAt,
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ],
                );
            }
        }
    }
}
