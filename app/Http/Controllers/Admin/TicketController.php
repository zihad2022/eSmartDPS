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

        // Filter by status
        if ($status = $request->query('status')) {
            if (isset($statusMap[$status])) {
                $query->where('status', $statusMap[$status]);
            }
        }

        // Filter by priority
        if ($priority = $request->query('priority')) {
            if (isset($priorityMap[$priority])) {
                $query->where('priority', $priorityMap[$priority]);
            }
        }

        $tickets = $query->paginate(10)->appends($request->query());

        // ✅ Collect status counts
        $statusCounts = [];
        foreach (TicketStatus::cases() as $status) {
            $statusCounts[$status->value] = Ticket::where('status', $status)->count();
        }

        // ✅ Collect priority counts
        $priorityCounts = [];
        foreach (TicketPriority::cases() as $priority) {
            $priorityCounts[$priority->value] = Ticket::where('priority', $priority)->count();
        }

        return view('admin.ticket.index', [
            'tickets' => $tickets,
            'totalTickets' => Ticket::count(),
            'statusCounts' => $statusCounts,
            'priorityCounts' => $priorityCounts,
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
