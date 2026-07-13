<?php

namespace App\Http\Requests\Admin;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Ticket;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    public function rules(): array
    {
        /** @var Ticket|null $ticket */
        $ticket = $this->route('ticket');

        return [
            'client_id' => [
                'required', 'integer',
                Rule::exists('clients', 'id')->whereNull('parent_id'),
            ],
            'ticket_number' => [
                'required', 'string', 'max:50',
                Rule::unique('tickets', 'ticket_number')->ignore($ticket?->id),
            ],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:10000'],
            'status' => ['required', Rule::enum(TicketStatus::class)],
            'priority' => ['required', Rule::enum(TicketPriority::class)],
            'admin_notes' => ['nullable', 'string', 'max:10000'],
        ];
    }
}
