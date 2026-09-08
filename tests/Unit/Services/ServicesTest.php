<?php

use App\Mail\ClientWelcomeMail;
use App\Models\AdminSetting;
use App\Models\Client;
use App\Models\Member;
use App\Models\Package;
use App\Services\MailService;
use App\Services\PackageService;
use App\Services\SmsService;
use Illuminate\Support\Facades\Mail;

test('PackageService validatePackageSwitch detects resource limit violations', function () {
    $smallPackage = Package::factory()->create([
        'member_limit' => 2,
        'user_limit' => 5,
        'project_limit' => 5,
    ]);

    $largePackage = Package::factory()->create([
        'member_limit' => 10,
        'user_limit' => 10,
        'project_limit' => 10,
    ]);

    $client = createActiveClient([], $largePackage);
    Member::factory()->count(5)->create(['client_id' => $client->id]);

    $service = app(PackageService::class);

    $error = $service->validatePackageSwitch($client, $smallPackage);
    expect($error)->toContain('Cannot switch: the selected package allows fewer members');

    $validError = $service->validatePackageSwitch($client, $largePackage);
    expect($validError)->toBeNull();
});

test('PackageService starts trial package when package has trial', function () {
    $trialPackage = Package::factory()->create([
        'has_trial' => true,
        'trial_days' => 14,
    ]);

    $client = Client::factory()->create();
    $service = app(PackageService::class);

    $result = $service->startPackage($client, $trialPackage);
    expect($result)->toBeTrue();
    expect($client->clientPackages)->toHaveCount(1);
    expect($client->clientPackages->first()->is_trial)->toBeTrue();

    $secondAttempt = $service->startPackage($client, $trialPackage);
    expect($secondAttempt)->toBe('You have already used the trial for this package.');
});

test('MailService sends ClientWelcomeMail successfully', function () {
    Mail::fake();
    AdminSetting::factory()->create();

    $client = Client::factory()->create();
    $mailService = app(MailService::class);

    $mailService->sendMail($client, 'TemporaryPassword123!');

    Mail::assertSent(ClientWelcomeMail::class, function ($mail) use ($client) {
        return $mail->hasTo($client->email);
    });
});

test('SmsService returns false when SMS gateway is not configured', function () {
    AdminSetting::factory()->create([
        'sms_status' => false,
    ]);

    $smsService = new SmsService;
    $sent = $smsService->sendOtp('1234', '01700000000', 'Test Client');

    expect($sent)->toBeFalse();
});
