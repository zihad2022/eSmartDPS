<?php

use App\Enums\InvoiceStatus;
use App\Models\Admin;
use App\Models\AdminSetting;
use App\Models\Client;
use App\Models\ClientPackage;
use App\Models\Invoice;
use App\Models\Member;
use App\Models\Package;

beforeEach(function () {
    AdminSetting::factory()->create();
});

test('active trial subscription allows client portal access', function () {
    $client = Client::factory()->create();
    $package = Package::factory()->trial(14)->create();

    ClientPackage::factory()->create([
        'client_id' => $client->id,
        'package_id' => $package->id,
        'starts_at' => now()->subDay(),
        'ends_at' => now()->addDays(13),
        'is_trial' => true,
        'is_active' => true,
        'status' => ClientPackage::STATUS_ACTIVE,
    ]);

    $response = $this->actingAs($client, 'client')->get(route('client.dashboard'));

    $response->assertOk();
});

test('expired subscription automatically blocks client portal and redirects to expired page', function () {
    $client = Client::factory()->create();
    $package = Package::factory()->create();

    ClientPackage::factory()->create([
        'client_id' => $client->id,
        'package_id' => $package->id,
        'starts_at' => now()->subDays(31),
        'ends_at' => now()->subDay(),
        'is_trial' => false,
        'is_active' => true,
        'status' => ClientPackage::STATUS_ACTIVE,
    ]);

    $response = $this->actingAs($client, 'client')->get(route('client.dashboard'));

    $response->assertRedirect(route('client.subscription.expired'));
});

test('invoices:generate command generates invoice for newly expired trial and marks status expired', function () {
    $client = Client::factory()->create(['status' => true]);
    $package = Package::factory()->create(['price' => 1200]);

    $trial = ClientPackage::factory()->create([
        'client_id' => $client->id,
        'package_id' => $package->id,
        'starts_at' => now()->subDays(15),
        'ends_at' => now()->subDay(),
        'is_trial' => true,
        'is_active' => true,
        'status' => ClientPackage::STATUS_ACTIVE,
    ]);

    $this->artisan('invoices:generate')->assertSuccessful();

    $this->assertDatabaseHas('invoices', [
        'client_id' => $client->id,
        'package_id' => $package->id,
        'invoice_amount' => 1200,
        'status' => InvoiceStatus::UNPAID,
    ]);

    $trial->refresh();
    expect($trial->is_active)->toBeFalse()
        ->and($trial->status)->toBe(ClientPackage::STATUS_EXPIRED);
});

test('saas:generate-upcoming-invoices generates invoice 3 days prior to expiration', function () {
    $client = Client::factory()->create(['status' => true]);
    $package = Package::factory()->create(['price' => 2000]);

    ClientPackage::factory()->create([
        'client_id' => $client->id,
        'package_id' => $package->id,
        'starts_at' => now()->subDays(27),
        'ends_at' => now()->addDays(3)->startOfDay(),
        'is_trial' => false,
        'is_active' => true,
        'status' => ClientPackage::STATUS_ACTIVE,
    ]);

    $this->artisan('saas:generate-upcoming-invoices')->assertSuccessful();

    $this->assertDatabaseHas('invoices', [
        'client_id' => $client->id,
        'package_id' => $package->id,
        'invoice_amount' => 2000,
        'status' => InvoiceStatus::UNPAID,
    ]);
});

test('admin marking an invoice as paid automatically renews subscription and reactivates portal', function () {
    $admin = createSuperAdmin();

    $client = Client::factory()->create();
    $package = Package::factory()->create(['price' => 1500]);

    // Client had an expired package
    ClientPackage::factory()->create([
        'client_id' => $client->id,
        'package_id' => $package->id,
        'starts_at' => now()->subDays(40),
        'ends_at' => now()->subDays(10),
        'is_trial' => false,
        'is_active' => true,
        'status' => ClientPackage::STATUS_ACTIVE,
    ]);

    $invoice = Invoice::factory()->create([
        'client_id' => $client->id,
        'package_id' => $package->id,
        'invoice_amount' => 1500,
        'status' => InvoiceStatus::UNPAID,
    ]);

    // Admin updates invoice to paid
    $response = $this->actingAs($admin, 'admin')->put(route('admin.invoices.update', $invoice), [
        'invoice_number' => $invoice->invoice_number,
        'client_id' => $client->id,
        'package_id' => $package->id,
        'invoice_amount' => 1500,
        'billing_start' => now()->format('Y-m-d'),
        'billing_end' => now()->addMonth()->format('Y-m-d'),
        'due_date' => now()->addDays(7)->format('Y-m-d'),
        'status' => InvoiceStatus::PAID->value,
    ]);

    $response->assertRedirect(route('admin.invoices.index'));

    $this->assertDatabaseHas('invoices', [
        'id' => $invoice->id,
        'status' => InvoiceStatus::PAID,
    ]);

    // Client now has an active subscription and can access portal
    $activePackage = $client->activeClientPackage()->first();
    expect($activePackage)->not->toBeNull()
        ->and($activePackage->isActive())->toBeTrue();

    $portalResponse = $this->actingAs($client, 'client')->get(route('client.dashboard'));
    $portalResponse->assertOk();
});

test('downgrade safeguard blocks switching package when current resource limits are exceeded', function () {
    $client = Client::factory()->create();
    $currentPackage = Package::factory()->create([
        'member_limit' => 100,
        'project_limit' => 50,
    ]);

    ClientPackage::factory()->create([
        'client_id' => $client->id,
        'package_id' => $currentPackage->id,
        'starts_at' => now()->subDays(5),
        'ends_at' => now()->addDays(25),
        'is_trial' => false,
        'is_active' => true,
        'status' => ClientPackage::STATUS_ACTIVE,
    ]);

    // Client has 10 members
    Member::factory()->count(10)->create(['client_id' => $client->id]);

    // Lower package only allows 5 members
    $lowerPackage = Package::factory()->create([
        'price' => 0,
        'member_limit' => 5,
        'project_limit' => 50,
        'is_active' => true,
    ]);

    $response = $this->actingAs($client, 'client')
        ->get(route('client.subscription.start.paid', $lowerPackage));

    $response->assertSessionHas('error');

    // Active package remains unchanged
    expect($client->activeClientPackage()->first()->package_id)->toBe($currentPackage->id);
});
