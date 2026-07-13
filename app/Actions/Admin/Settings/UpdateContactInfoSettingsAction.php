<?php

namespace App\Actions\Admin\Settings;

use App\Models\AdminSetting;
use App\Services\ActivityLogger;

class UpdateContactInfoSettingsAction
{
    public function execute(AdminSetting $settings, array $data): AdminSetting
    {
        $settings->update($data);
        ActivityLogger::log('Contact information settings updated.');

        return $settings->refresh();
    }
}
