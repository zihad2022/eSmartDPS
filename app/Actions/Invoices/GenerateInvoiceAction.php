<?php

namespace App\Actions\Invoices;

use App\Enums\InvoiceStatus;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Package;
use Carbon\Carbon;
use Carbon\CarbonInterface;

class GenerateInvoiceAction
{
    public function execute(
        Client $client,
        Package $package,
        CarbonInterface|string $billingStart,
        CarbonInterface|string $billingEnd,
        CarbonInterface|string|null $dueDate = null,
        ?int $amount = null,
    ): Invoice {
        $start = Carbon::parse($billingStart);
        $end = Carbon::parse($billingEnd);

        return Invoice::create([
            'client_id' => $client->id,
            'package_id' => $package->id,
            'package_name' => $package->name,
            'package_description' => $package->description,
            'billing_start' => $start,
            'billing_end' => $end,
            'due_date' => $dueDate ? Carbon::parse($dueDate) : $start->copy()->addDays(7),
            'invoice_number' => generate_invoice_number(),
            'invoice_amount' => $amount ?? $package->final_price,
            'status' => InvoiceStatus::UNPAID,
        ]);
    }
}
