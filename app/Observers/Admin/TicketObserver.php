<?php

namespace App\Observers\Admin;

use App\Models\Ticket;
use App\Services\ActivityLogger;

class TicketObserver
{
    public function created(Ticket $ticket)
    {
        ActivityLogger::log("Ticket '{$ticket->subject}' was created.");
    }

    public function updated(Ticket $ticket)
    {
        ActivityLogger::log("Ticket '{$ticket->subject}' was updated.");
    }

    public function deleted(Ticket $ticket)
    {
        ActivityLogger::log("Ticket '{$ticket->subject}' was deleted.");
    }
}
