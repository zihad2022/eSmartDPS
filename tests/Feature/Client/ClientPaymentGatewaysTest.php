<?php

use App\Enums\InvoiceStatus;
use App\Enums\PaymentMethod;
use App\Models\AdminSetting;
use App\Models\Invoice;
use App\Models\Package;
use App\Services\Payments\SslcommerzService;

beforeEach(function () {
    AdminSetting::factory()->create();
});

test('client can view checkout page for unpaid invoice', function () {
    $package = Package::factory()->create();
    $client = createActiveClient([], $package);
    $invoice = Invoice::factory()->create([
        'client_id' => $client->id,
        'package_id' => $package->id,
        'status' => InvoiceStatus::UNPAID,
    ]);

    $response = $this->actingAs($client, 'client')
        ->get(route('client.checkout.create', $invoice));

    $response->assertOk()
        ->assertViewIs('client.checkout')
        ->assertSee($invoice->invoice_number);
});

test('client can view payment method selection for unpaid invoice', function () {
    $package = Package::factory()->create();
    $client = createActiveClient([], $package);
    $invoice = Invoice::factory()->create([
        'client_id' => $client->id,
        'package_id' => $package->id,
        'status' => InvoiceStatus::UNPAID,
    ]);

    $response = $this->actingAs($client, 'client')
        ->get(route('client.payments.select', $invoice));

    $response->assertOk()
        ->assertViewIs('client.payments.select-method')
        ->assertSee('bKash')
        ->assertSee('SSLCommerz');
});

test('already paid invoice cannot enter select payment method and redirects', function () {
    $package = Package::factory()->create();
    $client = createActiveClient([], $package);
    $invoice = Invoice::factory()->create([
        'client_id' => $client->id,
        'package_id' => $package->id,
        'status' => InvoiceStatus::PAID,
    ]);

    $response = $this->actingAs($client, 'client')
        ->get(route('client.payments.select', $invoice));

    $response->assertRedirect(route('client.invoices.index'))
        ->assertSessionHas('info', 'This invoice is already paid.');
});

test('client initiating sslcommerz payment is redirected to gateway URL', function () {
    $package = Package::factory()->create();
    $client = createActiveClient([], $package);
    $invoice = Invoice::factory()->create([
        'client_id' => $client->id,
        'package_id' => $package->id,
        'status' => InvoiceStatus::UNPAID,
    ]);

    $mockSsl = mock(SslcommerzService::class);
    $mockSsl->shouldReceive('initiate')
        ->once()
        ->with(Mockery::on(fn ($arg) => $arg->id === $invoice->id))
        ->andReturn([
            'ok' => true,
            'url' => 'https://sandbox.sslcommerz.com/gwprocess/v4/api.php?Q=test',
            'request' => ['tran_id' => 'TRX-SSL-12345'],
        ]);

    app()->instance(SslcommerzService::class, $mockSsl);

    $response = $this->actingAs($client, 'client')
        ->post(route('client.payments.process', $invoice), [
            'payment_method' => 'sslcommerz',
        ]);

    $response->assertRedirect('https://sandbox.sslcommerz.com/gwprocess/v4/api.php?Q=test');
    expect($invoice->refresh()->payment_reference)->toBe('TRX-SSL-12345');
});

test('sslcommerz success callback marks invoice paid and renews subscription', function () {
    $package = Package::factory()->create();
    $client = createActiveClient([], $package);
    $invoice = Invoice::factory()->create([
        'client_id' => $client->id,
        'package_id' => $package->id,
        'status' => InvoiceStatus::UNPAID,
    ]);

    $mockSsl = mock(SslcommerzService::class);
    $mockSsl->shouldReceive('validate')
        ->once()
        ->andReturn(true);

    app()->instance(SslcommerzService::class, $mockSsl);

    $response = $this->post(route('client.payments.sslcommerz.success', $invoice), [
        'tran_id' => 'TRX-VALID-777',
        'val_id' => 'VAL-12345',
        'amount' => $invoice->invoice_amount,
    ]);

    $response->assertRedirect(route('client.dashboard'))
        ->assertSessionHas('success');

    $invoice->refresh();
    expect($invoice->status)->toBe(InvoiceStatus::PAID)
        ->and($invoice->trx_id)->toBe('TRX-VALID-777')
        ->and($invoice->payment_method)->toBe(PaymentMethod::ONLINE);

    $this->assertAuthenticatedAs($client, 'client');
});

test('sslcommerz fail and cancel callbacks redirect with appropriate alert message', function () {
    $invoice = Invoice::factory()->create();

    $failResponse = $this->get(route('client.payments.sslcommerz.fail', $invoice));
    $failResponse->assertRedirect(route('client.invoices.index'))
        ->assertSessionHas('error');

    $cancelResponse = $this->get(route('client.payments.sslcommerz.cancel', $invoice));
    $cancelResponse->assertRedirect(route('client.invoices.index'))
        ->assertSessionHas('info');
});
