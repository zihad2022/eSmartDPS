<?php

use App\Enums\TicketPriority;
use App\Models\Ticket;

test('client can list own tickets', function () {
    $client = createActiveClient();
    $tickets = Ticket::factory()->count(2)->create(['client_id' => $client->id]);

    $response = $this->actingAs($client, 'client')
        ->get(route('client.tickets.index'));

    $response->assertOk()
        ->assertViewIs('client.ticket.index')
        ->assertSee($tickets->first()->ticket_number);
});

test('client can create a support ticket', function () {
    $client = createActiveClient();

    $response = $this->actingAs($client, 'client')
        ->post(route('client.tickets.store'), [
            'ticket_number' => 'TCK123456',
            'subject' => 'Need Help With Billing',
            'priority' => TicketPriority::HIGH->value,
            'message' => 'Please explain invoice breakdown.',
        ]);

    $response->assertRedirect(route('client.tickets.index'));
    $this->assertDatabaseHas('tickets', [
        'client_id' => $client->id,
        'ticket_number' => 'TCK123456',
        'subject' => 'Need Help With Billing',
    ]);
});

test('client can view ticket chat interface and edit ticket', function () {
    $client = createActiveClient();
    $ticket = Ticket::factory()->create(['client_id' => $client->id]);

    $chatResponse = $this->actingAs($client, 'client')
        ->get(route('client.tickets.chat', $ticket));

    $chatResponse->assertOk()
        ->assertViewIs('client.ticket.chat');

    $editResponse = $this->actingAs($client, 'client')
        ->get(route('client.tickets.edit', $ticket));

    $editResponse->assertOk()
        ->assertViewIs('client.ticket.form');
});
