<?php

namespace App\Services;

use App\Mail\ClientWelcomeMail;
use App\Models\AdminSetting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

class MailService
{
    /**
     * Send a welcome email to a newly created client.
     *
     * @param  mixed  $client   The client object containing email and details
     * @param  string $password The generated password for the client
     *
     * Steps performed:
     *  1. Fetch mail settings from the database.
     *  2. Dynamically configure Laravel mail settings at runtime.
     *  3. Send the ClientWelcomeMail email to the client.
     */
    public function sendMail($client, $password): void
    {
        // 1. Fetch mail settings from the database
        $settings = AdminSetting::first();

        // 2. Dynamically configure Laravel mail settings
        Config::set('mail.mailers.smtp.host', $settings->mail_host);
        Config::set('mail.mailers.smtp.port', $settings->mail_port);
        Config::set('mail.mailers.smtp.username', $settings->mail_username);
        Config::set('mail.mailers.smtp.password', $settings->mail_password);
        Config::set('mail.mailers.smtp.encryption', $settings->mail_encryption);
        Config::set('mail.from.address', $settings->mail_from_address);
        Config::set('mail.from.name', $settings->mail_from_name);

        // 3. Send the welcome email with the provided client credentials
        Mail::to($client->email)->send(
            new ClientWelcomeMail($client, $password)
        );
    }
}
