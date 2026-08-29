<?php

namespace App\Actions\Admin\Tickets;

use App\Models\Client;
use App\Models\Ticket;

class UpdateTicketAction
{
    public function execute(Ticket $ticket, array $data): Ticket
    {
        Client::query()->parents()->findOrFail($data['client_id']);
        unset($data['ticket_number']);

        $ticket->update($data);

        return $ticket->refresh();
    }
}
