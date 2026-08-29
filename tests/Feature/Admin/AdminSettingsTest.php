<?php

use App\Models\AdminSetting;

test('admin can view and update general settings', function () {
    $admin = createSuperAdmin();
    AdminSetting::factory()->create();

    $response = $this->actingAs($admin, 'admin')
        ->get(route('admin.settings.general.edit'));

    $response->assertOk()
        ->assertViewIs('admin.settings.general');

    $updateResponse = $this->actingAs($admin, 'admin')
        ->put(route('admin.settings.general.update'), [
            'site_name' => 'eSmartDPS Pro',
            'site_slogan' => 'Best DPS System',
        ]);

    $updateResponse->assertRedirect(route('admin.settings.general.edit'));
    $this->assertDatabaseHas('admin_settings', [
        'site_name' => 'eSmartDPS Pro',
        'site_slogan' => 'Best DPS System',
    ]);
});

test('admin can view and update payment settings', function () {
    $admin = createSuperAdmin();
    AdminSetting::factory()->create();

    $response = $this->actingAs($admin, 'admin')
        ->get(route('admin.settings.payments.edit'));

    $response->assertOk()
        ->assertViewIs('admin.settings.payment');

    $updateGeneralPayment = $this->actingAs($admin, 'admin')
        ->put(route('admin.settings.payments.update'), [
            'section' => 'general',
            'currency' => 'USD',
            'late_fee' => 150,
        ]);

    $updateGeneralPayment->assertRedirect(route('admin.settings.payments.edit'));
    $this->assertDatabaseHas('admin_settings', [
        'currency' => 'USD',
        'late_fee' => 150,
    ]);
});

test('admin can view contact info settings', function () {
    $admin = createSuperAdmin();
    AdminSetting::factory()->create();

    $response = $this->actingAs($admin, 'admin')
        ->get(route('admin.settings.contact-info.edit'));

    $response->assertOk()
        ->assertViewIs('admin.settings.contact-info');
});
