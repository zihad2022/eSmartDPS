<?php

namespace App\Actions\Admin\Clients;

use App\Domain\Clients\Actions\HandleClientPackageAction;
use App\Domain\Clients\Actions\UpdateClientAction;
use App\Domain\Clients\DTOs\ClientData;
use App\Domain\Clients\Models\Client;
use App\Services\ImageService;
use Illuminate\Support\Facades\DB;

class UpdateClientWithPackageAction
{
    public function __construct(
        private readonly UpdateClientAction $updateClient,
        private readonly HandleClientPackageAction $handlePackage,
        private readonly ImageService $images,
    ) {}

    public function execute(Client $client, ClientData $data): Client
    {
        $originalFiles = [
            'profile_photo' => $client->profile_photo,
            'nid_card_front' => $client->nid_card_front,
            'nid_card_back' => $client->nid_card_back,
        ];

        try {
            $updated = DB::transaction(function () use ($client, $data): Client {
                $this->updateClient->execute($client, $data);
                $this->handlePackage->execute($client, $data->package_id);

                return $client->refresh();
            });
        } catch (\Throwable $exception) {
            $this->deleteNewFiles($data, $originalFiles);
            throw $exception;
        }

        $this->deleteReplacedFiles($updated, $originalFiles);

        return $updated;
    }

    private function deleteNewFiles(ClientData $data, array $originalFiles): void
    {
        $newFiles = [
            'profile_photo' => $data->profile_photo,
            'nid_card_front' => $data->nid_card_front,
            'nid_card_back' => $data->nid_card_back,
        ];

        foreach ($newFiles as $field => $path) {
            if ($path && $path !== $originalFiles[$field]) {
                $this->images->deleteImage($path);
            }
        }
    }

    private function deleteReplacedFiles(Client $updated, array $originalFiles): void
    {
        foreach ($originalFiles as $field => $oldPath) {
            if ($oldPath && $oldPath !== $updated->{$field}) {
                $this->images->deleteImage($oldPath);
            }
        }
    }
}
