<?php

namespace App\Mail;

use App\Models\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClientWelcomeMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public Client $client,
        public string $password,
        public string $siteName,
        public string $messageTemplate,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->siteName.' - Client Welcome Mail');
    }

    public function content(): Content
    {
        $body = str_replace(
            ['{first_name}', '{last_name}', '{user_id}', '{password}'],
            [$this->client->first_name, $this->client->last_name, $this->client->user_id, $this->password],
            $this->messageTemplate,
        );

        return new Content(
            view: 'admin.emails.client-email',
            with: [
                'client' => $this->client,
                'body' => $body,
                'siteName' => $this->siteName,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
