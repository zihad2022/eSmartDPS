<?php

namespace App\Exports\Client;

use App\Models\Payment;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PaymentExport implements FromCollection, WithHeadings, WithMapping
{
    private int $sl = 1;

    private ?string $status = null;

    public function __construct(?string $status = null)
    {
        $this->status = $status;
    }

    /**
     * @return Collection
     */
    public function collection(): \Illuminate\Support\Collection
    {
        if ($this->status === 'pending') {
            return Payment::pending()->where('client_id', owner_client_id())->get();
        } elseif ($this->status === 'due') {
            return Payment::due()->where('client_id', owner_client_id())->get();
        } elseif ($this->status === 'paid') {
            return Payment::paid()->where('client_id', owner_client_id())->get();
        } elseif ($this->status === 'cancelled') {
            return Payment::cancelled()->where('client_id', owner_client_id())->get();
        }

        return Payment::where('client_id', owner_client_id())->get();
    }

    public function headings(): array
    {
        return [
            'SL',
            'Payment ID',
            'Member',
            'Amount',
            'Generated At',
            'Paid At',
            'Due Date',
            'Method Method',
            'Status',
        ];
    }

    public function map($payment): array
    {
        $payment->load('member');

        return [
            $this->sl++,
            $payment->payment_id,
            $payment->member->name,
            $payment->amount,
            $payment->created_at->format('M d, Y'),
            $payment->paid_at?->format('M d, Y') ?? 'N/A',
            $payment->due_date->format('M d, Y'),
            $payment->payment_method->label(),
            $payment->status->label(),
        ];
    }
}
