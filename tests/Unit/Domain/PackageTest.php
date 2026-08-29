<?php

use App\Domain\Packages\Models\Package;
use App\Enums\Package\BillingCycle;
use App\Enums\Package\DiscountType;
use Carbon\Carbon;

test('calculates fixed discount amount correctly', function () {
    $package = Package::factory()->make([
        'price' => 1000,
        'discount_type' => DiscountType::FIXED,
        'discount_value' => 150,
    ]);

    expect($package->discount_amount)->toBe(150)
        ->and($package->final_price)->toBe(850)
        ->and($package->total_after_discount)->toBe(850);
});

test('calculates percent discount amount correctly', function () {
    $package = Package::factory()->make([
        'price' => 2000,
        'discount_type' => DiscountType::PERCENT,
        'discount_value' => 20,
    ]);

    expect($package->discount_amount)->toBe(400)
        ->and($package->final_price)->toBe(1600);
});

test('fixed discount cannot exceed total price', function () {
    $package = Package::factory()->make([
        'price' => 500,
        'discount_type' => DiscountType::FIXED,
        'discount_value' => 800,
    ]);

    expect($package->discount_amount)->toBe(500)
        ->and($package->final_price)->toBe(0);
});

test('percent discount capped at 100 percent', function () {
    $package = Package::factory()->make([
        'price' => 500,
        'discount_type' => DiscountType::PERCENT,
        'discount_value' => 120,
    ]);

    expect($package->discount_amount)->toBe(500)
        ->and($package->final_price)->toBe(0);
});

test('calculates monthly and yearly billing end dates correctly', function () {
    $monthlyPackage = Package::factory()->make(['billing_cycle' => BillingCycle::MONTHLY]);
    $yearlyPackage = Package::factory()->make(['billing_cycle' => BillingCycle::YEARLY]);

    $start = Carbon::parse('2026-01-15 00:00:00');

    expect($monthlyPackage->billingEndDate($start)->toDateString())->toBe('2026-02-15')
        ->and($yearlyPackage->billingEndDate($start)->toDateString())->toBe('2027-01-15');
});

test('checks feature flag availability accurately', function () {
    $package = Package::factory()->make([
        'features' => [
            'members' => true,
            'sms' => false,
        ],
    ]);

    expect($package->hasFeature('members'))->toBeTrue()
        ->and($package->hasFeature('sms'))->toBeFalse()
        ->and($package->hasFeature('non_existent'))->toBeFalse();
});

test('active and inactive scopes filter packages correctly', function () {
    Package::factory()->create(['is_active' => true]);
    Package::factory()->create(['is_active' => false]);

    expect(Package::active()->count())->toBe(1)
        ->and(Package::inactive()->count())->toBe(1);
});
