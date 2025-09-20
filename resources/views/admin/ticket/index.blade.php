<x-admin.layout.app>
    @php
        /**
         * =======================================
         * Page Setup: Status, Title, Breadcrumbs
         * =======================================
         */

        // 1. Retrieve status filter from the request (?status=open/closed/in_progress/resolved)
        $status = request()->status;

        // 2. Map status values to human-readable titles
        $statuses = [
            'open' => 'Open Tickets',
            'closed' => 'Closed Tickets',
            'in_progress' => 'In Progress Tickets',
            'resolved' => 'Resolved Tickets',
        ];

        // 3. Determine the page title based on filter, fallback to "All Tickets"
        $pageTitle = $statuses[$status] ?? 'All Tickets';

        // 4. Base breadcrumb items
        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'All Tickets', 'url' => route('admin.tickets.index')],
        ];

        // 5. Append specific status breadcrumb if filter applied
        if (isset($statuses[$status])) {
            $breadcrumbItems[] = [
                'label' => $statuses[$status],
                'url' => route('admin.tickets.index', ['status' => $status]),
            ];
        }
    @endphp

    {{-- ===========================
         Set HTML Page Title
    ============================ --}}
    <x-slot:title>{{ $pageTitle }}</x-slot:title>

    {{-- ===========================
         Breadcrumb Navigation
    ============================ --}}
    <x-breadcrumb :items="$breadcrumbItems" />

    {{-- ===========================
         Main Content Wrapper
    ============================ --}}
    <div>

        {{-- ===========================
             Flash Messages Section
        ============================ --}}
        @if (session('success') || session('error'))
            <x-flash-message :type="session('success') ? 'success' : 'error'" :title="session('success') ? 'Success' : 'Error'" :message="session('success') ?? session('error')" />
        @endif

        {{-- ===========================
             Stats Cards Section
        ============================ --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 mb-6">

            {{-- Total Tickets --}}
            <x-card.stat-card label="Total Tickets" :value="$totalTickets" icon="fas fa-ticket-alt" iconBgColor="bg-primary-50"
                iconTextColor="text-primary-600" />

            {{-- Ticket Status Counts --}}
            @foreach (App\Enums\TicketStatus::cases() as $ticketStatus)
                <x-card.stat-card :label="$ticketStatus->label() . ' Tickets'" :icon="match ($ticketStatus) {
                    App\Enums\TicketStatus::OPEN => 'fas fa-folder-open',
                    App\Enums\TicketStatus::IN_PROGRESS => 'fas fa-spinner',
                    App\Enums\TicketStatus::RESOLVED => 'fas fa-check-circle',
                    App\Enums\TicketStatus::CLOSED => 'fas fa-lock',
                }" :value="$statusCounts[$ticketStatus->value] ?? 0" :iconBgColor="match ($ticketStatus) {
                    App\Enums\TicketStatus::OPEN => 'bg-green-50',
                    App\Enums\TicketStatus::IN_PROGRESS => 'bg-yellow-50',
                    App\Enums\TicketStatus::RESOLVED => 'bg-blue-50',
                    App\Enums\TicketStatus::CLOSED => 'bg-gray-50',
                }"
                    :iconTextColor="match ($ticketStatus) {
                        App\Enums\TicketStatus::OPEN => 'text-green-600',
                        App\Enums\TicketStatus::IN_PROGRESS => 'text-yellow-600',
                        App\Enums\TicketStatus::RESOLVED => 'text-blue-600',
                        App\Enums\TicketStatus::CLOSED => 'text-gray-600',
                    }" />
            @endforeach

            {{-- Ticket Priority Counts --}}
            @foreach (App\Enums\TicketPriority::cases() as $ticketPriority)
                <x-card.stat-card :label="$ticketPriority->label() . ' Priority Tickets'" :icon="match ($ticketPriority) {
                    App\Enums\TicketPriority::LOW => 'fas fa-arrow-down',
                    App\Enums\TicketPriority::MEDIUM => 'fas fa-equals',
                    App\Enums\TicketPriority::HIGH => 'fas fa-exclamation-circle',
                }" :value="$priorityCounts[$ticketPriority->value] ?? 0" :iconBgColor="match ($ticketPriority) {
                    App\Enums\TicketPriority::LOW => 'bg-gray-50',
                    App\Enums\TicketPriority::MEDIUM => 'bg-orange-50',
                    App\Enums\TicketPriority::HIGH => 'bg-red-50',
                }"
                    :iconTextColor="match ($ticketPriority) {
                        App\Enums\TicketPriority::LOW => 'text-gray-600',
                        App\Enums\TicketPriority::MEDIUM => 'text-orange-600',
                        App\Enums\TicketPriority::HIGH => 'text-red-600',
                    }" />
            @endforeach
        </div>

        {{-- ===========================
             Tickets Table Section
        ============================ --}}
        <div class="bg-white rounded-xl shadow-sm">

            {{-- ====================================
                 Table Header: Title + Actions
            ==================================== --}}
            <div class="p-6 border-b border-gray-200">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">

                    {{-- Section Title --}}
                    <h3 class="text-lg font-semibold text-primary-900 mb-4 md:mb-0">{{ $pageTitle }}</h3>

                    {{-- Table Actions: Add Ticket + Export --}}
                    <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-4">
                        
                        {{-- Search Form --}}
                        <form method="GET" action="{{ route('admin.tickets.index') }}"
                            class="relative w-full md:w-auto">
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Search tickets..."
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-accent-500 focus:border-accent-500 transition duration-300">
                            <button type="submit"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-accent-500 transition">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>

                        <a href="{{ route('admin.tickets.create') }}"
                            class="bg-accent-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-accent-600 transition">
                            Add Ticket
                        </a>
                        <a href="{{ route('admin.tickets.export', ['status' => $status]) }}"
                            class="bg-gray-100 hover:bg-gray-200 text-primary-700 px-4 py-2 rounded-lg text-sm font-medium transition">
                            <i class="fas fa-download mr-2"></i>Export
                        </a>
                    </div>
                </div>
            </div>

            {{-- =============================
                 Table Body
            ============================= --}}
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase">#SL</th>
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

                                {{-- Serial Number --}}
                                <td class="px-6 py-4 text-sm text-primary-900 font-mono">
                                    #{{ $tickets->firstItem() + $index }}</td>

                                {{-- Ticket Number --}}
                                <td class="px-6 py-4 text-sm text-primary-900 font-mono">{{ $ticket->ticket_number }}
                                </td>

                                {{-- Subject --}}
                                <td class="px-6 py-4 text-sm text-primary-900">{{ $ticket->subject }}</td>

                                {{-- Client Name --}}
                                <td class="px-6 py-4 text-sm text-primary-600">
                                    {{ $ticket->client->first_name }} {{ $ticket->client->last_name }}
                                </td>

                                {{-- Status Badge --}}
                                <td class="px-6 py-4 text-sm">
                                    @include('components.status-badge', ['status' => $ticket->status])
                                </td>

                                {{-- Priority Badge --}}
                                <td class="px-6 py-4 text-sm">
                                    @include('components.priority-badge', [
                                        'priority' => $ticket->priority,
                                    ])
                                </td>

                                {{-- Created Date --}}
                                <td class="px-6 py-4 text-sm text-primary-600">
                                    {{ $ticket->created_at->format('M d, Y') }}</td>

                                {{-- Action Buttons --}}
                                <td class="px-6 py-4 text-sm font-medium">
                                    <div class="flex space-x-2">
                                        {{-- Edit Ticket --}}
                                        <a href="{{ route('admin.tickets.edit', $ticket->id) }}"
                                            class="text-secondary-600 hover:text-secondary-900" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        {{-- Chat --}}
                                        <a href="{{ route('admin.tickets.chat', $ticket->id) }}"
                                            class="text-blue-600 hover:text-blue-900" title="Chat">
                                            <i class="fas fa-comments"></i>
                                        </a>

                                        {{-- Delete Ticket --}}
                                        @include('admin.ticket.destroy')
                                    </div>
                                    <x-confirm-modal />
                                </td>
                            </tr>
                        @empty
                            {{-- Empty State --}}
                            <tr>
                                <td colspan="8" class="text-center py-4 text-sm text-gray-500">No tickets found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ===============================
                 Pagination & Results Info
            =============================== --}}
            <div class="px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    {{-- Results Info --}}
                    <div class="text-sm text-primary-600">
                        @if ($tickets->total() > 0)
                            Showing {{ $tickets->firstItem() }} to {{ $tickets->lastItem() }} of
                            {{ $tickets->total() }} results
                        @else
                            No results found.
                        @endif
                    </div>

                    {{-- Pagination Links --}}
                    <div class="flex space-x-2">
                        <x-pagination :paginator="$tickets" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin.layout.app>
