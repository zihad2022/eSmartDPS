<?php

namespace App\Livewire\Admin\Ticket;

use App\Actions\Admin\Tickets\CreateTicketReplyAction;
use App\Models\Ticket;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithFileUploads;

class Chat extends Component
{
    use WithFileUploads;

    public Ticket $ticket;

    public string $message = '';

    public int $messageKey = 0;

    public $attachment = null;

    public function mount(Ticket $ticket): void
    {
        $this->authorizeAccess();
        $this->ticket = $ticket->load([
            'replies' => fn ($query) => $query->oldest(),
            'replies.admin',
            'replies.client',
            'client',
        ]);
        $this->refreshMessageKey();
    }

    public function sendMessage(CreateTicketReplyAction $action): void
    {
        $this->authorizeAccess();
        abort_unless(auth('admin')->user()->can('send ticket messages'), 403);

        $this->validate([
            'message' => ['nullable', 'string', 'max:2000'],
            'attachment' => ['nullable', 'file', 'max:5120', 'mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,txt,zip'],
        ]);

        if (blank($this->message) && ! $this->attachment) {
            throw ValidationException::withMessages([
                'message' => ['Enter a message or attach a file.'],
            ]);
        }

        $action->execute(
            ticket: $this->ticket,
            admin: auth('admin')->user(),
            message: $this->message,
            attachment: $this->attachment,
        );

        $this->ticket->load([
            'replies' => fn ($query) => $query->oldest(),
            'replies.admin',
            'replies.client',
        ]);
        $this->reset(['message', 'attachment']);
        $this->refreshMessageKey();
        $this->dispatch('notify', 'Message sent successfully.');
    }

    public function render()
    {
        $this->authorizeAccess();

        return view('livewire.admin.ticket.chat');
    }

    private function authorizeAccess(): void
    {
        $admin = auth('admin')->user();
        abort_unless($admin && $admin->can('view ticket chats'), 403);
    }

    private function refreshMessageKey(): void
    {
        $this->messageKey++;
    }
}
