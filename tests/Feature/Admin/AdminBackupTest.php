<?php

use App\Models\AdminSetting;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('local');
    AdminSetting::factory()->create();
});

test('admin can trigger manual database backup creation', function () {
    $admin = createSuperAdmin();

    $response = $this->actingAs($admin, 'admin')
        ->post(route('admin.settings.backups.store'));

    $response->assertRedirect()
        ->assertSessionHas('success');

    $files = Storage::disk('local')->files('private/admin-backups');
    expect($files)->not->toBeEmpty();
    expect($files[0])->toEndWith('.jsonl.gz');
});

test('admin can download existing backup file', function () {
    $admin = createSuperAdmin();
    $filename = 'database-2026-09-08_12-00-00-abcdef.jsonl.gz';
    $path = 'private/admin-backups/'.$filename;

    Storage::disk('local')->put($path, gzencode(json_encode(['type' => 'metadata'])));

    $response = $this->actingAs($admin, 'admin')
        ->get(route('admin.settings.backups.download', $filename));

    $response->assertOk()
        ->assertDownload($filename);
});

test('admin can delete an existing backup file', function () {
    $admin = createSuperAdmin();
    $filename = 'database-2026-09-08_12-00-00-abcdef.jsonl.gz';
    $path = 'private/admin-backups/'.$filename;

    Storage::disk('local')->put($path, 'dummy backup data');

    $response = $this->actingAs($admin, 'admin')
        ->delete(route('admin.settings.backups.destroy', $filename));

    $response->assertRedirect()
        ->assertSessionHas('success');

    expect(Storage::disk('local')->exists($path))->toBeFalse();
});

test('guest cannot access admin backup endpoints', function () {
    $response = $this->post(route('admin.settings.backups.store'));
    $response->assertRedirect(route('admin.login'));
});
