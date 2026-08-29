<?php

use App\Models\Admin;
use App\Models\Client;
use App\Models\ClientPackage;
use App\Models\Package;
use Database\Seeders\AdminRolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature', 'Unit');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

function createSuperAdmin(): Admin
{
    (new AdminRolePermissionSeeder)->run();
    $admin = Admin::factory()->create();
    $admin->assignRole('super-admin');

    return $admin;
}

function createActiveClient(array $attributes = [], ?Package $package = null): Client
{
    $client = Client::factory()->create($attributes);
    $package ??= Package::factory()->create();

    ClientPackage::factory()->create([
        'client_id' => $client->id,
        'package_id' => $package->id,
        'starts_at' => now()->subDay(),
        'ends_at' => now()->addMonth(),
        'is_active' => true,
        'status' => ClientPackage::STATUS_ACTIVE,
    ]);

    return $client;
}
