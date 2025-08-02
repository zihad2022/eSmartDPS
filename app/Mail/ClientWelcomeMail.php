<?php

namespace App\Mail;

use App\Models\AdminSetting;
use App\Models\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

// class ClientWelcomeMail extends Mailable
// {
//     use Queueable, SerializesModels;

//     public $client;

//     public function __construct(Client $client)
//     {
//         $this->client = $client;
//     }

//     public function envelope(): Envelope
//     {
//         return new Envelope(
//             subject: AdminSetting::first()->site_name.' - Client Welcome Mail',
//         );
//     }

//     public function content(): Content
//     {
//         $email_message_template = AdminSetting::first()->email_message_template;
//         $body = str_replace([
//             '{first_name}' => $this->client->first_name,
//             '{last_name}' => $this->client->last_name,
//             '{user_id}' => $this->client->user_id,
//             '{password}' => $this->client->password,
//         ], [
//             $this->client->first_name,
//             $this->client->last_name,
//             $this->client->user_id,
//             $this->client->password,
//         ], $email_message_template);

//         return new Content(
//             view: 'admin.emails.client-email',
//             with: [
//                 'client' => $this->client,
//                 'body' => $body,
//             ],
//         );
//     }

//     public function attachments(): array
//     {
//         return [];
//     }
// }

class ClientWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $client;

    public $password;

    public function __construct(Client $client, $password)
    {
        $this->client = $client;
        $this->password = $password; // ✅ store actual password (not hashed one)
    }

    public function envelope(): Envelope
    {
        $siteName = AdminSetting::first()->site_name ?? config('app.name');

        return new Envelope(
            subject: $siteName.' - Client Welcome Mail',
        );
    }

    public function content(): Content
    {
        $settings = AdminSetting::first();
        $emailTemplate = $settings->email_message_template ?? 'Welcome {first_name} {last_name}, your User ID is {user_id} and password is {password}.';

        // ✅ Correct str_replace
        $body = str_replace(
            ['{first_name}', '{last_name}', '{user_id}', '{password}'],
            [$this->client->first_name, $this->client->last_name, $this->client->user_id, $this->password],
            $emailTemplate
        );

        return new Content(
            view: 'admin.emails.client-email',
            with: [
                'client' => $this->client,
                'body' => $body,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
