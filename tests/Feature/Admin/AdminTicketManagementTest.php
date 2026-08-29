<?php

use App\Domain\Clients\Models\Client;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Ticket;

test('admin can view tickets list', function () {
    $admin = createSuperAdmin();
    $tickets = Ticket::factory()->count(3)->create();

    $response = $this->actingAs($admin, 'admin')
        ->get(route('admin.tickets.index'));

    $response->assertOk()
        ->assertViewIs('admin.ticket.index')
        ->assertSee($tickets->first()->ticket_number);
});

test('admin can create ticket', function () {
    $admin = createSuperAdmin();
    $client = Client::factory()->create();

    $response = $this->actingAs($admin, 'admin')
        ->post(route('admin.tickets.store'), [
            'client_id' => $client->id,
            'ticket_number' => 'TKT9999',
            'subject' => 'Server Maintenance',
            'message' => 'Scheduled maintenance tonight.',
            'status' => TicketStatus::OPEN->value,
            'priority' => TicketPriority::HIGH->value,
        ]);

    $response->assertRedirect(route('admin.tickets.index'));
    $this->assertDatabaseHas('tickets', [
        'ticket_number' => 'TKT9999',
        'subject' => 'Server Maintenance',
    ]);
});

test('admin can view ticket and ticket chat', function () {
    $admin = createSuperAdmin();
    $ticket = Ticket::factory()->create();

    $response = $this->actingAs($admin, 'admin')
        ->get(route('admin.tickets.show', $ticket));

    $response->assertOk()
        ->assertViewIs('admin.ticket.show');

    $chatResponse = $this->actingAs($admin, 'admin')
        ->get(route('admin.tickets.chat', $ticket));

    $chatResponse->assertOk()
        ->assertViewIs('admin.ticket.chat');
});

test('admin can update ticket status and priority', function () {
    $admin = createSuperAdmin();
    $ticket = Ticket::factory()->create(['status' => TicketStatus::OPEN]);

    $response = $this->actingAs($admin, 'admin')
        ->put(route('admin.tickets.update', $ticket), [
            'client_id' => $ticket->client_id,
            'ticket_number' => $ticket->ticket_number,
            'subject' => $ticket->subject,
            'message' => $ticket->message,
            'status' => TicketStatus::CLOSED->value,
            'priority' => TicketPriority::LOW->value,
            'admin_notes' => 'Resolved after inspection.',
        ]);

    $response->assertRedirect(route('admin.tickets.index'));
    expect($ticket->refresh()->status)->toBe(TicketStatus::CLOSED)
        ->and($ticket->admin_notes)->toBe('Resolved after inspection.');
});

test('admin can delete a ticket', function () {
    $admin = createSuperAdmin();
    $ticket = Ticket::factory()->create();

    $response = $this->actingAs($admin, 'admin')
        ->delete(route('admin.tickets.destroy', $ticket));

    $response->assertRedirect(route('admin.tickets.index'));
    $this->assertDatabaseMissing('tickets', ['id' => $ticket->id]);
});
