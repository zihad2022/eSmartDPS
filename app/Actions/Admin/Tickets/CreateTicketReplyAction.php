<?php

namespace App\Actions\Admin\Tickets;

use App\Models\Admin;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Services\ImageService;
use Illuminate\Http\UploadedFile;

class CreateTicketReplyAction
{
    public function __construct(private readonly ImageService $imageService) {}

    public function execute(Ticket $ticket, Admin $admin, ?string $message, ?UploadedFile $attachment = null): TicketReply
    {
        $path = $attachment
            ? $this->imageService->uploadImage($attachment, "uploads/tickets/{$ticket->id}")
            : null;

        try {
            return $ticket->replies()->create([
                'admin_id' => $admin->id,
                'message' => filled($message) ? trim($message) : null,
                'attachment' => $path,
            ]);
        } catch (\Throwable $exception) {
            $this->imageService->deleteImage($path);
            throw $exception;
        }
    }
}
