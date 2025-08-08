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
        if ($this->status === 'active') {
            return Invoice::active()->get();
        }
        if ($this->status === 'inactive') {
            return Invoice::inactive()->get();
        }

        return Invoice::all();
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
            $invoice->client->first_name.' '.$invoice->client->last_name,
            $invoice->invoice_amount,
            $invoice->status,
            $invoice->payment_id,
            $invoice->trx_id,
            $invoice->payment_method,
            $invoice->wallet_address,
            $invoice->created_at,
            $invoice->updated_at,
        ];
    }
}
