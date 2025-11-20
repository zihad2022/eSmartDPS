<?php

namespace App\Domain\Clients\Actions;

use App\Domain\Clients\DTOs\ClientData;
use App\Domain\Clients\Models\Client;
use App\Http\Requests\Admin\ClientRequest;
use App\Services\ImageService;
use Illuminate\Http\UploadedFile;

class PrepareClientDataAction
{
    public function __construct(private ImageService $imageService) {}

    public function execute(ClientRequest $request, ?Client $client = null): ClientData
    {
        $v = $request->validated();

        // Resolve user_id first (important for folder paths)
        $userId = $client->user_id ?? generate_client_user_id();

        return new ClientData(
            user_id: $userId,
            first_name: $v['first_name'],
            last_name: $v['last_name'],
            email: $v['email'],
            phone: $v['phone'] ?? null,
            division: $v['division'] ?? null,
            district: $v['district'] ?? null,
            address: $v['address'] ?? null,
            postal_code: $v['postal_code'] ?? null,
            nid_number: $v['nid_number'] ?? null,
            nid_card_front: $this->handleFile(
                $v,
                'nid_card_front',
                $client?->nid_card_front,
                "uploads/clients/$userId/nid-card-front"
            ),
            nid_card_back: $this->handleFile(
                $v,
                'nid_card_back',
                $client?->nid_card_back,
                "uploads/clients/$userId/nid-card-back"
            ),
            profile_photo: $this->handleFile(
                $v,
                'profile_photo',
                $client?->profile_photo,
                "uploads/clients/$userId/profile-photo"
            ),
            password: $v['password'] ?? null,
            package_id: $v['package_id'] ?? null,
        );
    }

    // when data edit than check file is uploaded or not if upload so delete current file and upload new file
    private function handleFile(array $validated, string $key, ?string $existingPath, string $folder): ?string
    {
        // No file uploaded → return existing file or null for create
        if (!array_key_exists($key, $validated) || !($validated[$key] instanceof UploadedFile)) {
            return $existingPath;
        }

        // Delete existing file if it exists
        $this->imageService->deleteImage($existingPath);

        // Upload new file
        return $this->imageService->uploadImage($validated[$key], $folder);
    }
}
