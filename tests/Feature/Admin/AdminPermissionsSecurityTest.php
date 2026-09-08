<?php

use App\Models\Admin;
use App\Models\AdminSetting;
use App\Models\Client;
use Database\Seeders\AdminRolePermissionSeeder;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    (new AdminRolePermissionSeeder)->run();
    AdminSetting::factory()->create();
});

test('admin with manager role cannot delete client and receives 403', function () {
    $manager = Admin::factory()->create();
    $manager->assignRole('manager');

    $client = Client::factory()->create();

    $response = $this->actingAs($manager, 'admin')
        ->delete(route('admin.clients.destroy', $client));

    $response->assertForbidden();
    $this->assertDatabaseHas('clients', ['id' => $client->id]);
});

test('admin with manager role cannot update settings and receives 403', function () {
    $manager = Admin::factory()->create();
    $manager->assignRole('manager');

    $response = $this->actingAs($manager, 'admin')
        ->put(route('admin.settings.general.update'), [
            'site_name' => 'Hacked Site',
        ]);

    $response->assertForbidden();
});

test('admin with admin role cannot delete roles and receives 403', function () {
    $admin = Admin::factory()->create();
    $admin->assignRole('admin');

    $testRole = Role::create(['name' => 'custom-role', 'guard_name' => 'admin']);

    $response = $this->actingAs($admin, 'admin')
        ->delete(route('admin.roles.destroy', $testRole));

    $response->assertForbidden();
});

test('inactive admin user is blocked from accessing admin dashboard', function () {
    $inactiveAdmin = Admin::factory()->inactive()->create();
    $inactiveAdmin->assignRole('super-admin');

    $response = $this->actingAs($inactiveAdmin, 'admin')
        ->get(route('admin.dashboard'));

    $response->assertRedirect(route('admin.login'));
});
