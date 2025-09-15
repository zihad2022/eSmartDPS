<?php

namespace App\Http\Requests\Admin;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Allow all requests for now
    }

    public function rules(): array
    {
        $ticketId = $this->route('ticket'); // Get current ticket ID for unique check

        return [
            'client_id'     => ['required', 'exists:clients,id'], // Must belong to a valid client
            'ticket_number' => ['required', Rule::unique('tickets', 'ticket_number')->ignore($ticketId)], // Unique ticket number
            'subject'       => ['required', 'string', 'max:255'], // Ticket title
            'message'       => ['required', 'string'], // Ticket description
            'status'        => ['required', Rule::in(array_column(TicketStatus::cases(), 'value'))], // Must match allowed status
            'priority'      => ['required', Rule::in(array_column(TicketPriority::cases(), 'value'))], // Must match allowed priority
            'admin_notes'   => ['nullable', 'string'], // Optional admin notes
        ];
    }
}
