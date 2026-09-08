<?php

use App\Models\Client;
use App\Models\OtpCode;
use Illuminate\Support\Facades\Hash;

test('client can view forgot password phone form', function () {
    $response = $this->get(route('client.password.forgot'));

    $response->assertOk()
        ->assertViewIs('client.auth.forgot-password-phone');
});

test('client can request OTP with valid registered phone number', function () {
    $client = Client::factory()->create(['phone' => '01712345678']);

    $response = $this->post(route('client.password.forgot.store'), [
        'phone' => '01712345678',
    ]);

    $response->assertRedirect(route('client.otp.verify', ['phone' => '01712345678']))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('otp_codes', [
        'userable_id' => $client->id,
        'userable_type' => Client::class,
        'phone' => '01712345678',
        'is_used' => false,
    ]);
});

test('requesting OTP fails for unregistered phone number', function () {
    $response = $this->post(route('client.password.forgot.store'), [
        'phone' => '01999999999',
    ]);

    $response->assertSessionHas('error', 'Client not found');
});

test('requesting OTP redirects if an active unused OTP already exists', function () {
    $client = Client::factory()->create(['phone' => '01712345678']);
    OtpCode::create([
        'userable_id' => $client->id,
        'userable_type' => Client::class,
        'phone' => '01712345678',
        'otp' => 1234,
        'expires_at' => now()->addMinutes(4),
        'is_used' => false,
    ]);

    $response = $this->post(route('client.password.forgot.store'), [
        'phone' => '01712345678',
    ]);

    $response->assertRedirect(route('client.otp.verify', ['phone' => '01712345678']))
        ->assertSessionHas('error');
});

test('client can view OTP verification page', function () {
    $response = $this->get(route('client.otp.verify', ['phone' => '01712345678']));

    $response->assertOk()
        ->assertViewIs('client.auth.otp-verify');
});

test('client can verify valid OTP and gets redirected to password reset page', function () {
    $client = Client::factory()->create(['phone' => '01712345678']);
    $otpCode = OtpCode::create([
        'userable_id' => $client->id,
        'userable_type' => Client::class,
        'phone' => '01712345678',
        'otp' => 7890,
        'expires_at' => now()->addMinutes(4),
        'is_used' => false,
    ]);

    $response = $this->post(route('client.otp.verify.post', ['phone' => '01712345678']), [
        'phone' => '01712345678',
        'otp' => 7890,
    ]);

    $response->assertRedirect(route('client.password.reset', ['phone' => '01712345678']))
        ->assertSessionHas('success');

    $this->assertDatabaseMissing('otp_codes', ['id' => $otpCode->id]);
});

test('OTP verification fails with incorrect OTP code', function () {
    $client = Client::factory()->create(['phone' => '01712345678']);
    OtpCode::create([
        'userable_id' => $client->id,
        'userable_type' => Client::class,
        'phone' => '01712345678',
        'otp' => 7890,
        'expires_at' => now()->addMinutes(4),
        'is_used' => false,
    ]);

    $response = $this->from(route('client.otp.verify', ['phone' => '01712345678']))
        ->post(route('client.otp.verify.post', ['phone' => '01712345678']), [
            'phone' => '01712345678',
            'otp' => 1111,
        ]);

    $response->assertRedirect(route('client.otp.verify', ['phone' => '01712345678']))
        ->assertSessionHas('error', 'Invalid OTP.');
});

test('client can view reset password page with phone number', function () {
    $response = $this->get(route('client.password.reset', ['phone' => '01712345678']));

    $response->assertOk()
        ->assertViewIs('client.auth.reset-password-phone');
});

test('client can successfully reset password and log in with new password', function () {
    $client = Client::factory()->create([
        'phone' => '01712345678',
        'password' => Hash::make('OldPassword123!'),
    ]);

    $response = $this->post(route('client.password.reset.store'), [
        'phone' => '01712345678',
        'password' => 'BrandNewPassword123!',
        'password_confirmation' => 'BrandNewPassword123!',
    ]);

    $response->assertRedirect(route('client.login'))
        ->assertSessionHas('success');

    $client->refresh();
    expect(Hash::check('BrandNewPassword123!', $client->password))->toBeTrue();
});
