<?php

use App\Domain\Packages\Models\Package;
use App\Enums\Package\BillingCycle;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('home page renders successfully', function () {
    $response = $this->get('/');
    $response->assertStatus(200);
});

test('pricing page renders successfully', function () {
    $response = $this->get('/pricing');
    $response->assertStatus(200);
});

test('about page renders successfully', function () {
    $response = $this->get('/about');
    $response->assertStatus(200);
});

test('admin login page renders successfully', function () {
    $response = $this->get('/admin/login');
    $response->assertStatus(200);
});

test('client login page renders successfully', function () {
    $response = $this->get('/client/login');
    $response->assertStatus(200);
});

test('client register page renders successfully', function () {
    Package::create([
        'name' => 'Starter',
        'price' => 100,
        'billing_cycle' => BillingCycle::MONTHLY,
    ]);
    $response = $this->get('/client/register');
    $response->assertStatus(200);
});

test('member login page renders successfully', function () {
    $response = $this->get('/member/login');
    $response->assertStatus(200);
});
