<?php

namespace App\Actions\Admin\Users;

use App\Models\Admin;
use App\Services\ImageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class UpdateAdminAction
{
    public function __construct(
        private readonly ImageService $imageService,
        private readonly GuardAdminAccountManagementAction $guardManagement,
    ) {}

    public function execute(Admin $actor, Admin $admin, array $data): Admin
    {
        $role = $data['role'];
        unset($data['role']);

        $this->guardManagement->execute($actor, $admin);

        $this->guardRoleAssignment($actor, $role);

        if ($actor->is($admin) && ! $admin->hasRole($role)) {
            throw ValidationException::withMessages([
                'role' => ['You cannot change your own administrator role.'],
            ]);
        }

        if ($actor->is($admin) && ! (bool) $data['status']) {
            throw ValidationException::withMessages([
                'status' => ['You cannot deactivate your own account.'],
            ]);
        }

        if ($admin->hasRole('super-admin') && $role !== 'super-admin') {
            $superAdminCount = Admin::query()->role('super-admin')->count();
            if ($superAdminCount <= 1) {
                throw ValidationException::withMessages([
                    'role' => ['The system must keep at least one Super Admin.'],
                ]);
            }
        }

        $oldPhoto = $admin->profile_photo;
        $newPhoto = null;
        if (($data['profile_photo'] ?? null) instanceof UploadedFile) {
            $newPhoto = $this->imageService->uploadImage(
                $data['profile_photo'],
                'uploads/admins/users',
            );
            $data['profile_photo'] = $newPhoto;
        } else {
            unset($data['profile_photo']);
        }

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        try {
            $updated = DB::transaction(function () use ($admin, $data, $role): Admin {
                $admin->update($data);
                $admin->syncRoles([$role]);

                return $admin->refresh()->load('roles');
            });
        } catch (\Throwable $exception) {
            $this->imageService->deleteImage($newPhoto);
            throw $exception;
        }

        if ($newPhoto && $oldPhoto !== $newPhoto) {
            $this->imageService->deleteImage($oldPhoto);
        }

        return $updated;
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
