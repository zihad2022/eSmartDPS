<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Enums\TicketStatus;
use App\Enums\TicketPriority;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class TicketController extends Controller
{
    /**
     * Display a paginated list of tickets for the logged-in client.
     */
    public function index(Request $request): View
    {
        // -----------------------------
        // 1. Handle search query
        // -----------------------------
        $search = $request->get('search');

        // -----------------------------
        // 2. Maps for status and priority filters
        // -----------------------------
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

        // -----------------------------
        // 3. Build main query (only own tickets)
        // -----------------------------
        $query = Ticket::where('client_id', owner_client_id())
            ->with('client')
            ->when($search, function ($q, $search) {
                $q->where(function ($query) use ($search) {
                    $query->where('ticket_number', 'like', "%{$search}%")
                        ->orWhere('subject', 'like', "%{$search}%")
                        ->orWhere('message', 'like', "%{$search}%")
                        ->orWhereHas('client', function ($q2) use ($search) {
                            $q2->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->when($request->query('status'), function ($q, $status) use ($statusMap) {
                if (isset($statusMap[$status])) {
                    $q->where('status', $statusMap[$status]);
                }
            })
            ->when($request->query('priority'), function ($q, $priority) use ($priorityMap) {
                if (isset($priorityMap[$priority])) {
                    $q->where('priority', $priorityMap[$priority]);
                }
            })
            ->latest('id');

        // -----------------------------
        // 4. Paginate results
        // -----------------------------
        $tickets = $query->paginate(10)->appends($request->query());

        // -----------------------------
        // 5. Collect statistics
        // -----------------------------
        $statusCounts = Ticket::where('client_id', owner_client_id())
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $priorityCounts = Ticket::where('client_id', owner_client_id())
            ->selectRaw('priority, COUNT(*) as count')
            ->groupBy('priority')
            ->pluck('count', 'priority')
            ->toArray();

        $totalTickets = Ticket::where('client_id', owner_client_id())->count();

        // -----------------------------
        // 6. Return view
        // -----------------------------
        return view('client.ticket.index', [
            'tickets' => $tickets,
            'totalTickets' => $totalTickets,
            'statusCounts' => $statusCounts,
            'priorityCounts' => $priorityCounts,
            'search' => $search,
        ]);
    }

    /**
     * Show the form to create a new ticket.
     */
    public function create(): View
    {
        // -----------------------------
        // 1. Generate unique ticket number
        // -----------------------------
        $ticket_number = generate_ticket_number();

        // -----------------------------
        // 2. Return form view
        // -----------------------------
        return view('client.ticket.form', compact('ticket_number'));
    }

    /**
     * Store a new ticket for the logged-in client.
     */
    public function store(Request $request): RedirectResponse
    {
        // -----------------------------
        // 1. Validate form input
        // -----------------------------
        $validated = $request->validate([
            'ticket_number' => ['required', 'unique:tickets,ticket_number'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'priority' => ['required', 'in:' . implode(',', array_column(TicketPriority::cases(), 'value'))],
        ]);

        // -----------------------------
        // 2. Create ticket
        // -----------------------------
        Ticket::create([
            'ticket_number' => $validated['ticket_number'],
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'priority' => $validated['priority'],
            'client_id' => owner_client_id(),
        ]);

        // -----------------------------
        // 3. Redirect with success message
        // -----------------------------
        return redirect()
            ->route('client.tickets.index')
            ->with('success', 'Ticket created successfully.');
    }

    /**
     * Show the form to edit an existing ticket.
     */
    public function edit(int $id): View
    {
        // -----------------------------
        // 1. Fetch ticket (only own)
        // -----------------------------
        $ticket = Ticket::where('client_id', owner_client_id())->findOrFail($id);

        // -----------------------------
        // 2. Return form view
        // -----------------------------
        return view('client.ticket.form', compact('ticket'));
    }

    /**
     * Update an existing ticket.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        // -----------------------------
        // 1. Fetch ticket (only own)
        // -----------------------------
        $ticket = Ticket::where('client_id', owner_client_id())->findOrFail($id);

        // -----------------------------
        // 2. Validate input
        // -----------------------------
        $validated = $request->validate([
            'ticket_number' => ['required', 'unique:tickets,ticket_number,' . $id],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'priority' => ['required', 'in:' . implode(',', array_column(TicketPriority::cases(), 'value'))],
        ]);

        // -----------------------------
        // 3. Update ticket
        // -----------------------------
        $ticket->update($validated);

        // -----------------------------
        // 4. Redirect with success
        // -----------------------------
        return redirect()
            ->route('client.tickets.index')
            ->with('success', 'Ticket updated successfully.');
    }

    /**
     * Delete a ticket.
     */
    public function destroy(int $id): RedirectResponse
    {
        // -----------------------------
        // 1. Fetch ticket (only own)
        // -----------------------------
        $ticket = Ticket::where('client_id', owner_client_id())->findOrFail($id);

        // -----------------------------
        // 2. Delete ticket
        // -----------------------------
        $ticket->delete();

        // -----------------------------
        // 3. Redirect with success
        // -----------------------------
        return redirect()
            ->route('client.tickets.index')
            ->with('success', 'Ticket deleted successfully.');
    }
}
