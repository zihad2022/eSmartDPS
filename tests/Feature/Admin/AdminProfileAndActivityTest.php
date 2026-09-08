<?php

use App\Models\Activity;
use App\Models\Admin;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

test('admin can view profile edit page', function () {
    $admin = createSuperAdmin();

    $response = $this->actingAs($admin, 'admin')
        ->get(route('admin.profile.edit'));

    $response->assertOk()
        ->assertViewIs('admin.user.profile')
        ->assertSee($admin->name)
        ->assertSee($admin->email);
});

test('admin can update profile details and password', function () {
    Storage::fake('public');
    $admin = createSuperAdmin();

    $photo = UploadedFile::fake()->image('avatar.jpg');

    $response = $this->actingAs($admin, 'admin')
        ->put(route('admin.profile.update'), [
            'name' => 'Updated Admin Name',
            'phone' => '01899887766',
            'profile_photo' => $photo,
            'password' => 'NewSecretPass123!',
        ]);

    $response->assertRedirect(route('admin.profile.edit'))
        ->assertSessionHas('success');

    $admin->refresh();
    expect($admin->name)->toBe('Updated Admin Name')
        ->and($admin->phone)->toBe('01899887766')
        ->and(Hash::check('NewSecretPass123!', $admin->password))->toBeTrue()
        ->and($admin->profile_photo)->not->toBeNull();
});

test('admin profile update validates duplicate phone number', function () {
    $admin1 = createSuperAdmin();
    $admin2 = Admin::factory()->create(['phone' => '01711223344']);

    $response = $this->actingAs($admin1, 'admin')
        ->put(route('admin.profile.update'), [
            'name' => 'Name',
            'phone' => '01711223344',
        ]);

    $response->assertSessionHasErrors('phone');
});

test('admin can view activities log list', function () {
    $admin = createSuperAdmin();
    Activity::create([
        'causer_id' => $admin->id,
        'causer_type' => Admin::class,
        'activity' => 'Admin logged in for testing',
        'ip_address' => '127.0.0.1',
        'activity_date' => now(),
    ]);

    $response = $this->actingAs($admin, 'admin')
        ->get(route('admin.users.activities'));

    $response->assertOk()
        ->assertViewIs('admin.user.activities')
        ->assertSee('Admin logged in for testing');
});
