<?php

use App\Domain\Clients\Models\Client;
use App\Domain\Invoices\Models\Invoice;
use App\Domain\Packages\Models\Package;
use App\Enums\InvoiceStatus;
use App\Enums\PaymentMethod;
use App\Mail\InvoiceMail;
use Illuminate\Support\Facades\Mail;

test('admin can list invoices', function () {
    $admin = createSuperAdmin();
    $invoices = Invoice::factory()->count(2)->create();

    $response = $this->actingAs($admin, 'admin')
        ->get(route('admin.invoices.index'));

    $response->assertOk()
        ->assertViewIs('admin.invoice.index')
        ->assertSee($invoices->first()->invoice_number);
});

test('admin can create manual invoice', function () {
    $admin = createSuperAdmin();
    $client = Client::factory()->create();
    $package = Package::factory()->create();

    $response = $this->actingAs($admin, 'admin')
        ->post(route('admin.invoices.store'), [
            'invoice_number' => 'INV9999',
            'client_id' => $client->id,
            'package_id' => $package->id,
            'billing_start' => now()->startOfMonth()->toDateString(),
            'billing_end' => now()->endOfMonth()->toDateString(),
            'due_date' => now()->addDays(5)->toDateString(),
            'invoice_amount' => 1200,
            'status' => InvoiceStatus::UNPAID->value,
        ]);

    $response->assertRedirect(route('admin.invoices.index'));
    $this->assertDatabaseHas('invoices', [
        'invoice_number' => 'INV9999',
        'invoice_amount' => 1200,
    ]);
});

test('admin can view single invoice', function () {
    $admin = createSuperAdmin();
    $invoice = Invoice::factory()->create();

    $response = $this->actingAs($admin, 'admin')
        ->get(route('admin.invoices.show', $invoice));

    $response->assertOk()
        ->assertViewIs('admin.invoice.show')
        ->assertSee($invoice->invoice_number);
});

test('admin can update invoice status to paid', function () {
    $admin = createSuperAdmin();
    $invoice = Invoice::factory()->create(['status' => InvoiceStatus::UNPAID]);

    $response = $this->actingAs($admin, 'admin')
        ->put(route('admin.invoices.update', $invoice), [
            'invoice_number' => $invoice->invoice_number,
            'client_id' => $invoice->client_id,
            'package_id' => $invoice->package_id,
            'billing_start' => $invoice->billing_start->toDateString(),
            'billing_end' => $invoice->billing_end->toDateString(),
            'due_date' => $invoice->due_date?->toDateString(),
            'invoice_amount' => $invoice->invoice_amount,
            'status' => InvoiceStatus::PAID->value,
            'payment_method' => PaymentMethod::ONLINE->value,
            'trx_id' => 'TRX-TEST-1234',
        ]);

    $response->assertRedirect(route('admin.invoices.index'));
    expect($invoice->refresh()->status)->toBe(InvoiceStatus::PAID)
        ->and($invoice->trx_id)->toBe('TRX-TEST-1234');
});

test('admin can send invoice email to client', function () {
    Mail::fake();
    $admin = createSuperAdmin();
    $invoice = Invoice::factory()->create();

    $response = $this->actingAs($admin, 'admin')
        ->post(route('admin.invoices.send', $invoice));

    $response->assertRedirect();
    Mail::assertSent(InvoiceMail::class);
});
