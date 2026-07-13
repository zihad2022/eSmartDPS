<?php

namespace App\Actions\Admin\Invoices;

use App\Domain\Clients\Models\Client;
use App\Domain\Invoices\Models\Invoice;
use App\Domain\Packages\Models\Package;
use App\Enums\InvoiceStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UpdateInvoiceAction
{
    public function execute(Invoice $invoice, array $data): Invoice
    {
        return DB::transaction(function () use ($invoice, $data): Invoice {
            $client = Client::query()->parents()->findOrFail($data['client_id']);
            $package = Package::query()->findOrFail($data['package_id']);
            $newStatus = InvoiceStatus::from((int) $data['status']);

            $this->guardTransition($invoice->status, $newStatus);
            $this->guardFinancialSnapshot($invoice, $data);

            $duplicate = Invoice::query()
                ->whereKeyNot($invoice->getKey())
                ->where('client_id', $client->id)
                ->where('package_id', $package->id)
                ->whereDate('billing_start', $data['billing_start'])
                ->whereDate('billing_end', $data['billing_end'])
                ->exists();

            if ($duplicate) {
                throw ValidationException::withMessages([
                    'billing_start' => ['Another invoice already exists for this client, package, and billing period.'],
                ]);
            }

            $invoice->update([
                ...$data,
                'client_id' => $client->id,
                'package_id' => $package->id,
                'package_name' => $package->name,
                'package_description' => $package->description,
                'paid_at' => $this->resolvePaidAt($invoice, $newStatus),
            ]);

            return $invoice->refresh();
        });
    }

    private function guardTransition(InvoiceStatus $current, InvoiceStatus $next): void
    {
        $allowed = [
            InvoiceStatus::UNPAID->value => [InvoiceStatus::UNPAID, InvoiceStatus::PAID, InvoiceStatus::CANCELLED],
            InvoiceStatus::PAID->value => [InvoiceStatus::PAID, InvoiceStatus::REFUND_REQUESTED, InvoiceStatus::REFUNDED],
            InvoiceStatus::REFUND_REQUESTED->value => [InvoiceStatus::REFUND_REQUESTED, InvoiceStatus::PAID, InvoiceStatus::REFUNDED],
            InvoiceStatus::REFUNDED->value => [InvoiceStatus::REFUNDED],
            InvoiceStatus::CANCELLED->value => [InvoiceStatus::CANCELLED, InvoiceStatus::UNPAID],
        ];

        if (! in_array($next, $allowed[$current->value] ?? [], true)) {
            throw ValidationException::withMessages([
                'status' => ["Invoice status cannot change from {$current->label()} to {$next->label()}."],
            ]);
        }
    }

    private function guardFinancialSnapshot(Invoice $invoice, array $data): void
    {
        if (! in_array($invoice->status, [
            InvoiceStatus::PAID,
            InvoiceStatus::REFUND_REQUESTED,
            InvoiceStatus::REFUNDED,
        ], true)) {
            return;
        }

        $immutable = [
            'client_id' => (int) $invoice->client_id,
            'package_id' => (int) $invoice->package_id,
            'invoice_amount' => (int) $invoice->invoice_amount,
            'billing_start' => $invoice->billing_start?->format('Y-m-d'),
            'billing_end' => $invoice->billing_end?->format('Y-m-d'),
            'due_date' => $invoice->due_date?->format('Y-m-d'),
        ];

        foreach ($immutable as $field => $currentValue) {
            $incoming = $data[$field] ?? null;
            $incoming = in_array($field, ['client_id', 'package_id', 'invoice_amount'], true)
                ? (int) $incoming
                : ($incoming ? date('Y-m-d', strtotime((string) $incoming)) : null);

            if ($incoming !== $currentValue) {
                throw ValidationException::withMessages([
                    $field => ['Paid and refund-related invoices keep an immutable financial snapshot.'],
                ]);
            }
        }
    }

    private function resolvePaidAt(Invoice $invoice, InvoiceStatus $status)
    {
        if (in_array($status, [
            InvoiceStatus::PAID,
            InvoiceStatus::REFUND_REQUESTED,
            InvoiceStatus::REFUNDED,
        ], true)) {
            return $invoice->paid_at ?? now();
        }

        return null;
    }
}
