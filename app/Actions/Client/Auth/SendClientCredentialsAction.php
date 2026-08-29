<?php

namespace App\Actions\Client\Auth;

use App\Models\Client;
use App\Services\MailService;

class SendClientCredentialsAction
{
    protected MailService $mailService;

    public function __construct(MailService $mailService)
    {
        $this->mailService = $mailService;
    }

    public function execute(Client $client, string $password): void
    {
        $this->mailService->sendMail($client, $password);
    }
}
