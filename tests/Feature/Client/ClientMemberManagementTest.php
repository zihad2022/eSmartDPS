<?php

use App\Models\Member;

test('client can list members', function () {
    $client = createActiveClient();
    $members = Member::factory()->count(3)->create(['client_id' => $client->id]);

    $response = $this->actingAs($client, 'client')
        ->get(route('client.members.index'));

    $response->assertOk()
        ->assertViewIs('client.member.index')
        ->assertSee($members->first()->name);
});

test('client can render create member form', function () {
    $client = createActiveClient();

    $response = $this->actingAs($client, 'client')
        ->get(route('client.members.create'));

    $response->assertOk()
        ->assertViewIs('client.member.form');
});

test('client can store a new member', function () {
    $client = createActiveClient();

    $response = $this->actingAs($client, 'client')
        ->post(route('client.members.store'), [
            'name' => 'Rashid Khan',
            'email' => 'rashid@example.com',
            'phone' => '01811223344',
            'password' => 'secret123',
            'share_quantity' => 2,
            'status' => 1,
        ]);

    $response->assertRedirect(route('client.members.index'));
    $this->assertDatabaseHas('members', [
        'client_id' => $client->id,
        'name' => 'Rashid Khan',
        'email' => 'rashid@example.com',
    ]);
});

test('client can view a single member', function () {
    $client = createActiveClient();
    $member = Member::factory()->create(['client_id' => $client->id]);

    $response = $this->actingAs($client, 'client')
        ->get(route('client.members.show', $member));

    $response->assertOk()
        ->assertViewIs('client.member.show')
        ->assertSee($member->name);
});

test('client can update a member', function () {
    $client = createActiveClient();
    $member = Member::factory()->create([
        'client_id' => $client->id,
        'name' => 'Old Name',
    ]);

    $response = $this->actingAs($client, 'client')
        ->put(route('client.members.update', $member), [
            'name' => 'New Name',
            'email' => $member->email,
            'phone' => $member->phone,
            'share_quantity' => $member->share_quantity,
            'status' => 1,
        ]);

    $response->assertRedirect(route('client.members.index'));
    expect($member->refresh()->name)->toBe('New Name');
});

test('client can delete a member', function () {
    $client = createActiveClient();
    $member = Member::factory()->create(['client_id' => $client->id]);

    $response = $this->actingAs($client, 'client')
        ->delete(route('client.members.destroy', $member));

    $response->assertRedirect(route('client.members.index'));
    $this->assertDatabaseMissing('members', ['id' => $member->id]);
});
