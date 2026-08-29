<?php

use App\Domain\Packages\Models\Package;
use App\Enums\Package\BillingCycle;
use App\Enums\Package\DiscountType;

test('admin can list packages', function () {
    $admin = createSuperAdmin();
    $packages = Package::factory()->count(3)->create();

    $response = $this->actingAs($admin, 'admin')
        ->get(route('admin.packages.index'));

    $response->assertOk()
        ->assertViewIs('admin.package.index')
        ->assertSee($packages->first()->name);
});

test('admin can render create package form', function () {
    $admin = createSuperAdmin();

    $response = $this->actingAs($admin, 'admin')
        ->get(route('admin.packages.create'));

    $response->assertOk()
        ->assertViewIs('admin.package.form');
});

test('admin can store a new package', function () {
    $admin = createSuperAdmin();

    $response = $this->actingAs($admin, 'admin')
        ->post(route('admin.packages.store'), [
            'name' => 'Gold Plan',
            'description' => 'Gold subscription plan',
            'price' => 1500,
            'discount_value' => 10,
            'discount_type' => DiscountType::PERCENT->value,
            'billing_cycle' => BillingCycle::MONTHLY->value,
            'member_limit' => 500,
            'user_limit' => 10,
            'project_limit' => 20,
            'has_trial' => 0,
            'trial_days' => 0,
            'is_active' => 1,
            'features' => ['members' => 1, 'projects' => 1],
        ]);

    $response->assertRedirect(route('admin.packages.index'));
    $this->assertDatabaseHas('packages', [
        'name' => 'Gold Plan',
        'price' => 1500,
    ]);
});

test('admin can update a package', function () {
    $admin = createSuperAdmin();
    $package = Package::factory()->create(['name' => 'Silver Plan', 'price' => 800]);

    $response = $this->actingAs($admin, 'admin')
        ->put(route('admin.packages.update', $package), [
            'name' => 'Silver Plan Updated',
            'price' => 900,
            'discount_value' => 0,
            'discount_type' => null,
            'billing_cycle' => BillingCycle::MONTHLY->value,
            'member_limit' => 200,
            'user_limit' => 5,
            'project_limit' => 10,
            'has_trial' => 0,
            'trial_days' => 0,
            'is_active' => 1,
        ]);

    $response->assertRedirect(route('admin.packages.index'));
    expect($package->refresh()->name)->toBe('Silver Plan Updated')
        ->and($package->price)->toBe(900);
});

test('admin can delete a package without subscriptions', function () {
    $admin = createSuperAdmin();
    $package = Package::factory()->create();

    $response = $this->actingAs($admin, 'admin')
        ->delete(route('admin.packages.destroy', $package));

    $response->assertRedirect(route('admin.packages.index'));
    $this->assertDatabaseMissing('packages', ['id' => $package->id]);
});
