<?php

namespace App\Exports\Admin;

use App\Models\Ticket;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TicketExport implements FromCollection, WithHeadings, WithMapping
{
    private $status;

    private $sl = 1;

    public function __construct($status)
    {
        $this->status = $status;
    }

    public function collection()
    {
        if ($this->status === 'open') {
            $tickets = Ticket::open()->get();
            \Log::info('Open tickets exported', ['count' => $tickets->count()]);

            return $tickets;
        }

        if ($this->status === 'closed') {
            $tickets = Ticket::closed()->get();
            \Log::info('Closed tickets exported', ['count' => $tickets->count()]);

            return $tickets;
        }

        $tickets = Ticket::all();
        \Log::info('All tickets exported', ['count' => $tickets->count()]);

        return $tickets;
    }

    public function headings(): array
    {
        return [
            'sl',
            'Ticket No',
            'Subject',
            'Message',
            'Client',
            'Status',
            'Priority',
            'Admin Notes',
            'Created At',
        ];
    }

    public function map($ticket): array
    {
        $ticket->load('client');

        return [
            '#'.$this->sl++,
            $ticket->ticket_number,
            $ticket->subject,
            $ticket->message,
            $ticket->client ? $ticket->client->first_name.' '.$ticket->client->last_name : 'N/A',
            $ticket->status->label(),
            $ticket->priority->label(),
            $ticket->admin_notes,
            $ticket->created_at->format('Y-m-d'),
        ];
    }
}
