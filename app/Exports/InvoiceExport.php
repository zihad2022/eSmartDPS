<?php

namespace App\Exports;

use App\Models\Invoice;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class InvoiceExport implements FromCollection, WithHeadings, WithMapping
{
    private int $sl = 1;

    private $status;

    public function __construct($status)
    {
        $this->status = $status;
    }

    public function collection()
    {
        if ($this->status === 'paid') {
            return Invoice::orderBy('id', 'desc')->paid()->get();
        }
        if ($this->status === 'unpaid') {
            return Invoice::orderBy('id', 'desc')->unpaid()->get();
        }
        if ($this->status === 'refunded') {
            return Invoice::orderBy('id', 'desc')->refunded()->get();
        }
        if ($this->status === 'refund-requested') {
            return Invoice::orderBy('id', 'desc')->refundRequested()->get();
        }
        if ($this->status === 'cancelled') {
            return Invoice::orderBy('id', 'desc')->cancelled()->get();
        }

        return Invoice::orderBy('id', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'SL',
            'Invoice Number',
            'Client Name',
            'Invoice Amount',
            'Status',
            'Payment ID',
            'Trx ID',
            'Payment Method',
            'Wallet Address',
            'Created At',
            'Updated At',
        ];
    }

    public function map($invoice): array
    {
        $invoice->load('client');

        return [
            $this->sl++,
            $invoice->invoice_number,
            optional($invoice->client)->first_name.' '.optional($invoice->client)->last_name,
            $invoice->invoice_amount,
            $invoice->status?->label() ?? '-',
            $invoice->payment_id,
            $invoice->trx_id,
            $invoice->payment_method?->label() ?? '-',
            $invoice->wallet_address,
            $invoice->created_at,
            $invoice->updated_at,
        ];
    }
}
