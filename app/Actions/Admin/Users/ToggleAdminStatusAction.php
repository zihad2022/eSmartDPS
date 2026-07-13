<?php

namespace App\Actions\Admin\Users;

use App\Models\Admin;
use Illuminate\Validation\ValidationException;

class ToggleAdminStatusAction
{
    public function __construct(
        private readonly GuardAdminAccountManagementAction $guardManagement,
    ) {}
    public function execute(Admin $actor, Admin $admin): Admin
    {
        $this->guardManagement->execute($actor, $admin);

        if ($actor->is($admin)) {
            throw ValidationException::withMessages([
                'status' => ['You cannot change your own account status.'],
            ]);
        }

        if ($admin->hasRole('super-admin')) {
            throw ValidationException::withMessages([
                'status' => ['A Super Admin account cannot be disabled from this control.'],
            ]);
        }

        $admin->update(['status' => ! $admin->status]);

        return $admin->refresh();
    }
}
