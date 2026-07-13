<?php

namespace App\Actions\Admin\Settings;

use App\Models\AdminSetting;
use App\Services\ActivityLogger;

class UpdateSmsSettingsAction
{
    public function execute(AdminSetting $settings, array $data): AdminSetting
    {
        if (blank($data['sms_api_key'] ?? null)) {
            unset($data['sms_api_key']);
        }

        $settings->update($data);
        ActivityLogger::log('SMS settings updated.');

        return $settings->refresh();
    }
}
