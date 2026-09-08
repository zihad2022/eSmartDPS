<?php

use Maatwebsite\Excel\Facades\Excel;

beforeEach(function () {
    Excel::fake();
});

test('admin can export clients to excel', function () {
    $admin = createSuperAdmin();

    $response = $this->actingAs($admin, 'admin')
        ->get(route('admin.clients.export'));

    $response->assertOk();
    Excel::assertDownloaded('clients.xlsx');
});

test('admin can export packages to excel', function () {
    $admin = createSuperAdmin();

    $response = $this->actingAs($admin, 'admin')
        ->get(route('admin.packages.export'));

    $response->assertOk();
    Excel::assertDownloaded('packages.xlsx');
});

test('admin can export invoices to excel', function () {
    $admin = createSuperAdmin();

    $response = $this->actingAs($admin, 'admin')
        ->get(route('admin.invoices.export'));

    $response->assertOk();
    Excel::assertDownloaded('invoices.xlsx');
});

test('admin can export tickets to excel', function () {
    $admin = createSuperAdmin();

    $response = $this->actingAs($admin, 'admin')
        ->get(route('admin.tickets.export'));

    $response->assertOk();
    Excel::assertDownloaded('tickets.xlsx');
});

test('admin can export users to excel', function () {
    $admin = createSuperAdmin();

    $response = $this->actingAs($admin, 'admin')
        ->get(route('admin.users.export'));

    $response->assertOk();
    Excel::assertDownloaded('users.xlsx');
});

test('guest cannot access admin export endpoints', function () {
    $response = $this->get(route('admin.clients.export'));
    $response->assertRedirect(route('admin.login'));
});
