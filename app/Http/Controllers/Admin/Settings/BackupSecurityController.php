<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Actions\Admin\Backups\ListDatabaseBackupsAction;
use App\Actions\Admin\Settings\GetAdminSettingsAction;
use App\Actions\Admin\Settings\UpdateBackupSecuritySettingsAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Settings\BackupSecuritySettingsRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BackupSecurityController extends Controller
{
    public function edit(
        GetAdminSettingsAction $getSettings,
        ListDatabaseBackupsAction $listBackups,
    ): View {
        return view('admin.settings.backup-security', [
            'settings' => $getSettings->execute(),
            'backups' => $listBackups->execute(),
        ]);
    }

    public function update(
        BackupSecuritySettingsRequest $request,
        GetAdminSettingsAction $getSettings,
        UpdateBackupSecuritySettingsAction $updateSettings,
    ): RedirectResponse {
        $updateSettings->execute($getSettings->execute(), $request->validated());

        return back()->with('success', 'Backup and security settings updated successfully.');
    }
}
