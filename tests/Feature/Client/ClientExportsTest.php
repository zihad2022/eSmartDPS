<?php

use App\Models\Client;
use Maatwebsite\Excel\Facades\Excel;

beforeEach(function () {
    Excel::fake();
});

test('client can export members to excel', function () {
    $client = createActiveClient();

    $response = $this->actingAs($client, 'client')
        ->get(route('client.members.export'));

    $response->assertOk();
    Excel::assertDownloaded('members.xlsx');
});

test('client can export projects to excel', function () {
    $client = createActiveClient();

    $response = $this->actingAs($client, 'client')
        ->get(route('client.projects.export'));

    $response->assertOk();
    Excel::assertDownloaded('projects.xlsx');
});

test('client can export payments to excel', function () {
    $client = createActiveClient();

    $response = $this->actingAs($client, 'client')
        ->get(route('client.payments.export'));

    $response->assertOk();
    Excel::assertDownloaded('payments.xlsx');
});

test('client can export users to excel', function () {
    $client = createActiveClient();

    $response = $this->actingAs($client, 'client')
        ->get(route('client.users.export'));

    $response->assertOk();
    Excel::assertDownloaded('users.xlsx');
});

test('client can export tickets to excel', function () {
    $client = createActiveClient();

    $response = $this->actingAs($client, 'client')
        ->get(route('client.tickets.export'));

    $response->assertOk();
    Excel::assertDownloaded('tickets.xlsx');
});

test('guest cannot access client export endpoints', function () {
    $response = $this->get(route('client.members.export'));
    $response->assertRedirect(route('client.login'));
});
