<?php

namespace App\Livewire\Admin\Ticket;

use App\Models\Ticket;
use App\Services\ImageService;
use Livewire\Component;
use Livewire\WithFileUploads;

class Chat extends Component
{
    use WithFileUploads;

    public Ticket $ticket;

    public string $message = '';

    public int $messageKey = 0;

    public $attachment; // File upload (no type declaration)

    public function mount(Ticket $ticket): void
    {
        $this->ticket = Ticket::with([
            'replies' => fn ($q) => $q->orderBy('created_at', 'asc'),
            'client',
        ])->findOrFail($ticket->id);

        $this->messageKey++;
    }

    public function sendMessage(): void
    {
        $this->validate([
            'message' => 'required|string|max:2000',
            'attachment' => 'nullable|file|max:5120', // 5MB max
        ]);

        // Handle file upload if exists
        $attachmentPath = null;
        if ($this->attachment) {
            $attachmentPath = app(ImageService::class)->uploadImage($this->attachment, 'uploads/tickets');
        }

        // Create the reply
        $this->ticket->replies()->create([
            'admin_id' => auth('admin')->id(),
            'message' => $this->message,
            'attachment' => $attachmentPath,
        ]);

        // Reload only replies
        $this->ticket->load(['replies' => fn ($q) => $q->orderBy('created_at', 'asc')]);

        // Clear inputs
        $this->message = '';
        $this->attachment = null;
        $this->messageKey++;

        $this->dispatch('notify', 'Message sent successfully.');
    }

    public function render()
    {
        return view('livewire.admin.ticket.chat');
    }
}
