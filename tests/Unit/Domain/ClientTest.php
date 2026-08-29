<?php

use App\Domain\Clients\Models\Client;
use App\Domain\Clients\Models\ClientPackage;
use App\Domain\Packages\Models\Package;

test('scopes filter parents and children correctly', function () {
    $parent = Client::factory()->create(['status' => true]);
    $subUser = Client::factory()->subUser($parent)->create(['status' => true]);
    $inactiveParent = Client::factory()->inactive()->create();

    expect(Client::query()->parents()->count())->toBe(2)
        ->and(Client::query()->children()->count())->toBe(1)
        ->and(Client::activeParents()->count())->toBe(1)
        ->and(Client::inactiveParents()->count())->toBe(1);
});

test('parent-child client relationship works', function () {
    $parent = Client::factory()->create();
    $subUser = Client::factory()->subUser($parent)->create();

    expect($subUser->parent->id)->toBe($parent->id)
        ->and($parent->children->contains($subUser))->toBeTrue();
});

test('full name attribute combines first and last name', function () {
    $client = Client::factory()->make([
        'first_name' => 'Jane',
        'last_name' => 'Doe',
    ]);

    expect($client->full_name)->toBe('Jane Doe');
});

test('account user count includes parent and children', function () {
    $parent = Client::factory()->create();
    Client::factory()->subUser($parent)->create();
    Client::factory()->subUser($parent)->create();

    expect($parent->accountUserCount())->toBe(3);
});

test('capacity checking checks package limits', function () {
    $package = Package::factory()->create([
        'member_limit' => 2,
        'user_limit' => 2,
        'project_limit' => 2,
    ]);

    $client = Client::factory()->create();
    ClientPackage::factory()->create([
        'client_id' => $client->id,
        'package_id' => $package->id,
        'is_active' => true,
        'status' => ClientPackage::STATUS_ACTIVE,
        'starts_at' => now()->subDay(),
        'ends_at' => now()->addMonth(),
    ]);

    expect($client->canAddUser())->toBeTrue()
        ->and($client->canAddMember())->toBeTrue()
        ->and($client->canAddProject())->toBeTrue();
});
