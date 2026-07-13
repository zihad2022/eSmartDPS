<?php

namespace App\Actions\Admin\Users;

use App\Models\Admin;
use App\Services\ImageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class CreateAdminAction
{
    public function __construct(private readonly ImageService $imageService) {}

    public function execute(Admin $actor, array $data): Admin
    {
        $role = $data['role'];
        unset($data['role']);

        $this->guardRoleAssignment($actor, $role);

        $uploadedPath = null;
        if (($data['profile_photo'] ?? null) instanceof UploadedFile) {
            $uploadedPath = $this->imageService->uploadImage(
                $data['profile_photo'],
                'uploads/admins/users',
            );
            $data['profile_photo'] = $uploadedPath;
        }

        try {
            return DB::transaction(function () use ($data, $role): Admin {
                $admin = Admin::query()->create($data);
                $admin->syncRoles([$role]);

                return $admin->load('roles');
            });
        } catch (\Throwable $exception) {
            $this->imageService->deleteImage($uploadedPath);
            throw $exception;
        }
    }

    private function guardRoleAssignment(Admin $actor, string $roleName): void
    {
        $role = Role::query()
            ->where('guard_name', 'admin')
            ->where('name', $roleName)
            ->with('permissions:id')
            ->firstOrFail();

        if ($actor->hasRole('super-admin')) {
            return;
        }

        if ($role->name === 'super-admin' || $role->permissions->pluck('id')->diff($actor->getAllPermissions()->pluck('id'))->isNotEmpty()) {
            throw ValidationException::withMessages([
                'role' => ['You cannot assign a role that grants permissions beyond your own account.'],
            ]);
        }
    }
}
