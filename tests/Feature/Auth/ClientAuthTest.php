<?php

use App\Mail\ClientWelcomeMail;
use App\Models\Client;
use App\Models\Package;
use Illuminate\Support\Facades\Mail;

test('client login screen can be rendered', function () {
    $response = $this->get(route('client.login'));

    $response->assertOk()
        ->assertViewIs('client.auth.login');
});

test('client can authenticate using user_id and password', function () {
    $client = Client::factory()->create([
        'user_id' => 'UID99',
        'password' => 'secret123',
        'status' => true,
    ]);

    $response = $this->post(route('client.authenticate'), [
        'user_id' => 'UID99',
        'password' => 'secret123',
    ]);

    $this->assertAuthenticatedAs($client, 'client');
    $response->assertRedirect(route('client.dashboard'));
});

test('client cannot authenticate with incorrect password', function () {
    $client = Client::factory()->create([
        'user_id' => 'UID99',
        'password' => 'secret123',
        'status' => true,
    ]);

    $response = $this->post(route('client.authenticate'), [
        'user_id' => 'UID99',
        'password' => 'wrongpassword',
    ]);

    $this->assertGuest('client');
    $response->assertSessionHasErrors('password');
});

test('client cannot authenticate with invalid user_id', function () {
    $response = $this->post(route('client.authenticate'), [
        'user_id' => 'NONEXISTENT',
        'password' => 'secret123',
    ]);

    $this->assertGuest('client');
    $response->assertSessionHasErrors('user_id');
});

test('inactive client cannot authenticate', function () {
    $client = Client::factory()->inactive()->create([
        'user_id' => 'UID99',
        'password' => 'secret123',
    ]);

    $response = $this->post(route('client.authenticate'), [
        'user_id' => 'UID99',
        'password' => 'secret123',
    ]);

    $this->assertGuest('client');
    $response->assertRedirect(route('client.login'))
        ->assertSessionHas('error');
});

test('new client can register with a package', function () {
    Mail::fake();

    $package = Package::factory()->create([
        'price' => 1000,
        'has_trial' => false,
    ]);

    $response = $this->post(route('client.register.store'), [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john.doe@example.com',
        'phone' => '01711223344',
        'password' => 'password123',
        'package_id' => $package->id,
    ]);

    $response->assertRedirect(route('client.auth.success', ['id' => $package->id]));

    $this->assertDatabaseHas('clients', [
        'email' => 'john.doe@example.com',
        'phone' => '01711223344',
    ]);

    Mail::assertSent(ClientWelcomeMail::class);
});

test('client registration rejects duplicate email', function () {
    Mail::fake();

    Client::factory()->create(['email' => 'duplicate@example.com']);
    $package = Package::factory()->create();

    $response = $this->post(route('client.register.store'), [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'duplicate@example.com',
        'phone' => '01711999999',
        'password' => 'password123',
        'package_id' => $package->id,
    ]);

    $response->assertSessionHasErrors('email');
});

test('client can logout', function () {
    $client = Client::factory()->create([
        'user_id' => 'UID99',
        'password' => 'secret123',
    ]);

    $this->post(route('client.authenticate'), [
        'user_id' => 'UID99',
        'password' => 'secret123',
    ]);

    $this->assertAuthenticatedAs($client, 'client');

    $response = $this->post(route('client.logout'));

    $this->assertGuest('client');
    $response->assertRedirect(route('client.login'));
});
