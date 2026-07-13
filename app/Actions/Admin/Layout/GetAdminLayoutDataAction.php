<?php

namespace App\Actions\Admin\Layout;

use App\Actions\Admin\Activities\GetRecentAdminActivitiesAction;
use App\Actions\Admin\Settings\GetAdminSettingsAction;
use App\Models\Admin;
use App\Models\AdminSetting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class GetAdminLayoutDataAction
{
    public function __construct(
        private readonly GetAdminSettingsAction $getAdminSettings,
        private readonly GetRecentAdminActivitiesAction $getRecentActivities,
    ) {
    }

    /**
     * @return array{user: Admin, settings: AdminSetting, recentActivities: Collection}
     */
    public function execute(): array
    {
        /** @var Admin|null $user */
        $user = Auth::guard('admin')->user();

        abort_unless($user instanceof Admin, 401);

        $user->loadMissing('roles.permissions');

        return [
            'user' => $user,
            'settings' => $this->getAdminSettings->execute(),
            'recentActivities' => $user->can('view user activities')
                ? $this->getRecentActivities->execute()
                : collect(),
        ];
    }
}
