<?php

namespace App\Domain\Clients\Actions;

use App\Domain\Clients\DTOs\ClientData;
use App\Domain\Clients\Models\Client;
use App\Http\Requests\Admin\ClientRequest;
use App\Services\ImageService;
use Illuminate\Http\UploadedFile;

class PrepareClientDataAction
{
    public function __construct(private readonly ImageService $imageService) {}

    public function execute(ClientRequest $request, ?Client $client = null): ClientData
    {
        $validated = $request->validated();
        $userId = $client?->user_id ?? generate_client_user_id();
        $uploadedPaths = [];

        try {
            $nidFront = $this->storeReplacement(
                $validated['nid_card_front'] ?? null,
                $client?->nid_card_front,
                "uploads/clients/{$userId}/nid-card-front",
                $uploadedPaths,
            );
            $nidBack = $this->storeReplacement(
                $validated['nid_card_back'] ?? null,
                $client?->nid_card_back,
                "uploads/clients/{$userId}/nid-card-back",
                $uploadedPaths,
            );
            $profilePhoto = $this->storeReplacement(
                $validated['profile_photo'] ?? null,
                $client?->profile_photo,
                "uploads/clients/{$userId}/profile-photo",
                $uploadedPaths,
            );
        } catch (\Throwable $exception) {
            foreach ($uploadedPaths as $path) {
                $this->imageService->deleteImage($path);
            }

            throw $exception;
        }

        return new ClientData(
            user_id: $userId,
            first_name: $validated['first_name'],
            last_name: $validated['last_name'],
            email: $validated['email'],
            phone: $validated['phone'] ?? null,
            division: $validated['division'] ?? null,
            district: $validated['district'] ?? null,
            address: $validated['address'] ?? null,
            postal_code: $validated['postal_code'] ?? null,
            nid_number: $validated['nid_number'] ?? null,
            nid_card_front: $nidFront,
            nid_card_back: $nidBack,
            profile_photo: $profilePhoto,
            password: $validated['password'] ?? null,
            package_id: isset($validated['package_id']) ? (int) $validated['package_id'] : null,
            status: (bool) ($validated['status'] ?? $client?->status ?? true),
        );
    }

    private function storeReplacement(
        mixed $file,
        ?string $existingPath,
        string $folder,
        array &$uploadedPaths,
    ): ?string {
        if (! $file instanceof UploadedFile) {
            return $existingPath;
        }

        $path = $this->imageService->uploadImage($file, $folder);
        $uploadedPaths[] = $path;

        return $path;
    }
}
