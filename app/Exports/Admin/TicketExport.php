<?php

namespace App\Exports\Admin;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TicketExport implements FromQuery, WithHeadings, WithMapping
{
    private int $sl = 1;

    public function __construct(private readonly ?string $status = null) {}

    public function query(): Builder
    {
        $statusMap = [
            'open' => TicketStatus::OPEN,
            'in_progress' => TicketStatus::IN_PROGRESS,
            'resolved' => TicketStatus::RESOLVED,
            'closed' => TicketStatus::CLOSED,
        ];

        return Ticket::query()
            ->with('client:id,first_name,last_name')
            ->when(isset($statusMap[$this->status]), fn (Builder $query) => $query->where('status', $statusMap[$this->status]))
            ->orderBy('id');
    }

    public function headings(): array
    {
        return ['SL', 'Ticket No', 'Subject', 'Message', 'Client', 'Status', 'Priority', 'Admin Notes', 'Created At'];
    }

    public function map($ticket): array
    {
        return [
            '#'.$this->sl++,
            $ticket->ticket_number,
            $ticket->subject,
            $ticket->message,
            trim(($ticket->client?->first_name ?? '').' '.($ticket->client?->last_name ?? '')) ?: 'N/A',
            $ticket->status?->label() ?? 'N/A',
            $ticket->priority?->label() ?? 'N/A',
            $ticket->admin_notes,
            $ticket->created_at?->format('Y-m-d'),
        ];
    }
}
