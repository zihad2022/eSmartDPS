<?php

namespace App\Actions\Admin\Tickets;

use App\Domain\Clients\Models\Client;
use App\Models\Ticket;

class GetTicketFormDataAction
{
    public function execute(?Ticket $ticket = null): array
    {
        return [
            'ticket' => $ticket,
            'clients' => Client::query()
                ->parents()
                ->when(! $ticket, fn ($query) => $query->active())
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->get(),
            'ticket_number' => $ticket?->ticket_number ?? generate_ticket_number(),
        ];
    }
}
