<?php

namespace App\Actions\Admin\Settings;

use App\Models\AdminSetting;

class GetAdminSettingsAction
{
    public function execute(): AdminSetting
    {
        return AdminSetting::query()->firstOrCreate([]);
    }
}
