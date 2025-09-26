<?php

namespace App\Listeners;

use App\Events\Admin\ClientCreated;
use App\Services\MailService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendClientWelcomeMail
{
    public function __construct(private MailService $mailService) {}

    public function handle(ClientCreated $event): void
    {
        $this->mailService->sendMail($event->client, $event->password);
    }
}
