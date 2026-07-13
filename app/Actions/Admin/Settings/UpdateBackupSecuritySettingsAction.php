<?php

namespace App\Actions\Admin\Settings;

use App\Models\AdminSetting;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\Cache;

class UpdateBackupSecuritySettingsAction
{
    public function execute(AdminSetting $settings, array $data): AdminSetting
    {
        $settings->update($data);
        Cache::forget('admin.session_timeout_minutes');
        ActivityLogger::log('Backup and security settings updated.');

        return $settings->refresh();
    }
}
