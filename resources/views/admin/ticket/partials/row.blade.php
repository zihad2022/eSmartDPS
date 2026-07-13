@php($ticket = $row)

<td class="px-6 py-4 text-sm text-primary-900 font-mono">{{ $ticket->ticket_number }}</td>
<td class="px-6 py-4 text-sm text-primary-900">{{ $ticket->subject }}</td>
<td class="px-6 py-4 text-sm text-primary-600">{{ $ticket->client?->full_name ?? 'Unknown client' }}</td>
<td class="px-6 py-4 text-sm">@include('components.status-badge', ['status' => $ticket->status])</td>
<td class="px-6 py-4 text-sm">@include('components.priority-badge', ['priority' => $ticket->priority])</td>
<td class="px-6 py-4 text-sm text-primary-600">{{ $ticket->created_at->format('M d, Y') }}</td>
<td class="px-6 py-4 text-sm font-medium">
    <div class="flex space-x-2">
        <a href="{{ route('admin.tickets.show', $ticket) }}" class="text-accent-600 hover:text-accent-900" title="View">
            <i class="fas fa-eye"></i>
        </a>
        @adminCan('edit tickets')
            <a href="{{ route('admin.tickets.edit', $ticket) }}" class="text-secondary-600 hover:text-secondary-900" title="Edit">
                <i class="fas fa-edit"></i>
            </a>
        @endadminCan
        @adminCan('view ticket chats')
            <a href="{{ route('admin.tickets.chat', $ticket) }}" class="text-blue-600 hover:text-blue-900" title="Chat">
                <i class="fas fa-comments"></i>
            </a>
        @endadminCan
        @adminCan('delete tickets')
            @include('admin.ticket.destroy')
        @endadminCan
    </div>
</td>
