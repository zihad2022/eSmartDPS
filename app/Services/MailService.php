<?php

namespace App\Services;

use App\Mail\ClientWelcomeMail;
use App\Models\AdminSetting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

class MailService
{
    public function sendMail($client)
    {
        // Get settings from DB
        $settings = AdminSetting::first();

        // Dynamically set mail configuration
        Config::set('mail.mailers.smtp.host', $settings->mail_host);
        Config::set('mail.mailers.smtp.port', $settings->mail_port);
        Config::set('mail.mailers.smtp.username', $settings->mail_username);
        Config::set('mail.mailers.smtp.password', $settings->mail_password);
        Config::set('mail.mailers.smtp.encryption', $settings->mail_encryption);
        Config::set('mail.from.address', $settings->mail_from_address);
        Config::set('mail.from.name', $settings->mail_from_name);

        // Send Email
        Mail::to($client->email)->send(new ClientWelcomeMail($client));
    }
}
