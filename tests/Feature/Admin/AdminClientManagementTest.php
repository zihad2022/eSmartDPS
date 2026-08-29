<?php

use App\Models\Client;
use App\Models\Package;
use Illuminate\Support\Facades\Mail;

test('admin can view clients list', function () {
    $admin = createSuperAdmin();
    $clients = Client::factory()->count(3)->create();

    $response = $this->actingAs($admin, 'admin')
        ->get(route('admin.clients.index'));

    $response->assertOk()
        ->assertViewIs('admin.client.index')
        ->assertSee($clients->first()->first_name);
});

test('admin can render create client form', function () {
    $admin = createSuperAdmin();
    Package::factory()->create(['is_active' => true]);

    $response = $this->actingAs($admin, 'admin')
        ->get(route('admin.clients.create'));

    $response->assertOk()
        ->assertViewIs('admin.client.form');
});

test('admin can store a new client', function () {
    Mail::fake();
    $admin = createSuperAdmin();
    $package = Package::factory()->create(['is_active' => true]);

    $response = $this->actingAs($admin, 'admin')
        ->post(route('admin.clients.store'), [
            'first_name' => 'Alice',
            'last_name' => 'Smith',
            'email' => 'alice@example.com',
            'phone' => '01700112233',
            'password' => 'Password123!',
            'package_id' => $package->id,
            'status' => 1,
        ]);

    $response->assertRedirect(route('admin.clients.index'));
    $this->assertDatabaseHas('clients', [
        'email' => 'alice@example.com',
        'first_name' => 'Alice',
    ]);
});

test('admin can view a single client', function () {
    $admin = createSuperAdmin();
    $client = Client::factory()->create();

    $response = $this->actingAs($admin, 'admin')
        ->get(route('admin.clients.show', $client));

    $response->assertOk()
        ->assertViewIs('admin.client.show')
        ->assertSee($client->first_name);
});

test('admin can update a client', function () {
    $admin = createSuperAdmin();
    $package = Package::factory()->create(['is_active' => true]);
    $client = Client::factory()->create(['first_name' => 'OldName']);

    $response = $this->actingAs($admin, 'admin')
        ->put(route('admin.clients.update', $client), [
            'first_name' => 'NewName',
            'last_name' => 'UpdatedLast',
            'email' => $client->email,
            'phone' => $client->phone,
            'package_id' => $package->id,
            'status' => 1,
        ]);

    $response->assertRedirect(route('admin.clients.index'));
    expect($client->refresh()->first_name)->toBe('NewName');
});

test('admin can delete a client', function () {
    $admin = createSuperAdmin();
    $client = Client::factory()->create();

    $response = $this->actingAs($admin, 'admin')
        ->delete(route('admin.clients.destroy', $client));

    $response->assertRedirect(route('admin.clients.index'));
    $this->assertDatabaseMissing('clients', ['id' => $client->id]);
});

test('admin can impersonate a client and stop impersonation', function () {
    $admin = createSuperAdmin();
    $client = createActiveClient();

    $response = $this->actingAs($admin, 'admin')
        ->post(route('admin.client.impersonate', $client));

    $response->assertRedirect(route('client.dashboard'));
    $this->assertAuthenticatedAs($client, 'client');

    $stopResponse = $this->post(route('admin.client.impersonate.stop'));
    $stopResponse->assertRedirect(route('admin.dashboard'));
    $this->assertAuthenticatedAs($admin, 'admin');
});
