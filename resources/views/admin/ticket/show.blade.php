<x-admin.layout.app>
    <x-slot:title>Ticket Details</x-slot:title>
    <x-breadcrumb :items="[
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Tickets', 'url' => route('admin.tickets.index')],
        ['label' => '#'.$ticket->ticket_number],
    ]" />

    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                <div>
                    <p class="text-sm font-mono text-accent-600">#{{ $ticket->ticket_number }}</p>
                    <h2 class="mt-1 text-2xl font-bold text-primary-900">{{ $ticket->subject }}</h2>
                    <p class="mt-2 text-sm text-primary-500">
                        Opened by {{ $ticket->client?->full_name ?? 'Unknown client' }} on {{ $ticket->created_at->format('M d, Y h:i A') }}
                    </p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-700">{{ $ticket->status->label() }}</span>
                    <span class="rounded-full bg-orange-100 px-3 py-1 text-xs font-medium text-orange-700">{{ $ticket->priority->label() }} Priority</span>
                </div>
            </div>

            <div class="mt-6 rounded-lg bg-gray-50 p-5 text-sm leading-6 text-primary-700 whitespace-pre-line">{{ $ticket->message }}</div>

            @if ($ticket->admin_notes)
                <div class="mt-5 rounded-lg border border-yellow-200 bg-yellow-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-yellow-700">Admin Notes</p>
                    <p class="mt-2 text-sm text-yellow-900 whitespace-pre-line">{{ $ticket->admin_notes }}</p>
                </div>
            @endif

            <div class="mt-6 flex flex-wrap justify-end gap-3">
                @adminCan('view ticket chats')
                    <a href="{{ route('admin.tickets.chat', $ticket) }}" class="rounded-lg bg-accent-500 px-4 py-2 text-sm text-white hover:bg-accent-600">
                        <i class="fas fa-comments mr-2"></i>Open Chat
                    </a>
                @endadminCan
                @adminCan('edit tickets')
                    <a href="{{ route('admin.tickets.edit', $ticket) }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-primary-700 hover:bg-gray-50">
                        <i class="fas fa-edit mr-2"></i>Edit Ticket
                    </a>
                @endadminCan
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-primary-900 mb-4">Conversation Summary</h3>
            <div class="space-y-4">
                @forelse ($ticket->replies as $reply)
                    <div class="rounded-lg border border-gray-200 p-4">
                        <div class="flex items-center justify-between gap-4">
                            <p class="text-sm font-medium text-primary-900">
                                {{ $reply->admin?->name ?? $reply->client?->full_name ?? 'Unknown sender' }}
                                <span class="ml-1 text-xs font-normal text-primary-500">({{ $reply->admin_id ? 'Admin' : 'Client' }})</span>
                            </p>
                            <time class="text-xs text-primary-500">{{ $reply->created_at->format('M d, Y h:i A') }}</time>
                        </div>
                        @if ($reply->message)
                            <p class="mt-2 text-sm text-primary-700 whitespace-pre-line">{{ $reply->message }}</p>
                        @endif
                        @if ($reply->attachment)
                            <a href="{{ asset('storage/'.$reply->attachment) }}" target="_blank" rel="noopener" class="mt-2 inline-block text-sm text-accent-600 hover:underline">
                                <i class="fas fa-paperclip mr-1"></i>{{ basename($reply->attachment) }}
                            </a>
                        @endif
                    </div>
                @empty
                    <p class="py-4 text-center text-sm text-primary-500">No replies yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-admin.layout.app>
