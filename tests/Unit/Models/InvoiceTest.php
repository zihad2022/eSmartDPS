<?php

use App\Enums\InvoiceStatus;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Package;

test('invoice status scopes filter properly', function () {
    $client = Client::factory()->create();
    $package = Package::factory()->create();

    $unpaid = Invoice::factory()->create([
        'status' => InvoiceStatus::UNPAID,
    ]);

    $paid = Invoice::factory()->paid()->create([
        'status' => InvoiceStatus::PAID,
    ]);

    $refunded = Invoice::factory()->refunded()->create([
        'status' => InvoiceStatus::REFUNDED,
    ]);

    $cancelled = Invoice::factory()->cancelled()->create([
        'status' => InvoiceStatus::CANCELLED,
    ]);

    expect(Invoice::unpaid()->count())->toBe(1)
        ->and(Invoice::paid()->count())->toBe(1)
        ->and(Invoice::refunded()->count())->toBe(1)
        ->and(Invoice::cancelled()->count())->toBe(1)
        ->and($paid->isPaid())->toBeTrue()
        ->and($unpaid->isPaid())->toBeFalse();
});

test('invoice belongs to client and package', function () {
    $client = Client::factory()->create();
    $package = Package::factory()->create();

    $invoice = Invoice::factory()->create([
        'client_id' => $client->id,
        'package_id' => $package->id,
    ]);

    expect($invoice->client->id)->toBe($client->id)
        ->and($invoice->package->id)->toBe($package->id);
});
