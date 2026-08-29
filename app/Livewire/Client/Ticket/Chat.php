<?php

namespace App\Livewire\Client\Ticket;

use App\Models\Ticket;
use App\Services\ImageService;
use Livewire\Component;
use Livewire\WithFileUploads;

class Chat extends Component
{
    use WithFileUploads;

    public Ticket $ticket; // Current ticket being viewed

    public string $message = ''; // Message input

    public int $messageKey = 0; // Key to reset input component

    public $attachment; // Optional file attachment

    /**
     * Mount the component with a ticket.
     */
    public function mount(Ticket $ticket): void
    {
        // -----------------------------
        // 1. Load ticket with client and replies
        // -----------------------------
        $this->ticket = Ticket::with([
            'replies' => fn ($q) => $q->oldest(), // Oldest first
            'client',
        ])->findOrFail($ticket->id);

        $this->refreshMessageKey(); // Ensure Livewire input resets properly
    }

    /**
     * Send a message to the ticket.
     */
    public function sendMessage(): void
    {
        // -----------------------------
        // 1. Validate message and attachment
        // -----------------------------
        $this->validate([
            'message' => 'required|string|max:2000',
            'attachment' => 'nullable|file|max:5120', // Max 5MB
        ]);

        // -----------------------------
        // 2. Upload attachment if exists
        // -----------------------------
        $path = $this->attachment
            ? app(ImageService::class)->uploadImage($this->attachment, 'uploads/tickets')
            : null;

        // -----------------------------
        // 3. Save reply to database
        // -----------------------------
        $this->ticket->replies()->create([
            'client_id' => owner_client_id(),
            'message' => $this->message,
            'attachment' => $path,
        ]);

        // -----------------------------
        // 4. Reload ticket replies
        // -----------------------------
        $this->ticket->load(['replies' => fn ($q) => $q->oldest()]);

        // -----------------------------
        // 5. Reset input fields
        // -----------------------------
        $this->reset(['message', 'attachment']);
        $this->refreshMessageKey();

        // -----------------------------
        // 6. Notify user of success
        // -----------------------------
        $this->dispatch('notify', 'Message sent successfully.');
    }

    /**
     * Increment message key to force input reset in Livewire.
     */
    private function refreshMessageKey(): void
    {
        $this->messageKey++;
    }

    /**
     * Render the Livewire component view.
     */
    public function render()
    {
        return view('livewire.client.ticket.chat');
    }
}
