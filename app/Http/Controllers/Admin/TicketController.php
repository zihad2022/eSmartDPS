<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\Tickets\CreateTicketAction;
use App\Actions\Admin\Tickets\DeleteTicketAction;
use App\Actions\Admin\Tickets\GetTicketDetailsAction;
use App\Actions\Admin\Tickets\GetTicketFormDataAction;
use App\Actions\Admin\Tickets\GetTicketsAction;
use App\Actions\Admin\Tickets\UpdateTicketAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TicketRequest;
use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(Request $request, GetTicketsAction $action): View
    {
        return view('admin.ticket.index', $action->execute(
            search: $request->string('search')->trim()->toString() ?: null,
            status: $request->string('status')->toString() ?: null,
            priority: $request->string('priority')->toString() ?: null,
        ));
    }

    public function create(GetTicketFormDataAction $action): View
    {
        return view('admin.ticket.form', $action->execute());
    }

    public function store(TicketRequest $request, CreateTicketAction $action): RedirectResponse
    {
        $action->execute($request->validated());

        return redirect()->route('admin.tickets.index')
            ->with('success', 'Ticket created successfully.');
    }

    public function show(Ticket $ticket, GetTicketDetailsAction $action): View
    {
        return view('admin.ticket.show', ['ticket' => $action->execute($ticket)]);
    }

    public function edit(Ticket $ticket, GetTicketFormDataAction $action): View
    {
        return view('admin.ticket.form', $action->execute($ticket));
    }

    public function update(
        TicketRequest $request,
        Ticket $ticket,
        UpdateTicketAction $action
    ): RedirectResponse {
        $action->execute($ticket, $request->validated());

        return redirect()->route('admin.tickets.index')
            ->with('success', 'Ticket updated successfully.');
    }

    public function destroy(Ticket $ticket, DeleteTicketAction $action): RedirectResponse
    {
        $action->execute($ticket);

        return redirect()->route('admin.tickets.index')
            ->with('success', 'Ticket deleted successfully.');
    }
}
