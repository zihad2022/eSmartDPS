<?php

namespace App\Actions\Admin\Profiles;

use App\Models\Admin;
use App\Services\ImageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class UpdateAdminProfileAction
{
    public function __construct(private readonly ImageService $imageService) {}

    public function execute(Admin $admin, array $data): Admin
    {
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
            $updated = DB::transaction(function () use ($admin, $data): Admin {
                $admin->update($data);

                return $admin->refresh();
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
}
