<?php

use App\Models\Activity;
use App\Models\Admin;
use App\Models\Client;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\Auth;

test('logs activity for authenticated admin', function () {
    $admin = Admin::factory()->create();
    Auth::guard('admin')->login($admin);

    ActivityLogger::log('Admin logged in');

    expect(Activity::count())->toBe(1);

    $activity = Activity::first();
    expect($activity->causer_id)->toBe($admin->id)
        ->and($activity->causer_type)->toBe(Admin::class)
        ->and($activity->activity)->toBe('Admin logged in');

    Auth::guard('admin')->logout();
});

test('logs activity for authenticated client', function () {
    $client = Client::factory()->create();
    Auth::guard('client')->login($client);

    ActivityLogger::log('Client updated profile');

    $activity = Activity::where('causer_id', $client->id)->where('causer_type', Client::class)->first();
    expect($activity)->not->toBeNull()
        ->and($activity->activity)->toBe('Client updated profile');

    Auth::guard('client')->logout();
});

test('does not log when no user is authenticated', function () {
    $initialCount = Activity::count();
    ActivityLogger::log('Guest action');

    expect(Activity::count())->toBe($initialCount);
});
