<?php

use App\Models\Admin;

test('admin can view roles list', function () {
    $admin = createSuperAdmin();

    $response = $this->actingAs($admin, 'admin')
        ->get(route('admin.roles.index'));

    $response->assertOk()
        ->assertViewIs('admin.role.index');
});

test('admin can create a new role', function () {
    $admin = createSuperAdmin();

    $response = $this->actingAs($admin, 'admin')
        ->post(route('admin.roles.store'), [
            'name' => 'editor',
            'permissions' => [],
        ]);

    $response->assertRedirect(route('admin.roles.index'));
    $this->assertDatabaseHas('roles', [
        'name' => 'editor',
        'guard_name' => 'admin',
    ]);
});

test('admin can view admin users list', function () {
    $admin = createSuperAdmin();
    Admin::factory()->count(2)->create();

    $response = $this->actingAs($admin, 'admin')
        ->get(route('admin.users.index'));

    $response->assertOk()
        ->assertViewIs('admin.user.index');
});

test('admin can create a new admin user with role', function () {
    $admin = createSuperAdmin();

    $response = $this->actingAs($admin, 'admin')
        ->post(route('admin.users.store'), [
            'name' => 'Moderator User',
            'username' => 'moderator1',
            'email' => 'mod@test.com',
            'phone' => '01799887766',
            'password' => 'Password123!',
            'role' => 'admin',
            'status' => 1,
        ]);

    $response->assertRedirect(route('admin.users.index'));
    $this->assertDatabaseHas('admins', [
        'username' => 'moderator1',
        'email' => 'mod@test.com',
    ]);
});
