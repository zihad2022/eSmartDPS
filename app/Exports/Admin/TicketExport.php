<?php

namespace App\Exports\Admin;

use App\Models\Ticket;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TicketExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * Status filter for tickets (open, closed, or all).
     *
     * @var string|null
     */
    private $status;

    /**
     * Counter for serial number in exported sheet.
     *
     * @var int
     */
    private $sl = 1;

    /**
     * Create a new export instance with a specific status filter.
     *
     * @param string|null $status
     */
    public function __construct($status = null)
    {
        $this->status = $status;
    }

    /**
     * Get the collection of tickets to be exported based on status filter.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // If status is "open", only export open tickets
        if ($this->status === 'open') {
            $tickets = Ticket::open()->get();
            \Log::info('Open tickets exported', ['count' => $tickets->count()]);
            return $tickets;
        }

        // If status is "closed", only export closed tickets
        if ($this->status === 'closed') {
            $tickets = Ticket::closed()->get();
            \Log::info('Closed tickets exported', ['count' => $tickets->count()]);
            return $tickets;
        }

        // Otherwise, export all tickets
        $tickets = Ticket::all();
        \Log::info('All tickets exported', ['count' => $tickets->count()]);
        return $tickets;
    }

    /**
     * Define the headings for the exported Excel file.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'SL',
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

    /**
     * Map each ticket model to a row of export data.
     *
     * @param \App\Models\Ticket $ticket
     * @return array
     */
    public function map($ticket): array
    {
        // Ensure client relation is loaded for each ticket
        $ticket->load('client');

        return [
            '#' . $this->sl++, // Serial number
            $ticket->ticket_number,
            $ticket->subject,
            $ticket->message,
            $ticket->client
                ? $ticket->client->first_name . ' ' . $ticket->client->last_name
                : 'N/A', // Show "N/A" if client does not exist
            $ticket->status->label(),   // Status label (e.g. Open, Closed)
            $ticket->priority->label(), // Priority label (e.g. High, Low)
            $ticket->admin_notes,
            $ticket->created_at->format('Y-m-d'), // Format date as YYYY-MM-DD
        ];
    }
}
