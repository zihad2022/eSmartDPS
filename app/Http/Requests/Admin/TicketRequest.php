<?php

namespace App\Http\Requests\Admin;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use Illuminate\Foundation\Http\FormRequest;

class TicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $ticketId = $this->route('ticket'); // Get ticket ID from route for updates

        return [
            'client_id' => ['required', 'exists:clients,id'],
            'ticket_number' => ['required', 'unique:tickets,ticket_number,'.$ticketId],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'status' => ['required', 'in:'.implode(',', array_column(TicketStatus::cases(), 'value'))],
            'priority' => ['required', 'in:'.implode(',', array_column(TicketPriority::cases(), 'value'))],
            'admin_notes' => ['nullable', 'string'],
        ];
    }
}
