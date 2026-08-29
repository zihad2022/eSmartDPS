<?php

use App\Domain\Clients\Models\Client;
use App\Domain\Clients\Models\ClientPackage;
use App\Domain\Invoices\Models\Invoice;
use App\Domain\Packages\Models\Package;
use App\Models\AdminSetting;

test('client can view packages list', function () {
    AdminSetting::factory()->create();
    $client = Client::factory()->create();
    $packages = Package::factory()->count(2)->create(['is_active' => true]);

    $response = $this->actingAs($client, 'client')
        ->get(route('client.subscription.packages'));

    $response->assertOk()
        ->assertViewIs('client.subscription.packages')
        ->assertSee($packages->first()->name);
});

test('client can start a trial subscription', function () {
    $client = Client::factory()->create();
    $package = Package::factory()->trial(14)->create(['is_active' => true]);

    $response = $this->actingAs($client, 'client')
        ->get(route('client.subscription.start.trial', $package));

    $response->assertRedirect();
    $this->assertDatabaseHas('client_packages', [
        'client_id' => $client->id,
        'package_id' => $package->id,
        'is_trial' => true,
        'status' => ClientPackage::STATUS_ACTIVE,
    ]);
});

test('client can start a paid subscription', function () {
    $client = Client::factory()->create();
    $package = Package::factory()->create(['is_active' => true]);

    $response = $this->actingAs($client, 'client')
        ->get(route('client.subscription.start.paid', $package));

    $response->assertRedirect();
    $this->assertDatabaseHas('client_packages', [
        'client_id' => $client->id,
        'package_id' => $package->id,
        'is_trial' => false,
        'status' => ClientPackage::STATUS_ACTIVE,
    ]);
});

test('client can view expired subscription page with invoice', function () {
    $client = Client::factory()->create();
    $invoice = Invoice::factory()->create(['client_id' => $client->id]);

    $response = $this->actingAs($client, 'client')
        ->get(route('client.subscription.expired'));

    $response->assertOk()
        ->assertViewIs('client.subscription.expired');
});
