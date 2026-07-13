<?php

namespace App\Actions\Admin\Tickets;

use App\Models\Ticket;

class GetTicketChatAction
{
    public function execute(Ticket $ticket): Ticket
    {
        return $ticket->load([
            'client',
            'replies' => fn ($query) => $query->oldest(),
            'replies.admin',
            'replies.client',
        ]);
    }
}
