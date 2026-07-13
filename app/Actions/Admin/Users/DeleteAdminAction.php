<?php

namespace App\Actions\Admin\Users;

use App\Models\Admin;
use App\Services\ImageService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DeleteAdminAction
{
    public function __construct(
        private readonly ImageService $imageService,
        private readonly GuardAdminAccountManagementAction $guardManagement,
    ) {}

    public function execute(Admin $actor, Admin $admin): void
    {
        $this->guardManagement->execute($actor, $admin);

        if ($actor->is($admin)) {
            throw ValidationException::withMessages([
                'user' => ['You cannot delete your own account.'],
            ]);
        }

        if ($admin->hasRole('super-admin')) {
            throw ValidationException::withMessages([
                'user' => ['Super Admin accounts cannot be deleted.'],
            ]);
        }

        $photo = $admin->profile_photo;
        DB::transaction(fn () => $admin->delete());
        $this->imageService->deleteImage($photo);
    }
}
