<?php

namespace App\Actions\Admin\Tickets;

use App\Domain\Clients\Models\Client;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;

class CreateTicketAction
{
    public function execute(array $data): Ticket
    {
        return DB::transaction(function () use ($data): Ticket {
            Client::query()->parents()->findOrFail($data['client_id']);

            return Ticket::query()->create([
                ...$data,
                'ticket_number' => $data['ticket_number'],
            ]);
        });
    }
}
