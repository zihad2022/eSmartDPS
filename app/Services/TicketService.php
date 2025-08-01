<?php

namespace App\Services;

use App\Models\Ticket;

class TicketService
{
    public function generateTicketNumber(): string
    {
        // Pattern: TKT001, TKT002, TKT003, ...
        $lastTicket = Ticket::where('ticket_number', 'like', 'TKT%')
            ->orderByRaw('CAST(SUBSTRING(ticket_number, 4) AS UNSIGNED) DESC')
            ->first();

        if ($lastTicket && preg_match('/TKT(\d+)/', $lastTicket->ticket_number, $matches)) {
            $lastNumber = (int) $matches[1];
        } else {
            $lastNumber = 0;
        }

        $nextNumber = $lastNumber + 1;

        return 'TKT'.str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
}
