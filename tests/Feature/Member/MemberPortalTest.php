<?php

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Client;
use App\Models\ClientSetting;
use App\Models\Member;
use App\Models\Payment;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

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

test('authenticated member can submit payment receipt successfully', function () {
    Storage::fake('public');

    $client = Client::factory()->create();
    $setting = ClientSetting::where('client_id', $client->id)->first();
    $setting->update([
        'payment_methods' => [PaymentMethod::BANK_TRANSFER->value],
    ]);

    $member = Member::factory()->create(['client_id' => $client->id]);

    $receiptFile = UploadedFile::fake()->create('receipt.pdf', 500, 'application/pdf');

    $response = $this->actingAs($member, 'member')
        ->post(route('member.payment.store'), [
            'payment_amount' => 500,
            'payment_date' => '2026-08-29',
            'payment_method' => PaymentMethod::BANK_TRANSFER->value,
            'reference_number' => 'REF123456',
            'payment_notes' => 'Monthly deposit',
            'receipt_file' => $receiptFile,
        ]);

    $response->assertSessionHas('success', 'Payment submitted successfully!');

    $payment = Payment::where('member_id', $member->id)->first();
    expect($payment)->not->toBeNull();
    expect($payment->amount)->toBe(500);
    expect($payment->status)->toBe(PaymentStatus::PENDING);
    expect($payment->reference_number)->toBe('REF123456');
    Storage::disk('public')->assertExists($payment->meta['receipt_path']);
});

test('member payment submission fails validation when receipt is missing or amount is invalid', function () {
    $client = Client::factory()->create();
    $member = Member::factory()->create(['client_id' => $client->id]);

    $response = $this->actingAs($member, 'member')
        ->post(route('member.payment.store'), [
            'payment_amount' => 0,
            'payment_date' => 'not-a-date',
            'payment_method' => 999,
        ]);

    $response->assertSessionHasErrors(['payment_amount', 'payment_date', 'payment_method', 'receipt_file']);
});

test('member cannot view payment page if client has no payment methods configured', function () {
    $client = Client::factory()->create();
    $setting = ClientSetting::where('client_id', $client->id)->first();
    $setting->update(['payment_methods' => []]);

    $member = Member::factory()->create(['client_id' => $client->id]);

    $response = $this->actingAs($member, 'member')
        ->from(route('member.dashboard'))
        ->get(route('member.payment.create'));

    $response->assertRedirect(route('member.dashboard'))
        ->assertSessionHasErrors('general');
});

test('member can log out successfully', function () {
    $client = Client::factory()->create();
    $member = Member::factory()->create(['client_id' => $client->id]);

    $response = $this->actingAs($member, 'member')
        ->post(route('member.logout'));

    $response->assertRedirect(route('home'));
    $this->assertGuest('member');
});

