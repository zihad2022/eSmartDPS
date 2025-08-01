<x-admin.layout.app>
    @php
        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'Tickets', 'url' => route('admin.tickets.index')],
        ];

        if (request()->status === 'open') {
            $breadcrumbItems[] = [
                'label' => 'Open Tickets',
                'url' => route('admin.tickets.index', ['status' => 'open']),
            ];
        } elseif (request()->status === 'closed') {
            $breadcrumbItems[] = [
                'label' => 'Closed Tickets',
                'url' => route('admin.tickets.index', ['status' => 'closed']),
            ];
        } elseif (request()->status === 'in_progress') {
            $breadcrumbItems[] = [
                'label' => 'In Progress Tickets',
                'url' => route('admin.tickets.index', ['status' => 'in_progress']),
            ];
        } elseif (request()->status === 'resolved') {
            $breadcrumbItems[] = [
                'label' => 'Resolved Tickets',
                'url' => route('admin.tickets.index', ['status' => 'resolved']),
            ];
        }
    @endphp
    <x-breadcrumb :items="$breadcrumbItems" />
    <div>
        @if (session('success') || session('error'))
            <x-flash-message :type="session('success') ? 'success' : 'error'" :title="session('success') ? 'Success' : 'Error'" :message="session('success') ?? session('error')" />
        @endif
        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 mb-6">
            <x-card.stat-card label="Total Tickets" icon="fas fa-ticket-alt" :value="$totalTickets" bgColor="bg-primary-50"
                textColor="text-primary-600" />
            <x-card.stat-card label="Open Tickets" icon="fas fa-folder-open" :value="$openTickets" bgColor="bg-green-50"
                textColor="text-green-600" />
            <x-card.stat-card label="High Priority" icon="fas fa-exclamation-circle" :value="$highPriorityTickets"
                bgColor="bg-red-50" textColor="text-red-600" />
            <x-card.stat-card label="Closed Tickets" icon="fas fa-check-circle" :value="$closedTickets" bgColor="bg-gray-50"
                textColor="text-gray-600" />
        </div>

        <!-- Tickets Table -->
        <div class="bg-white rounded-xl shadow-sm">
            <div class="p-6 border-b border-gray-200">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <h3 class="text-lg font-semibold text-primary-900 mb-4 md:mb-0">All Tickets</h3>
                    <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-4">
                        <a href="{{ route('admin.tickets.create') }}"
                            class="bg-accent-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-accent-600 transition">
                            <i class="fas fa-plus mr-2"></i>Create Ticket
                        </a>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase">sl</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase">Ticket No
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase">Subject</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase">Client</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase">Priority</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase">Created At
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($tickets as $index => $ticket)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-primary-900 font-mono">
                                    #{{ $tickets->firstItem() + $index }}
                                </td>
                                <td class="px-6 py-4 text-sm text-primary-900 font-mono">{{ $ticket->ticket_number }}
                                </td>
                                <td class="px-6 py-4 text-sm text-primary-900">{{ $ticket->subject }}</td>
                                <td class="px-6 py-4 text-sm text-primary-600">
                                    {{ $ticket->client->first_name }} {{ $ticket->client->last_name }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    @include('components.status-badge', [
                                        'status' => $ticket->status,
                                    ])
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    @include('components.priority-badge', [
                                        'priority' => $ticket->priority,
                                    ])
                                </td>
                                <td class="px-6 py-4 text-sm text-primary-600">
                                    {{ $ticket->created_at->format('M d, Y') }}</td>
                                <td class="px-6 py-4 text-sm font-medium">
                                    <div class="flex space-x-2">
                                        {{-- <a href="{{ route('admin.tickets.show', $ticket->id) }}"
                                            class="text-accent-600 hover:text-accent-900" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a> --}}

                                        <a href="{{ route('admin.tickets.edit', $ticket->id) }}"
                                            class="text-secondary-600 hover:text-secondary-900" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <a href="{{ route('admin.tickets.chat', $ticket->id) }}"
                                            class="text-blue-600 hover:text-blue-900" title="Chat">
                                            <i class="fas fa-comments"></i>
                                        </a>
                                        @include('admin.ticket.destroy')
                                    </div>
                                </td>
                                <x-confirm-modal />
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-sm text-gray-500">No tickets found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-primary-600">
                        @if ($tickets->total() > 0)
                            Showing {{ $tickets->firstItem() }} to {{ $tickets->lastItem() }} of
                            {{ $tickets->total() }} results
                        @else
                            No results found.
                        @endif
                    </div>
                    <div class="flex space-x-2">
                        <x-pagination :paginator="$tickets" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin.layout.app>
