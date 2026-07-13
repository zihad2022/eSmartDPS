<?php

namespace App\Exports;

use App\Domain\Invoices\Models\Invoice;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class InvoiceExport implements FromQuery, WithHeadings, WithMapping
{
    private int $sl = 1;

    public function __construct(private readonly ?string $status = null)
    {
    }

    public function query(): Builder
    {
        return Invoice::query()
            ->with('client:id,first_name,last_name')
            ->when($this->status === 'paid', fn (Builder $query) => $query->paid())
            ->when($this->status === 'unpaid', fn (Builder $query) => $query->unpaid())
            ->when($this->status === 'refunded', fn (Builder $query) => $query->refunded())
            ->when($this->status === 'refund-requested', fn (Builder $query) => $query->refundRequested())
            ->when($this->status === 'cancelled', fn (Builder $query) => $query->cancelled())
            ->orderByDesc('id');
    }

    public function headings(): array
    {
        return [
            'SL', 'Invoice Number', 'Client Name', 'Package', 'Billing Start', 'Billing End',
            'Due Date', 'Invoice Amount', 'Status', 'Payment ID', 'Trx ID', 'Payment Method',
            'Wallet Address', 'Paid At', 'Created At',
        ];
    }

    public function map($invoice): array
    {
        return [
            $this->sl++,
            $invoice->invoice_number,
            trim(($invoice->client?->first_name ?? '').' '.($invoice->client?->last_name ?? '')) ?: 'N/A',
            $invoice->package_name,
            $invoice->billing_start?->format('Y-m-d'),
            $invoice->billing_end?->format('Y-m-d'),
            $invoice->due_date?->format('Y-m-d'),
            $invoice->invoice_amount,
            $invoice->status?->label() ?? '-',
            $invoice->payment_id,
            $invoice->trx_id,
            $invoice->payment_method?->label() ?? '-',
            $invoice->wallet_address,
            $invoice->paid_at?->format('Y-m-d H:i:s'),
            $invoice->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
