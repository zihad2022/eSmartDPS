<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketReply;
use Illuminate\Http\Request;

class TicketChatController extends Controller
{
    public function chat(string $id)
    {
        $ticket = Ticket::with(['replies' => function ($q) {
            $q->orderBy('created_at', 'asc');
        }, 'client'])->findOrFail($id);

        return view('admin.ticket.chat', compact('ticket'));
    }

    // public function storeMessage(Request $request, $ticketId)
    // {
    //     $request->validate([
    //         'message' => 'nullable|string',
    //         'attachment' => 'nullable|file|max:5120', // Max 5MB
    //     ]);

    //     if (empty($request->message) && ! $request->hasFile('attachment')) {
    //         return redirect()->back()->with('error', 'Please enter a message or attach a file.');
    //     }

    //     $reply = new TicketReply();
    //     $reply->ticket_id = $ticketId;
    //     $reply->admin_id = auth()->guard('admin')->id();
    //     $reply->message = $request->message;

    //     if ($request->hasFile('attachment')) {
    //         $reply->attachment = $request->file('attachment')->store('tickets', 'public');
    //     }

    //     $reply->save();

    //     return redirect()->back()->with('success', 'Message sent successfully.');
    // }
}
