<?php

use App\Domain\Clients\Models\Client;
use App\Models\Member;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\HttpException;

test('owner_client_id returns authenticated parent or user id', function () {
    $parent = Client::factory()->create();
    $subUser = Client::factory()->subUser($parent)->create();

    expect(owner_client_id())->toBeNull();

    Auth::guard('client')->login($parent);
    expect(owner_client_id())->toBe($parent->id);

    Auth::guard('client')->login($subUser);
    expect(owner_client_id())->toBe($parent->id);

    Auth::guard('client')->logout();
});

test('generate_sequential_id produces sequential formatted codes', function () {
    $uid1 = generate_client_user_id();
    expect($uid1)->toBe('UID01');

    $inv1 = generate_invoice_number();
    expect($inv1)->toBe('INV001');

    $pay1 = generate_payment_id();
    expect($pay1)->toBe('PAY001');

    $mid1 = generate_member_id();
    expect($mid1)->toBe('MID01');

    $tkt1 = generate_ticket_number();
    expect($tkt1)->toBe('TKT001');
});

test('authorize_owner passes when owner matches and aborts 403 when not', function () {
    $client = Client::factory()->create();
    Auth::guard('client')->login($client);

    $member = Member::factory()->create(['client_id' => $client->id]);
    expect(fn () => authorize_owner($member))->not->toThrow(Exception::class);

    $otherMember = Member::factory()->create();
    expect(fn () => authorize_owner($otherMember))->toThrow(HttpException::class);
});
