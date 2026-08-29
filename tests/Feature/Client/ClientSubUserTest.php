<?php

use App\Domain\Clients\Models\Client;

test('client can list sub-users', function () {
    $client = createActiveClient();
    $subUser = Client::factory()->subUser($client)->create();

    $response = $this->actingAs($client, 'client')
        ->get(route('client.users.index'));

    $response->assertOk()
        ->assertViewIs('client.user.index')
        ->assertSee($subUser->first_name);
});

test('client can create sub-user', function () {
    $client = createActiveClient();

    $response = $this->actingAs($client, 'client')
        ->post(route('client.users.store'), [
            'first_name' => 'Bob',
            'last_name' => 'Manager',
            'email' => 'bob.manager@example.com',
            'phone' => '01712345678',
            'password' => 'secret123',
            'role' => 'manager',
            'status' => 1,
        ]);

    $response->assertRedirect(route('client.users.index'));
    $this->assertDatabaseHas('clients', [
        'parent_id' => $client->id,
        'email' => 'bob.manager@example.com',
        'role' => 'manager',
    ]);
});

test('client can update sub-user', function () {
    $client = createActiveClient();
    $subUser = Client::factory()->subUser($client)->create(['first_name' => 'OldName']);

    $response = $this->actingAs($client, 'client')
        ->put(route('client.users.update', $subUser), [
            'first_name' => 'NewName',
            'last_name' => $subUser->last_name,
            'role' => 'editor',
            'status' => 1,
        ]);

    $response->assertRedirect(route('client.users.index'));
    expect($subUser->refresh()->first_name)->toBe('NewName')
        ->and($subUser->role)->toBe('editor');
});

test('client can delete sub-user', function () {
    $client = createActiveClient();
    $subUser = Client::factory()->subUser($client)->create();

    $response = $this->actingAs($client, 'client')
        ->delete(route('client.users.destroy', $subUser));

    $response->assertRedirect(route('client.users.index'));
    $this->assertDatabaseMissing('clients', ['id' => $subUser->id]);
});
