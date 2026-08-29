<?php

namespace App\Listeners;

use App\Events\Admin\ClientCreated;
use App\Services\MailService;

class SendClientWelcomeMail
{
    public function __construct(private MailService $mailService) {}

    public function handle(ClientCreated $event): void
    {
        $this->mailService->sendMail($event->client, $event->password);
    }
}
