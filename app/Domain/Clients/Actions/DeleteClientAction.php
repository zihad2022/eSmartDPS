<?php

namespace App\Domain\Clients\Actions;

use App\Domain\Clients\Models\Client;
use App\Services\ImageService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DeleteClientAction
{
    public function __construct(private readonly ImageService $imageService) {}

    public function execute(Client $client): void
    {
        $blockingRelations = [
            'children' => 'child users',
            'members' => 'members',
            'projects' => 'projects',
            'payments' => 'payments',
            'ledgers' => 'ledger entries',
            'invoices' => 'invoices',
            'clientPackages' => 'subscription history',
            'tickets' => 'support tickets',
        ];

        foreach ($blockingRelations as $relation => $label) {
            if ($client->{$relation}()->exists()) {
                throw ValidationException::withMessages([
                    'client' => ["This client has {$label}. Deactivate the account instead of deleting financial or operational history."],
                ]);
            }
        }

        $files = [$client->profile_photo, $client->nid_card_front, $client->nid_card_back];

        DB::transaction(fn () => $client->delete());

        foreach ($files as $path) {
            $this->imageService->deleteImage($path);
        }
    }
}
