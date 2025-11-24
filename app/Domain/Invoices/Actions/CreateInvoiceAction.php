<?php

namespace App\Domain\Billing\Actions;

use App\Domain\Invoices\Models\Invoice;
use App\Domain\Clients\Models\Client;
use App\Domain\Packages\Models\Package;
use App\Enums\InvoiceStatus;

class CreateInvoiceAction
{
    public function execute(Client $client, Package $package, $billingStart, $billingEnd): Invoice
    {
        return Invoice::create([
            'client_id' => $client->id,
            'package_id' => $package->id,

            // snapshot → important for historical record
            'package_name' => $package->name,
            'package_description' => $package->description,

            'billing_start' => $billingStart,
            'billing_end' => $billingEnd,

            'invoice_number' => generate_invoice_number(),
            'invoice_amount' => $package->price,
            'status' => InvoiceStatus::UNPAID,
        ]);
    }
}
