<?php

namespace App\Actions\Admin\Tickets;

use App\Models\Ticket;

class GetTicketDetailsAction
{
    public function execute(Ticket $ticket): Ticket
    {
        return $ticket->load(['client', 'replies.admin', 'replies.client']);
    }
}
