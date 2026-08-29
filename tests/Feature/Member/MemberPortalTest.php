<?php

use App\Models\Client;
use App\Models\ClientSetting;
use App\Models\Member;

test('unauthenticated user cannot access member dashboard', function () {
    $response = $this->get(route('member.dashboard'));

    $response->assertRedirect(route('home'));
});

test('authenticated member can view member dashboard with calculations', function () {
    $client = Client::factory()->create();
    $member = Member::factory()->create([
        'client_id' => $client->id,
        'share_quantity' => 5,
        'total_balance' => 50000,
    ]);

    $response = $this->actingAs($member, 'member')
        ->get(route('member.dashboard'));

    $response->assertOk()
        ->assertViewIs('member.dashboard')
        ->assertViewHas('totalShares', 5)
        ->assertViewHas('totalBalance', 50000);
});

test('authenticated member can view payment submission page', function () {
    $client = Client::factory()->create();
    $setting = ClientSetting::where('client_id', $client->id)->first();
    $setting->update([
        'payment_methods' => [1, 2],
    ]);

    $member = Member::factory()->create(['client_id' => $client->id]);

    $response = $this->actingAs($member, 'member')
        ->get(route('member.payment.create'));

    $response->assertOk()
        ->assertViewIs('member.payment');
});
