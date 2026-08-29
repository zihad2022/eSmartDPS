<?php

test('client can view and update general settings', function () {
    $client = createActiveClient();

    $response = $this->actingAs($client, 'client')
        ->get(route('client.settings.general.edit'));

    $response->assertOk()
        ->assertViewIs('client.settings.general');

    $updateResponse = $this->actingAs($client, 'client')
        ->put(route('client.settings.general.update'), [
            'organization_name' => 'Updated Org Name',
            'short_name' => 'UON',
            'contact_email' => 'contact@example.com',
            'contact_phone' => '01700000000',
            'address' => 'Dhaka, Bangladesh',
            'currency' => 'BDT',
        ]);

    $updateResponse->assertRedirect(route('client.settings.general.edit'));
    $this->assertDatabaseHas('client_settings', [
        'client_id' => $client->id,
        'organization_name' => 'Updated Org Name',
    ]);
});

test('client can view share settings', function () {
    $client = createActiveClient();

    $response = $this->actingAs($client, 'client')
        ->get(route('client.settings.share.edit'));

    $response->assertOk()
        ->assertViewIs('client.settings.share');
});

test('client can view payment settings', function () {
    $client = createActiveClient();

    $response = $this->actingAs($client, 'client')
        ->get(route('client.settings.payment.edit'));

    $response->assertOk()
        ->assertViewIs('client.settings.payment');
});
