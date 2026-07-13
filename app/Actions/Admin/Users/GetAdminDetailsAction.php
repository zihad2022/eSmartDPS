<?php

namespace App\Actions\Admin\Users;

use App\Models\Activity;
use App\Models\Admin;

class GetAdminDetailsAction
{
    public function __construct(
        private readonly GuardAdminAccountManagementAction $guardManagement,
    ) {}

    public function execute(Admin $actor, Admin $admin): array
    {
        $admin->load('roles.permissions');

        return [
            'user' => $admin,
            'canManageUser' => $this->guardManagement->allows($actor, $admin),
            'activities' => Activity::query()
                ->where('causer_type', Admin::class)
                ->where('causer_id', $admin->id)
                ->latest()
                ->limit(20)
                ->get(),
        ];
    }
}
