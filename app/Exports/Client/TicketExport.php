<?php

namespace App\Exports\Client;

use App\Models\Ticket;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TicketExport implements FromCollection, WithHeadings, WithMapping
{
    private ?string $status;

    private int $sl = 1;

    public function __construct(?string $status = null)
    {
        $this->status = $status;
    }

    /**
     * Return tickets filtered by status if provided.
     */
    public function collection(): \Illuminate\Support\Collection
    {
        return match ($this->status) {
            'open' => Ticket::open()->get(),
            'in_progress' => Ticket::inProgress()->get(),
            'resolved' => Ticket::resolved()->get(),
            'closed' => Ticket::closed()->get(),
            default => Ticket::all(),
        };
    }

    public function headings(): array
    {
        return [
            'SL',
            'Ticket No',
            'Subject',
            'Message',
            'Status',
            'Priority',
            'Admin Notes',
            'Created At',
        ];
    }

    public function map($ticket): array
    {
        $ticket->loadMissing('client');

        return [
            '#'.$this->sl++, // Serial number
            $ticket->ticket_number,
            $ticket->subject,
            $ticket->message,
            $ticket->status->label(),
            $ticket->priority->label(),
            $ticket->admin_notes,
            $ticket->created_at->format('Y-m-d'),
        ];
    }
}
