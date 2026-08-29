<?php

namespace App\Actions\Admin\Invoices;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Package;

class GetInvoiceFormDataAction
{
    public function execute(?Invoice $invoice = null): array
    {
        $editing = $invoice !== null;

        return [
            'invoice' => $invoice?->load('client'),
            'clients' => Client::query()
                ->parents()
                ->when(! $editing, fn ($query) => $query->active())
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->get(['id', 'first_name', 'last_name']),
            'packages' => Package::query()
                ->when(! $editing, fn ($query) => $query->active())
                ->orderBy('name')
                ->get(['id', 'name', 'price']),
            'invoice_number' => $invoice?->invoice_number ?? generate_invoice_number(),
        ];
    }
}
