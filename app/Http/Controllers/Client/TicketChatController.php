<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;

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
        $ticket = Ticket::with(['replies' => function ($q) {
            $q->orderBy('created_at', 'asc');
        }, 'client'])->findOrFail($id);

        // -----------------------------
        // 2. Return chat view
        // -----------------------------
        return view('client.ticket.chat', compact('ticket'));
    }
}
