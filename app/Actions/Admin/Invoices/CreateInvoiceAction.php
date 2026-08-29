<?php

namespace App\Actions\Admin\Invoices;

use App\Enums\InvoiceStatus;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Package;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateInvoiceAction
{
    public function execute(array $data): Invoice
    {
        return DB::transaction(function () use ($data): Invoice {
            $client = Client::query()->parents()->findOrFail($data['client_id']);
            $package = Package::query()->findOrFail($data['package_id']);

            $duplicate = Invoice::query()
                ->where('client_id', $client->id)
                ->where('package_id', $package->id)
                ->whereDate('billing_start', $data['billing_start'])
                ->whereDate('billing_end', $data['billing_end'])
                ->exists();

            if ($duplicate) {
                throw ValidationException::withMessages([
                    'billing_start' => ['An invoice already exists for this client, package, and billing period.'],
                ]);
            }

            $status = InvoiceStatus::from((int) $data['status']);

            return Invoice::query()->create([
                ...$data,
                'client_id' => $client->id,
                'package_id' => $package->id,
                'package_name' => $package->name,
                'package_description' => $package->description,
                'invoice_number' => $data['invoice_number'],
                'paid_at' => in_array($status, [InvoiceStatus::PAID, InvoiceStatus::REFUND_REQUESTED, InvoiceStatus::REFUNDED], true) ? now() : null,
            ]);
        });
    }
}
