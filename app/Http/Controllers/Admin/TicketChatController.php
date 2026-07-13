<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\Tickets\GetTicketChatAction;
use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\View\View;

class TicketChatController extends Controller
{
    public function chat(Ticket $ticket, GetTicketChatAction $action): View
    {
        return view('admin.ticket.chat', [
            'ticket' => $action->execute($ticket),
        ]);
    }
}
