<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TicketRequest;
use App\Models\Client;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $statusMap = [
            'open' => TicketStatus::OPEN,
            'closed' => TicketStatus::CLOSED,
            'in_progress' => TicketStatus::IN_PROGRESS,
            'resolved' => TicketStatus::RESOLVED,
        ];

        $priorityMap = [
            'high' => TicketPriority::HIGH,
            'medium' => TicketPriority::MEDIUM,
            'low' => TicketPriority::LOW,
        ];

        $query = Ticket::with('client')->latest('id');

        if ($filter = $request->query('status')) {
            if (isset($statusMap[$filter])) {
                $query->where('status', $statusMap[$filter]);
            }
            if (isset($priorityMap[$filter])) {
                $query->where('priority', $priorityMap[$filter]);
            }
        }

        $tickets = $query->paginate(10)->appends($request->query());

        return view('admin.ticket.index', [
            'tickets' => $tickets,
            'totalTickets' => Ticket::count(),
            'openTickets' => Ticket::where('status', TicketStatus::OPEN)->count(),
            'closedTickets' => Ticket::where('status', TicketStatus::CLOSED)->count(),
            'highPriorityTickets' => Ticket::where('priority', TicketPriority::HIGH)->count(),
        ]);
    }

    public function create()
    {
        return view('admin.ticket.form', [
            'clients' => Client::where('parent_id', owner_client_id())->get(),
            'ticket_number' => generate_ticket_number(),
        ]);
    }

    public function store(TicketRequest $request)
    {
        Ticket::create($request->validated());

        return redirect()
            ->route('admin.tickets.index')
            ->with('success', 'Ticket created successfully.');
    }

    public function show(string $id)
    {
        return view('admin.ticket.show', [
            'ticket' => Ticket::with('client')->findOrFail($id),
            'tickets' => Ticket::with('client')->latest('id')->paginate(10),
        ]);
    }

    public function edit(string $id)
    {
        return view('admin.ticket.form', [
            'ticket' => Ticket::findOrFail($id),
            'clients' => Client::all(),
            'ticket_number' => Ticket::findOrFail($id)->ticket_number,
        ]);
    }

    public function update(TicketRequest $request, string $id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->update($request->validated());

        return redirect()
            ->route('admin.tickets.index')
            ->with('success', 'Ticket updated successfully.');
    }

    public function destroy(string $id)
    {
        Ticket::findOrFail($id)->delete();

        return redirect()
            ->route('admin.tickets.index')
            ->with('success', 'Ticket deleted successfully.');
    }
}
