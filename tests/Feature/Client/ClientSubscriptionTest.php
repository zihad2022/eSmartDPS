<?php

use App\Models\AdminSetting;
use App\Models\Client;
use App\Models\ClientPackage;
use App\Models\Invoice;
use App\Models\Package;

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

test('client can activate a free subscription immediately', function () {
    $client = Client::factory()->create();
    $package = Package::factory()->create([
        'price' => 0,
        'is_active' => true,
    ]);

    $response = $this->actingAs($client, 'client')
        ->get(route('client.subscription.start.paid', $package));

    $response->assertRedirect(route('client.dashboard'));
    $this->assertDatabaseHas('client_packages', [
        'client_id' => $client->id,
        'package_id' => $package->id,
        'is_trial' => false,
        'status' => ClientPackage::STATUS_ACTIVE,
    ]);
});

test('client selecting paid subscription creates invoice and redirects to payment', function () {
    $client = Client::factory()->create();
    $package = Package::factory()->create([
        'price' => 500,
        'is_active' => true,
    ]);

    $response = $this->actingAs($client, 'client')
        ->get(route('client.subscription.start.paid', $package));

    $this->assertDatabaseHas('invoices', [
        'client_id' => $client->id,
        'package_id' => $package->id,
        'invoice_amount' => 500,
    ]);

    $invoice = Invoice::where('client_id', $client->id)->first();
    $response->assertRedirect(route('client.payments.select', $invoice->id));
});

test('client can view expired subscription page with invoice', function () {
    $client = Client::factory()->create();
    $invoice = Invoice::factory()->create(['client_id' => $client->id]);

    $response = $this->actingAs($client, 'client')
        ->get(route('client.subscription.expired'));

    $response->assertOk()
        ->assertViewIs('client.subscription.expired')
        ->assertSee($invoice->invoice_number);
});
