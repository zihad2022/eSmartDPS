<?php

use App\Models\Admin;

test('admin login screen can be rendered', function () {
    $response = $this->get(route('admin.login'));

    $response->assertOk()
        ->assertViewIs('admin.auth.login');
});

test('admin can authenticate using valid credentials', function () {
    $admin = Admin::factory()->create([
        'email' => 'admin@test.com',
        'password' => 'secret123',
        'status' => true,
    ]);

    $response = $this->post(route('admin.authenticate'), [
        'email' => 'admin@test.com',
        'password' => 'secret123',
    ]);

    $this->assertAuthenticatedAs($admin, 'admin');
    $response->assertRedirect(route('admin.dashboard'));
});

test('admin cannot authenticate with invalid password', function () {
    $admin = Admin::factory()->create([
        'email' => 'admin@test.com',
        'password' => 'secret123',
        'status' => true,
    ]);

    $response = $this->post(route('admin.authenticate'), [
        'email' => 'admin@test.com',
        'password' => 'wrongpassword',
    ]);

    $this->assertGuest('admin');
    $response->assertSessionHasErrors('email');
});

test('inactive admin cannot authenticate', function () {
    $admin = Admin::factory()->inactive()->create([
        'email' => 'inactive@test.com',
        'password' => 'secret123',
    ]);

    $response = $this->post(route('admin.authenticate'), [
        'email' => 'inactive@test.com',
        'password' => 'secret123',
    ]);

    $this->assertGuest('admin');
    $response->assertSessionHasErrors('email');
});

test('admin can log out', function () {
    $admin = Admin::factory()->create([
        'email' => 'admin@test.com',
        'password' => 'secret123',
    ]);

    $this->post(route('admin.authenticate'), [
        'email' => 'admin@test.com',
        'password' => 'secret123',
    ]);

    $this->assertAuthenticatedAs($admin, 'admin');

    $response = $this->post(route('admin.logout'));

    $this->assertGuest('admin');
    $response->assertRedirect(route('admin.login'));
});
