<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
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
        $ticket = Ticket::where('client_id', owner_client_id())
            ->with(['replies' => function ($q) {
                $q->orderBy('created_at', 'asc');
            }, 'client'])->findOrFail($id);

        // -----------------------------
        // 2. Return chat view
        // -----------------------------
        return view('client.ticket.chat', compact('ticket'));
    }

    /**
     * Store a client reply for a ticket owned by the current account.
     */
    public function storeMessage(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $ticket = Ticket::where('client_id', owner_client_id())->findOrFail($id);

        $ticket->replies()->create([
            'client_id' => owner_client_id(),
            'message' => trim($validated['message']),
        ]);

        return redirect()
            ->route('client.tickets.chat', $ticket)
            ->with('success', 'Message sent successfully.');
    }
}
