<?php

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Ledger;
use App\Models\LedgerCategory;
use App\Models\Member;
use App\Models\Payment;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Ticket;

test('client cannot access another clients member', function () {
    $clientA = createActiveClient();
    $clientB = createActiveClient();

    $memberB = Member::factory()->create(['client_id' => $clientB->id]);

    $response = $this->actingAs($clientA, 'client')
        ->get(route('client.members.show', $memberB));

    expect($response->status())->toBeIn([403, 404]);

    $updateResponse = $this->actingAs($clientA, 'client')
        ->put(route('client.members.update', $memberB), [
            'name' => 'Hacked Name',
            'status' => 1,
            'share_quantity' => 1,
        ]);

    expect($updateResponse->status())->toBeIn([403, 404]);

    $deleteResponse = $this->actingAs($clientA, 'client')
        ->delete(route('client.members.destroy', $memberB));

    expect($deleteResponse->status())->toBeIn([403, 404]);
    $this->assertDatabaseHas('members', ['id' => $memberB->id]);
});

test('client cannot access another clients project', function () {
    $clientA = createActiveClient();
    $clientB = createActiveClient();

    $catB = ProjectCategory::factory()->create(['client_id' => $clientB->id]);
    $projectB = Project::factory()->create([
        'client_id' => $clientB->id,
        'project_category_id' => $catB->id,
    ]);

    $response = $this->actingAs($clientA, 'client')
        ->get(route('client.projects.show', $projectB));

    expect($response->status())->toBeIn([403, 404]);
});

test('client cannot access another clients ledger entry', function () {
    $clientA = createActiveClient();
    $clientB = createActiveClient();

    $catB = LedgerCategory::factory()->create(['client_id' => $clientB->id]);
    $ledgerB = Ledger::factory()->create([
        'client_id' => $clientB->id,
        'ledger_category_id' => $catB->id,
    ]);

    $response = $this->actingAs($clientA, 'client')
        ->get(route('client.ledgers.show', $ledgerB));

    expect($response->status())->toBeIn([403, 404]);
});

test('client cannot access another clients ticket', function () {
    $clientA = createActiveClient();
    $clientB = createActiveClient();

    $ticketB = Ticket::factory()->create(['client_id' => $clientB->id]);

    $response = $this->actingAs($clientA, 'client')
        ->get(route('client.tickets.edit', $ticketB));

    expect($response->status())->toBeIn([403, 404]);

    $chatResponse = $this->actingAs($clientA, 'client')
        ->get(route('client.tickets.chat', $ticketB));

    expect($chatResponse->status())->toBeIn([403, 404]);
});

test('client cannot access another clients sub-users', function () {
    $clientA = createActiveClient();
    $clientB = createActiveClient();

    $subUserB = Client::factory()->subUser($clientB)->create();

    $response = $this->actingAs($clientA, 'client')
        ->get(route('client.users.show', $subUserB));

    expect($response->status())->toBeIn([403, 404]);
});

test('client cannot access another clients invoice or payment', function () {
    $clientA = createActiveClient();
    $clientB = createActiveClient();

    $invoiceB = Invoice::factory()->create(['client_id' => $clientB->id]);
    $paymentB = Payment::factory()->create(['client_id' => $clientB->id]);

    $invResponse = $this->actingAs($clientA, 'client')
        ->get(route('client.invoices.show', $invoiceB));
    expect($invResponse->status())->toBeIn([403, 404]);

    $payResponse = $this->actingAs($clientA, 'client')
        ->get(route('client.payments.show', $paymentB));
    expect($payResponse->status())->toBeIn([403, 404]);
});
