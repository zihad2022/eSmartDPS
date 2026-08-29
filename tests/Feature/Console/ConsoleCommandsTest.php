<?php

use App\Enums\PaymentStatus;
use App\Models\AdminSetting;
use App\Models\Client;
use App\Models\ClientPackage;
use App\Models\ClientSetting;
use App\Models\Member;
use App\Models\Package;
use Illuminate\Support\Facades\Artisan;

test('payments:generate command creates monthly due payments for members', function () {
    $client = Client::factory()->create();
    $setting = ClientSetting::where('client_id', $client->id)->first();
    $setting->update([
        'share_price' => 1000,
    ]);

    $member = Member::factory()->create([
        'client_id' => $client->id,
        'share_quantity' => 3,
    ]);

    $exitCode = Artisan::call('payments:generate');

    expect($exitCode)->toBe(0);
    $this->assertDatabaseHas('payments', [
        'client_id' => $client->id,
        'member_id' => $member->id,
        'amount' => 3000,
        'status' => PaymentStatus::DUE->value,
    ]);
});

test('saas:generate-upcoming-invoices command generates invoice for soon-to-expire packages', function () {
    AdminSetting::factory()->create();
    $client = Client::factory()->create();
    $package = Package::factory()->create(['price' => 5000]);

    ClientPackage::factory()->create([
        'client_id' => $client->id,
        'package_id' => $package->id,
        'is_active' => true,
        'starts_at' => now()->subMonth(),
        'ends_at' => now()->addDays(3)->startOfDay(),
    ]);

    $exitCode = Artisan::call('saas:generate-upcoming-invoices');

    expect($exitCode)->toBe(0);
    $this->assertDatabaseHas('invoices', [
        'client_id' => $client->id,
        'package_id' => $package->id,
    ]);
});

test('invoices:generate command generates invoice for expired trials and active packages', function () {
    AdminSetting::factory()->create();
    $client = Client::factory()->create();
    $package = Package::factory()->create(['price' => 5000]);

    ClientPackage::factory()->create([
        'client_id' => $client->id,
        'package_id' => $package->id,
        'is_active' => true,
        'is_trial' => false,
        'starts_at' => now()->subMonth(),
        'ends_at' => now()->startOfDay(),
    ]);

    $exitCode = Artisan::call('invoices:generate');

    expect($exitCode)->toBe(0);
});
