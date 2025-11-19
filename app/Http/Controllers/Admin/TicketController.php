<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Http\Controllers\Controller;
use App\Domain\Clients\Models\Client;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class TicketController extends Controller
{
    /**
     * Display a paginated list of tickets with optional filters.
     */
    public function index(Request $request): View
    {
        // -----------------------------
        // 1. Get search query
        // -----------------------------
        $search = $request->get('search');
    
        // -----------------------------
        // 2. Define filter maps
        // -----------------------------
        $statusMap = [
            'open' => TicketStatus::OPEN->value,
            'closed' => TicketStatus::CLOSED->value,
            'in_progress' => TicketStatus::IN_PROGRESS->value,
            'resolved' => TicketStatus::RESOLVED->value,
        ];
    
        $priorityMap = [
            'high' => TicketPriority::HIGH->value,
            'medium' => TicketPriority::MEDIUM->value,
            'low' => TicketPriority::LOW->value,
        ];
    
        // -----------------------------
        // 3. Build query with eager loading
        // -----------------------------
        $tickets = Ticket::with('client')
            ->when($search, function ($q, $search) {
                $q->where(function ($query) use ($search) {
                    $query->where('ticket_number', 'like', "%{$search}%")
                          ->orWhere('subject', 'like', "%{$search}%")
                          ->orWhere('message', 'like', "%{$search}%")
                          ->orWhereHas('client', function ($clientQuery) use ($search) {
                              $clientQuery->where('first_name', 'like', "%{$search}%")
                                          ->orWhere('last_name', 'like', "%{$search}%")
                                          ->orWhere('user_id', 'like', "%{$search}%")
                                          ->orWhere('email', 'like', "%{$search}%")
                                          ->orWhere('phone', 'like', "%{$search}%");
                          });
                });
            })
            ->when($request->get('status'), function ($q, $status) use ($statusMap) {
                if (isset($statusMap[$status])) {
                    $q->where('status', $statusMap[$status]);
                }
            })
            ->when($request->get('priority'), function ($q, $priority) use ($priorityMap) {
                if (isset($priorityMap[$priority])) {
                    $q->where('priority', $priorityMap[$priority]);
                }
            })
            ->latest('id')
            ->paginate(10)
            ->appends($request->query());
    
        // -----------------------------
        // 4. Collect statistics
        // -----------------------------
        $totalTickets = Ticket::count();
        $statusCounts = [];
        foreach (TicketStatus::cases() as $status) {
            $statusCounts[$status->value] = Ticket::where('status', $status->value)->count();
        }
    
        $priorityCounts = [];
        foreach (TicketPriority::cases() as $priority) {
            $priorityCounts[$priority->value] = Ticket::where('priority', $priority->value)->count();
        }
    
        // -----------------------------
        // 5. Return view
        // -----------------------------
        return view('admin.ticket.index', compact(
            'tickets',
            'totalTickets',
            'statusCounts',
            'priorityCounts',
            'search'
        ));
    }
    

    /**
     * Show form for creating a new ticket.
     */
    public function create(): View
    {
        // -----------------------------
        // 1. Fetch available clients for current owner
        // -----------------------------
        $clients = Client::where('parent_id', owner_client_id())->get();

        // -----------------------------
        // 2. Generate ticket number
        // -----------------------------
        $ticketNumber = generate_ticket_number();

        // -----------------------------
        // 3. Return view
        // -----------------------------
        return view('admin.ticket.form', [
            'clients' => $clients,
            'ticket_number' => $ticketNumber,
        ]);
    }

    /**
     * Store a newly created ticket.
     */
    public function store(Request $request): RedirectResponse
    {
        // -----------------------------
        // 1. Validate request data
        // -----------------------------
        $validated = $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'ticket_number' => ['required', 'unique:tickets,ticket_number'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'status' => ['required', 'in:' . implode(',', array_column(TicketStatus::cases(), 'value'))],
            'priority' => ['required', 'in:' . implode(',', array_column(TicketPriority::cases(), 'value'))],
            'admin_notes' => ['nullable', 'string'],
        ]);

        // -----------------------------
        // 2. Create ticket
        // -----------------------------
        Ticket::create($validated);

        // -----------------------------
        // 3. Redirect with success
        // -----------------------------
        return redirect()
            ->route('admin.tickets.index')
            ->with('success', 'Ticket created successfully.');
    }

    /**
     * Display a single ticket details page.
     */
    public function show(string $id): View
    {
        // -----------------------------
        // 1. Fetch ticket with client
        // -----------------------------
        $ticket = Ticket::with('client')->findOrFail($id);

        // -----------------------------
        // 2. Fetch sidebar tickets list
        // -----------------------------
        $tickets = Ticket::with('client')->latest('id')->paginate(10);

        // -----------------------------
        // 3. Return view
        // -----------------------------
        return view('admin.ticket.show', compact('ticket', 'tickets'));
    }

    /**
     * Show form for editing a ticket.
     */
    public function edit(string $id): View
    {
        // -----------------------------
        // 1. Fetch ticket
        // -----------------------------
        $ticket = Ticket::findOrFail($id);

        // -----------------------------
        // 2. Fetch clients for current owner
        // -----------------------------
        $clients = Client::where('parent_id', owner_client_id())->get();

        // -----------------------------
        // 3. Return view
        // -----------------------------
        return view('admin.ticket.form', [
            'ticket' => $ticket,
            'clients' => $clients,
            'ticket_number' => $ticket->ticket_number,
        ]);
    }

    /**
     * Update an existing ticket.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        // -----------------------------
        // 1. Fetch ticket
        // -----------------------------
        $ticket = Ticket::findOrFail($id);

        // -----------------------------
        // 2. Validate request data
        // -----------------------------
        $validated = $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'ticket_number' => ['required', 'unique:tickets,ticket_number,' . $ticket->id],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'status' => ['required', 'in:' . implode(',', array_column(TicketStatus::cases(), 'value'))],
            'priority' => ['required', 'in:' . implode(',', array_column(TicketPriority::cases(), 'value'))],
            'admin_notes' => ['nullable', 'string'],
        ]);

        // -----------------------------
        // 3. Update ticket
        // -----------------------------
        $ticket->update($validated);

        // -----------------------------
        // 4. Redirect with success
        // -----------------------------
        return redirect()
            ->route('admin.tickets.index')
            ->with('success', 'Ticket updated successfully.');
    }

    /**
     * Delete a ticket.
     */
    public function destroy(string $id): RedirectResponse
    {
        // -----------------------------
        // 1. Fetch and delete ticket
        // -----------------------------
        Ticket::findOrFail($id)->delete();

        // -----------------------------
        // 2. Redirect with success
        // -----------------------------
        return redirect()
            ->route('admin.tickets.index')
            ->with('success', 'Ticket deleted successfully.');
    }
}
