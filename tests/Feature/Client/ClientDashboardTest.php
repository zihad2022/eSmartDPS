<?php

use App\Domain\Clients\Models\Client;
use App\Models\Member;
use App\Models\Project;

test('unauthenticated user is redirected from client dashboard to login', function () {
    $response = $this->get(route('client.dashboard'));

    $response->assertRedirect(route('client.login'));
});

test('client without active subscription is redirected to subscription expired page', function () {
    $client = Client::factory()->create();

    $response = $this->actingAs($client, 'client')
        ->get(route('client.dashboard'));

    $response->assertRedirect(route('client.subscription.expired'));
});

test('client with active subscription can view dashboard with metrics', function () {
    $client = createActiveClient();
    Member::factory()->count(2)->create(['client_id' => $client->id]);
    Project::factory()->count(1)->create(['client_id' => $client->id]);

    $response = $this->actingAs($client, 'client')
        ->get(route('client.dashboard'));

    $response->assertOk()
        ->assertViewIs('client.dashboard')
        ->assertViewHas('totalMembers', 2)
        ->assertViewHas('totalProjects', 1);
});
