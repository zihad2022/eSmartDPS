<?php

namespace App\Domain\Clients\Actions;

use App\Domain\Clients\Models\Client;
use App\Services\ImageService;

class DeleteClientAction
{
    public function __construct(
        private ImageService $imageService
    ) {}
    public function execute(Client $client): void
    {
        $this->imageService->deleteImage($client->profile_photo);
        $this->imageService->deleteImage($client->nid_card_front);
        $this->imageService->deleteImage($client->nid_card_back);
        $client->delete();
    }
}
