<?php

use App\Domain\Clients\Models\Client;
use App\Domain\Clients\Models\ClientPackage;
use App\Domain\Packages\Models\Package;
use App\Services\SubscriptionService;

test('detects active subscription correctly', function () {
    $client = Client::factory()->create();
    $package = Package::factory()->create();

    ClientPackage::factory()->create([
        'client_id' => $client->id,
        'package_id' => $package->id,
        'starts_at' => now()->subDay(),
        'ends_at' => now()->addDays(20),
        'is_active' => true,
        'status' => ClientPackage::STATUS_ACTIVE,
    ]);

    $service = new SubscriptionService($client);

    expect($service->isActive())->toBeTrue()
        ->and($service->getRemainingDays())->toBeGreaterThanOrEqual(19);
});

test('handles expired or missing subscription', function () {
    $client = Client::factory()->create();
    $service = new SubscriptionService($client);

    expect($service->isActive())->toBeFalse()
        ->and($service->canAccessFeature('members'))->toBeFalse()
        ->and($service->isLimitReached('member_limit', 1))->toBeTrue()
        ->and($service->getRemainingDays())->toBe(0);
});

test('checks feature access and limits properly', function () {
    $client = Client::factory()->create();
    $package = Package::factory()->create([
        'member_limit' => 10,
        'features' => [
            'reports' => true,
            'sms' => false,
        ],
    ]);

    ClientPackage::factory()->create([
        'client_id' => $client->id,
        'package_id' => $package->id,
        'starts_at' => now()->subDay(),
        'ends_at' => now()->addDays(15),
        'is_active' => true,
        'status' => ClientPackage::STATUS_ACTIVE,
    ]);

    $service = new SubscriptionService($client);

    expect($service->canAccessFeature('reports'))->toBeTrue()
        ->and($service->canAccessFeature('sms'))->toBeFalse()
        ->and($service->isLimitReached('member_limit', 5))->toBeFalse()
        ->and($service->isLimitReached('member_limit', 10))->toBeTrue();
});
