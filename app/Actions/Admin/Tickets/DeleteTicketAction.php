<?php

namespace App\Actions\Admin\Tickets;

use App\Models\Ticket;
use App\Services\ImageService;
use Illuminate\Support\Facades\DB;

class DeleteTicketAction
{
    public function __construct(private readonly ImageService $imageService) {}

    public function execute(Ticket $ticket): void
    {
        $ticket->loadMissing('replies');
        $attachments = $ticket->replies->pluck('attachment')->filter()->all();

        DB::transaction(fn () => $ticket->delete());

        foreach ($attachments as $attachment) {
            $this->imageService->deleteImage($attachment);
        }
    }
}
