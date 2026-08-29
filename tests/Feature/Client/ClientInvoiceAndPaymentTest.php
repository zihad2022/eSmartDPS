<?php

use App\Models\AdminSetting;
use App\Models\Invoice;
use App\Models\Member;
use App\Models\Package;
use App\Models\Payment;

beforeEach(function () {
    AdminSetting::factory()->create();
});

test('client can list own invoices', function () {
    $client = createActiveClient();
    $package = Package::factory()->create();
    $invoices = Invoice::factory()->count(2)->create([
        'client_id' => $client->id,
        'package_id' => $package->id,
    ]);

    $response = $this->actingAs($client, 'client')
        ->get(route('client.invoices.index'));

    $response->assertOk()
        ->assertViewIs('client.invoice.index')
        ->assertSee($invoices->first()->invoice_number);
});

test('client can view own invoice details', function () {
    AdminSetting::factory()->create();
    $client = createActiveClient();
    $package = Package::factory()->create();
    $invoice = Invoice::factory()->create([
        'client_id' => $client->id,
        'package_id' => $package->id,
    ]);

    $response = $this->actingAs($client, 'client')
        ->get(route('client.invoices.show', $invoice));

    $response->assertOk()
        ->assertViewIs('client.invoice.show')
        ->assertSee($invoice->invoice_number);
});

test('client cannot view another clients invoice', function () {
    $client = createActiveClient();
    $otherClient = createActiveClient();
    $invoice = Invoice::factory()->create([
        'client_id' => $otherClient->id,
    ]);

    $response = $this->actingAs($client, 'client')
        ->get(route('client.invoices.show', $invoice));

    $response->assertForbidden();
});

test('client can view payments list and single payment', function () {
    $client = createActiveClient();
    $member = Member::factory()->create(['client_id' => $client->id]);
    $payment = Payment::factory()->create([
        'client_id' => $client->id,
        'member_id' => $member->id,
    ]);

    $response = $this->actingAs($client, 'client')
        ->get(route('client.payments.index'));

    $response->assertOk()
        ->assertViewIs('client.payment.index')
        ->assertSee($payment->payment_id);

    $showResponse = $this->actingAs($client, 'client')
        ->get(route('client.payments.show', $payment));

    $showResponse->assertOk()
        ->assertViewIs('client.payment.show');
});
