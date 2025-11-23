<?php

namespace App\Actions\Client\Auth;

use App\Domain\Invoices\Models\Invoice;
use App\Domain\Clients\Models\Client;
use App\Domain\Packages\Models\Package;
use App\Enums\InvoiceStatus;

class CreateClientInvoiceAction
{
    public function execute(Client $client, Package $package): Invoice
    {
        $billingStart = now()->toDateString();
        $billingEnd = now()->addMonth()->toDateString();
        $nextInvoice = now()->addMonth()->toDateTimeString();

        return Invoice::create([
            'client_id'           => $client->id,
            'package_id'          => $package->id,
            'package_name'        => $package->name,
            'package_description' => $package->description,
            'billing_start'       => $billingStart,
            'billing_end'         => $billingEnd,
            'invoice_number'      => generate_invoice_number(),
            'invoice_amount'      => $package->price,
            'status'              => InvoiceStatus::UNPAID,
            'paid_at'             => null,
            'next_invoice_at'     => $nextInvoice,
            'payment_reference'   => null,
            'payment_id'          => null,
            'trx_id'              => null,
            'payment_method'      => null,
            'wallet_address'      => null,
        ]);
    }
}
