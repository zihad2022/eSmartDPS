<?php

namespace App\Services;

use App\Actions\Admin\Settings\ConfigureAdminMailAction;
use App\Actions\Admin\Settings\GetAdminSettingsAction;
use App\Domain\Clients\Models\Client;
use App\Mail\ClientWelcomeMail;
use Illuminate\Support\Facades\Mail;

class MailService
{
    public function __construct(
        private readonly ConfigureAdminMailAction $configureMail,
        private readonly GetAdminSettingsAction $getSettings,
    ) {
    }

    public function sendMail(Client $client, string $password): void
    {
        $settings = $this->getSettings->execute();
        $configured = $this->configureMail->execute($settings);
        $mailer = $configured ? Mail::mailer('smtp') : Mail::mailer();
        $siteName = $settings->site_name ?: config('app.name');
        $template = $settings->email_message_template
            ?: 'Welcome {first_name} {last_name}, your User ID is {user_id} and password is {password}.';

        $mailer->to($client->email)->send(
            new ClientWelcomeMail($client, $password, $siteName, $template),
        );
    }
}
