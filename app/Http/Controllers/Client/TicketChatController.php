<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Ticket;

class TicketChatController extends Controller
{
    /**
     * Display the chat interface for a specific ticket.
     */
    public function chat(string $id)
    {
        // -----------------------------
        // 1. Fetch ticket (only own)
        // -----------------------------
        $ticket = Ticket::where('client_id', owner_client_id())
            ->with(['replies' => function ($q) {
                $q->orderBy('created_at', 'asc');
            }, 'client'])->findOrFail($id);

        // -----------------------------
        // 2. Return chat view
        // -----------------------------
        return view('client.ticket.chat', compact('ticket'));
    }
}
