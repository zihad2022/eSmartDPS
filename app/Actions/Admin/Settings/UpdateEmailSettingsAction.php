<?php

namespace App\Actions\Admin\Settings;

use App\Models\AdminSetting;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\Cache;

class UpdateEmailSettingsAction
{
    public function execute(AdminSetting $settings, array $data): AdminSetting
    {
        if (blank($data['mail_password'] ?? null)) {
            unset($data['mail_password']);
        }

        $settings->update($data);
        Cache::forget('admin_settings_first');
        ActivityLogger::log('Email settings updated.');

        return $settings->refresh();
    }
}
