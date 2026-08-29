<?php

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Package;

test('unauthenticated user is redirected from admin dashboard', function () {
    $response = $this->get(route('admin.dashboard'));

    $response->assertRedirect(route('admin.login'));
});

test('super admin can access dashboard with metric stats', function () {
    $admin = createSuperAdmin();
    Client::factory()->count(3)->create();
    Package::factory()->count(2)->create();
    Invoice::factory()->paid()->count(2)->create(['invoice_amount' => 500]);

    $response = $this->actingAs($admin, 'admin')
        ->get(route('admin.dashboard'));

    $response->assertOk()
        ->assertViewIs('admin.dashboard');
});
