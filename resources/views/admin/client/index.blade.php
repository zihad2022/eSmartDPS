<x-admin.layout.app>
    @php
        // Get the status query parameter from the request (e.g., 'active', 'inactive')
        $status = request()->status;

        // Map status values to human-readable titles for the page
        $titleMap = [
            'active' => 'Active Clients',
            'inactive' => 'Inactive Clients',
        ];

        // Determine page title based on the status filter, default to 'All Clients'
        $pageTitle = $titleMap[$status] ?? 'All Clients';

        // Base breadcrumb items: Dashboard > All Clients
        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'All Clients', 'url' => route('admin.clients.index')],
        ];

        // Append specific status breadcrumb if a valid status is set
        if (isset($titleMap[$status])) {
            $breadcrumbItems[] = [
                'label' => $pageTitle,
                'url' => route('admin.clients.index', ['status' => $status]),
            ];
        }
    @endphp

    {{-- Set the HTML page title dynamically --}}
    <x-slot:title>{{ $pageTitle }}</x-slot:title>

    {{-- Render breadcrumb navigation based on current page location --}}
    <x-breadcrumb :items="$breadcrumbItems" />

    <!-- Main Content Wrapper -->
    <div>
        {{-- Display flash messages (success or error) if any --}}
        @if (session('success') || session('error'))
            <x-flash-message :type="session('success') ? 'success' : 'error'" :title="session('success') ? 'Success' : 'Error'" :message="session('success') ?? session('error')" />
        @endif

        <!-- Stats Cards: Summary info for clients -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 mb-6">
            <x-card.stat-card :label="'Total Members'" :value="$totalMembers" :bgColor="'bg-primary-100'" :textColor="'text-primary-600'"
                :icon="'fas fa-users'" />

            <x-card.stat-card :label="'Active Members'" :value="$activeMembers" :bgColor="'bg-green-100'" :textColor="'text-green-600'"
                :icon="'fas fa-user-check'" />

            {{-- Inactive Members card is commented out but can be re-enabled --}}
            {{-- <x-card.stat-card :label="'Inactive Members'" :value="$inactiveMembers" :bgColor="'bg-red-100'" :textColor="'text-red-600'" :icon="'fas fa-user-times'" />  --}}

            <x-card.stat-card :label="'Total Shares'" :value="0" :bgColor="'bg-red-100'" :textColor="'text-red-600'"
                :icon="'fas fa-chart-pie'" />

            <x-card.stat-card :label="'Suspended'" :value="$inactiveMembers" :bgColor="'bg-red-100'" :textColor="'text-red-600'"
                :icon="'fas fa-user-times'" />
        </div>

        <!-- Clients Table Container -->
        <div class="bg-white rounded-xl shadow-sm">
            {{-- Table Header with title and action buttons --}}
            <div class="p-6 border-b border-gray-200">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <h3 class="text-lg font-semibold text-primary-900 mb-4 md:mb-0">
                        {{ $pageTitle }}
                    </h3>

                    {{-- Action buttons: Add Client and Export Clients --}}
                    <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-4">
                        <a href="{{ route('admin.clients.create') }}"
                            class="bg-accent-500 hover:bg-accent-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-300">
                            Add Client
                        </a>
                        <a href="{{ route('admin.clients.export', ['status' => $status]) }}"
                            class="bg-gray-100 hover:bg-gray-200 text-primary-700 px-4 py-2 rounded-lg text-sm font-medium transition duration-300">
                            <i class="fas fa-download mr-2"></i>Export
                        </a>
                    </div>
                </div>
            </div>

            {{-- Responsive table wrapper with horizontal scroll --}}
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    {{-- Table column headers --}}
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                sl</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                User ID</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Name</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Email</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Status</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Joined On</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>

                    {{-- Table body with client rows --}}
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php
                            // Initialize serial number starting from current pagination first item
                            $sl = $clients->firstItem() ?? 1;
                        @endphp

                        {{-- Loop through clients and display each in a table row --}}
                        @forelse ($clients as $client)
                            <tr class="hover:bg-gray-50">
                                {{-- Serial Number --}}
                                <td class="px-6 py-4 text-sm text-primary-900 font-mono">#{{ $sl++ }}</td>

                                {{-- User ID --}}
                                <td class="px-6 py-4 text-sm text-primary-900 font-mono">{{ $client->user_id }}</td>

                                {{-- Client Name with Profile Photo or Placeholder Avatar --}}
                                <td class="px-6 py-4 whitespace-nowrap flex items-center">
                                    <img src="{{ $client->profile_photo
                                        ? asset('storage/' . $client->profile_photo)
                                        : 'https://ui-avatars.com/api/?name=' . urlencode($client->first_name . ' ' . $client->last_name) }}"
                                        alt="Profile Photo" class="w-10 h-10 rounded-full mr-3">
                                    <span class="text-sm font-medium text-primary-900">
                                        {{ $client->first_name }} {{ $client->last_name }}
                                    </span>
                                </td>

                                {{-- Email Address --}}
                                <td class="px-6 py-4 text-sm text-primary-600">{{ $client->email }}</td>

                                {{-- Status Badge: Active (green) or Inactive (red) --}}
                                <td class="px-6 py-4">
                                    <span
                                        class="px-2 py-1 text-xs font-medium rounded-full 
                                        {{ $client->status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $client->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>

                                {{-- Date client joined, formatted --}}
                                <td class="px-6 py-4 text-sm text-primary-600">
                                    {{ $client->created_at->format('M d, Y') }}
                                </td>

                                {{-- Actions: View, Edit, Delete --}}
                                <td class="px-6 py-4 text-sm font-medium">
                                    <div class="flex space-x-2">
                                        {{-- View Client --}}
                                        <a href="{{ route('admin.clients.show', $client->id) }}"
                                            class="text-accent-600 hover:text-accent-900" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        {{-- Edit Client --}}
                                        <a href="{{ route('admin.clients.edit', $client->id) }}"
                                            class="text-secondary-600 hover:text-secondary-900" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        {{-- Delete Client Form with confirmation --}}
                                        <form method="POST" action="{{ route('admin.clients.destroy', $client->id) }}"
                                            class="delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="text-red-600 hover:text-red-900 delete-btn"
                                                title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>

                                {{-- Confirm modal component for deletion confirmation --}}
                                <x-confirm-modal />
                            </tr>

                            {{-- Show message if no clients are found --}}
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-sm text-gray-500">
                                    No clients found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>

            <!-- Pagination Controls and Showing Results Info -->
            <div class="px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    {{-- Showing items range and total count --}}
                    <div class="text-sm text-primary-600">
                        @if ($clients->total() > 0)
                            Showing {{ $clients->firstItem() }} to {{ $clients->lastItem() }} of
                            {{ $clients->total() }} results
                        @else
                            No results found.
                        @endif
                    </div>

                    {{-- Pagination links --}}
                    <div class="flex space-x-2">
                        <x-pagination :paginator="$clients" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin.layout.app>
