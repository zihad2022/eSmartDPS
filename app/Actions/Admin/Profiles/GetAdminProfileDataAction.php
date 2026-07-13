<?php

namespace App\Actions\Admin\Profiles;

use App\Models\Admin;

class GetAdminProfileDataAction
{
    /**
     * @return array{user: Admin}
     */
    public function execute(Admin $admin): array
    {
        return ['user' => $admin->loadMissing('roles')];
    }
}
