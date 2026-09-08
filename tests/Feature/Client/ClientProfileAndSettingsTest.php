<?php

use App\Models\ClientSetting;
use Illuminate\Support\Facades\Hash;

test('client can view own profile page', function () {
    $client = createActiveClient(['password' => Hash::make('MyPassword123!')]);

    $response = $this->actingAs($client, 'client')
        ->get(route('client.profile.edit'));

    $response->assertOk()
        ->assertViewIs('client.user.profile')
        ->assertSee($client->first_name)
        ->assertSee($client->email);
});

test('client can update profile and change password', function () {
    $client = createActiveClient(['password' => Hash::make('CurrentPassword123!')]);

    $response = $this->actingAs($client, 'client')
        ->put(route('client.profile.update'), [
            'first_name' => 'UpdatedFirst',
            'last_name' => 'UpdatedLast',
            'phone' => '01799887766',
            'current_password' => 'CurrentPassword123!',
            'new_password' => 'BrandNewPassword999!',
        ]);

    $response->assertRedirect(route('client.profile.edit'))
        ->assertSessionHas('success');

    $client->refresh();
    expect($client->first_name)->toBe('UpdatedFirst')
        ->and($client->phone)->toBe('01799887766')
        ->and(Hash::check('BrandNewPassword999!', $client->password))->toBeTrue();
});

test('client password update fails when current password is wrong', function () {
    $client = createActiveClient(['password' => Hash::make('CurrentPassword123!')]);

    $response = $this->actingAs($client, 'client')
        ->put(route('client.profile.update'), [
            'first_name' => 'UpdatedFirst',
            'last_name' => 'UpdatedLast',
            'phone' => '01799887766',
            'current_password' => 'WrongCurrentPassword!',
            'new_password' => 'BrandNewPassword999!',
        ]);

    $response->assertSessionHasErrors('current_password');
});

test('client can update backup security settings', function () {
    $client = createActiveClient();

    $response = $this->actingAs($client, 'client')
        ->put(route('client.settings.backup-security.update'), [
            'auto_backup' => true,
            'two_factor_auth' => true,
            'session_timeout' => false,
            'login_notifications' => true,
        ]);

    $response->assertRedirect(route('client.settings.backup-security.edit'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('client_settings', [
        'client_id' => $client->id,
        'auto_backup' => true,
        'two_factor_auth' => true,
    ]);
});

test('client can update notification settings', function () {
    $client = createActiveClient();

    $response = $this->actingAs($client, 'client')
        ->put(route('client.settings.notification.update'), [
            'sms_api_provider' => 'BulkSMS',
            'sms_api_key' => 'secret_key_123',
            'email_payment_confirmations' => true,
            'email_payment_reminders' => false,
            'email_payment_reports' => true,
            'sms_payment_confirmations' => true,
            'sms_payment_reminders' => false,
        ]);

    $response->assertRedirect(route('client.settings.notification.edit'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('client_settings', [
        'client_id' => $client->id,
        'sms_api_provider' => 'BulkSMS',
        'sms_api_key' => 'secret_key_123',
    ]);
});
