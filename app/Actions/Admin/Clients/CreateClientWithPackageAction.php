<?php

namespace App\Actions\Admin\Clients;

use App\Models\Client;
use App\Services\ImageService;
use App\Services\MailService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateClientWithPackageAction
{
    public function __construct(
        private readonly CreateClientAction $createClient,
        private readonly HandleClientPackageAction $handlePackage,
        private readonly ImageService $images,
        private readonly MailService $mail,
    ) {}

    public function execute(array $data): ClientCreationResult
    {
        try {
            $client = DB::transaction(function () use ($data): Client {
                $client = $this->createClient->execute($data);
                $this->handlePackage->execute($client, $data['package_id'] ?? null);

                return $client->refresh();
            });
        } catch (\Throwable $exception) {
            $this->deleteUploadedFiles($data);
            throw $exception;
        }

        $mailSent = false;
        if (! empty($data['password'])) {
            try {
                $this->mail->sendMail($client, $data['password']);
                $mailSent = true;
            } catch (\Throwable $exception) {
                Log::warning('Client created, but credentials email delivery failed.', [
                    'client_id' => $client->id,
                    'email' => $client->email,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        return new ClientCreationResult($client, $mailSent);
    }

    private function deleteUploadedFiles(array $data): void
    {
        foreach ([$data['profile_photo'] ?? null, $data['nid_card_front'] ?? null, $data['nid_card_back'] ?? null] as $path) {
            if ($path) {
                $this->images->deleteImage($path);
            }
        }
    }
}
