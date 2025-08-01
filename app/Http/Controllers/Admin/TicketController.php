<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Ticket;
use App\Services\TicketService;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $query = Ticket::with('client')->orderBy('id', 'desc');

        if ($status = $request->query('status')) {
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

            if (isset($statusMap[$status])) {
                $query->where('status', $statusMap[$status]);
            }

            if (isset($priorityMap[$status])) {
                $query->where('priority', $priorityMap[$status]);
            }
        }

        $tickets = $query->paginate(10)->appends($request->query());

        // Summary Counts
        $totalTickets = Ticket::count();
        $openTickets = Ticket::where('status', TicketStatus::OPEN)->count();
        $highPriorityTickets = Ticket::where('priority', TicketPriority::HIGH)->count();
        $closedTickets = Ticket::where('status', TicketStatus::CLOSED)->count();

        return view('admin.ticket.index', compact(
            'tickets',
            'totalTickets',
            'openTickets',
            'highPriorityTickets',
            'closedTickets'
        ));
    }

    public function create()
    {
        $clients = Client::where('parent_id', owner_client_id())->get();
        $ticket_number = (new TicketService())->generateTicketNumber();

        return view('admin.ticket.form', compact('clients', 'ticket_number'));
    }

    public function store(Request $request)
    {
        $this->validateData($request);

        Ticket::create($request->only([
            'client_id',
            'ticket_number',
            'subject',
            'message',
            'status',
            'priority',
            'admin_notes',
        ]));

        return redirect()->route('admin.tickets.index')->with('success', 'Ticket created successfully.');
    }

    public function show(string $id)
    {
        $ticket = Ticket::with('client')->findOrFail($id);
        $tickets = Ticket::with('client')->orderBy('id', 'desc')->paginate(10);

        return view('admin.ticket.show', compact('ticket', 'tickets'));
    }

    public function edit(string $id)
    {
        $ticket = Ticket::findOrFail($id);
        $clients = Client::all();
        $ticket_number = $ticket->ticket_number;

        return view('admin.ticket.form', compact('ticket', 'clients', 'ticket_number'));
    }

    public function update(Request $request, string $id)
    {
        $ticket = Ticket::findOrFail($id);
        $this->validateData($request, $ticket);

        $ticket->update($request->only([
            'client_id',
            'ticket_number',
            'subject',
            'message',
            'status',
            'priority',
            'admin_notes',
        ]));

        return redirect()->route('admin.tickets.index')->with('success', 'Ticket updated successfully.');
    }

    public function destroy(string $id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->delete();

        return redirect()->route('admin.tickets.index')->with('success', 'Ticket deleted successfully.');
    }

    private function validateData(Request $request, Ticket $ticket = null)
    {
        $ticketId = $ticket ? $ticket->id : null;

        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'ticket_number' => 'required|unique:tickets,ticket_number,'.$ticketId,
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'status' => 'required|in:'.implode(',', array_column(TicketStatus::cases(), 'value')),
            'priority' => 'required|in:'.implode(',', array_column(TicketPriority::cases(), 'value')),
            'admin_notes' => 'nullable|string',
        ]);
    }
}
