<?php

use App\Models\Member;

test('member login screen can be rendered', function () {
    $response = $this->get(route('member.login'));

    $response->assertOk()
        ->assertViewIs('member.auth.login');
});

test('member can authenticate using valid member_id and password', function () {
    $member = Member::factory()->create([
        'member_id' => 'MID99',
        'password' => 'secret123',
    ]);

    $response = $this->post(route('member.authenticate'), [
        'member_id' => 'MID99',
        'password' => 'secret123',
    ]);

    $this->assertAuthenticatedAs($member, 'member');
    $response->assertRedirect(route('member.dashboard'));
});

test('member cannot authenticate with invalid credentials', function () {
    $member = Member::factory()->create([
        'member_id' => 'MID99',
        'password' => 'secret123',
    ]);

    $response = $this->post(route('member.authenticate'), [
        'member_id' => 'MID99',
        'password' => 'wrongpassword',
    ]);

    $this->assertGuest('member');
    $response->assertSessionHas('error');
});

test('member can log out', function () {
    $member = Member::factory()->create([
        'member_id' => 'MID99',
        'password' => 'secret123',
    ]);

    $this->post(route('member.authenticate'), [
        'member_id' => 'MID99',
        'password' => 'secret123',
    ]);

    $this->assertAuthenticatedAs($member, 'member');

    $response = $this->post(route('member.logout'));

    $this->assertGuest('member');
    $response->assertRedirect(route('home'));
});
