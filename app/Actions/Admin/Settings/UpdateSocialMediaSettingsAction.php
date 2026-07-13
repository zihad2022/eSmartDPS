<?php

namespace App\Actions\Admin\Settings;

use App\Models\AdminSetting;
use App\Services\ActivityLogger;

class UpdateSocialMediaSettingsAction
{
    public function execute(AdminSetting $settings, array $data): AdminSetting
    {
        $settings->update($data);
        ActivityLogger::log('Social media settings updated.');

        return $settings->refresh();
    }
}
