{{-- resources/views/admin/ticket/partials/row.blade.php --}}
@php($ticket = $row)

<td class="px-6 py-4 text-sm text-primary-900 font-mono">{{ $ticket->ticket_number }}</td>
<td class="px-6 py-4 text-sm text-primary-900">{{ $ticket->subject }}</td>
<td class="px-6 py-4 text-sm text-primary-600">{{ $ticket->client->first_name }} {{ $ticket->client->last_name }}</td>
<td class="px-6 py-4 text-sm">
    @include('components.status-badge', ['status' => $ticket->status])
</td>
<td class="px-6 py-4 text-sm">
    @include('components.priority-badge', ['priority' => $ticket->priority])
</td>
<td class="px-6 py-4 text-sm text-primary-600">{{ $ticket->created_at->format('M d, Y') }}</td>
<td class="px-6 py-4 text-sm font-medium">
    <div class="flex space-x-2">
        <a href="{{ route('admin.tickets.edit', $ticket->id) }}" class="text-secondary-600 hover:text-secondary-900" title="Edit">
            <i class="fas fa-edit"></i>
        </a>
        <a href="{{ route('admin.tickets.chat', $ticket->id) }}" class="text-blue-600 hover:text-blue-900" title="Chat">
            <i class="fas fa-comments"></i>
        </a>
        @include('admin.ticket.destroy')
    </div>
</td>
