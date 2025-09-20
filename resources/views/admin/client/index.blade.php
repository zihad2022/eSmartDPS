<x-admin.layout.app>
    @php
        /**
         * =======================================
         * Page Setup: Status, Title, Breadcrumbs
         * =======================================
         */

        // 1. Retrieve status filter from the request (?status=active/inactive)
        $status = request()->status;

        // 2. Map status values to human-readable titles
        $titleMap = [
            'active' => 'Active Clients',
            'inactive' => 'Inactive Clients',
        ];

        // 3. Determine the page title based on filter, fallback to "All Clients"
        $pageTitle = $titleMap[$status] ?? 'All Clients';

        // 4. Base breadcrumb items
        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'All Clients', 'url' => route('admin.clients.index')],
        ];

        // 5. Append specific status breadcrumb if filter applied
        if (isset($titleMap[$status])) {
            $breadcrumbItems[] = [
                'label' => $pageTitle,
                'url' => route('admin.clients.index', ['status' => $status]),
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
            <x-flash-message
                :type="session('success') ? 'success' : 'error'"
                :title="session('success') ? 'Success' : 'Error'"
                :message="session('success') ?? session('error')"
            />
        @endif

        {{-- ===========================
             Stats Cards Section
        ============================ --}}
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 md:gap-6 mb-6">
            {{-- Total Clients --}}
            <x-card.stat-card
                :label="'Total Clients'"
                :value="$totalClients"
                :iconBgColor="'bg-primary-100'"
                :iconTextColor="'text-primary-600'"
                :icon="'fas fa-users'"
            />

            {{-- Active Clients --}}
            <x-card.stat-card
                :label="'Active Clients'"
                :value="$activeClients"
                :iconBgColor="'bg-green-100'"
                :iconTextColor="'text-green-600'"
                :icon="'fas fa-user-check'"
            />

            {{-- Suspended Clients --}}
            <x-card.stat-card
                :label="'Suspended Clients'"
                :value="$inactiveClients"
                :iconBgColor="'bg-red-100'"
                :iconTextColor="'text-red-600'"
                :icon="'fas fa-user-times'"
            />
        </div>

        {{-- ===========================
             Clients Table Section
        ============================ --}}
        <div class="bg-white rounded-xl shadow-sm">

            {{-- ====================================
                 Table Header (Title + Actions)
            ==================================== --}}
            <div class="p-6 border-b border-gray-200">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">

                    {{-- Section Title --}}
                    <h3 class="text-lg font-semibold text-primary-900 mb-4 md:mb-0">
                        {{ $pageTitle }}
                    </h3>

                    {{-- Table Actions (Search + Add + Export) --}}
                    <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-4">

                        {{-- Search Form --}}
                        <form method="GET" action="{{ route('admin.clients.index') }}" class="relative w-full md:w-auto">
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Search clients..."
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-accent-500 focus:border-accent-500 transition duration-300">
                            <button type="submit"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-accent-500 transition">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>

                        {{-- Add Client Button --}}
                        <a href="{{ route('admin.clients.create') }}"
                            class="bg-accent-500 hover:bg-accent-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-300">
                            Add Client
                        </a>

                        {{-- Export Button --}}
                        <a href="{{ route('admin.clients.export', ['status' => $status]) }}"
                            class="bg-gray-100 hover:bg-gray-200 text-primary-700 px-4 py-2 rounded-lg text-sm font-medium transition duration-300">
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
                    {{-- Table Headers --}}
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">SL</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">User ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">Phone</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">Division</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">District</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">Joined On</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
            
                    {{-- Table Rows --}}
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php
                            $sl = $clients->firstItem() ?? 1;
                        @endphp
            
                        @forelse ($clients as $client)
                            <tr class="hover:bg-gray-50">
                                {{-- Serial Number --}}
                                <td class="px-6 py-4 text-sm text-primary-900 font-mono">#{{ $sl++ }}</td>

                                {{-- User ID --}}
                                <td class="px-6 py-4 text-sm text-primary-900 font-mono">{{ $client->user_id }}</td>
            
                                {{-- Name + Avatar --}}
                                <td class="px-6 py-4 whitespace-nowrap flex items-center">
                                    <img src="{{ $client->profile_photo ? $client->profile_photo_url : 'https://ui-avatars.com/api/?name=' . urlencode($client->first_name . ' ' . $client->last_name) }}"
                                        alt="Profile Photo" class="w-10 h-10 rounded-full mr-3">
                                    <span class="text-sm font-medium text-primary-900">{{ $client->first_name }} {{ $client->last_name }}</span>
                                </td>
            
                                {{-- Email --}}
                                <td class="px-6 py-4 text-sm text-primary-600">{{ $client->email }}</td>

                                {{-- Phone --}}
                                <td class="px-6 py-4 text-sm text-primary-600">{{ $client->phone ?? 'N/A' }}</td>

                                {{-- Role --}}
                                <td class="px-6 py-4 text-sm text-primary-900 font-semibold">{{ ucfirst($client->role) }}</td>

                                {{-- Division --}}
                                <td class="px-6 py-4 text-sm text-primary-600">{{ $client->division ?? 'N/A' }}</td>

                                {{-- District --}}
                                <td class="px-6 py-4 text-sm text-primary-600">{{ $client->district ?? 'N/A' }}</td>
            
                                {{-- Status Badge --}}
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $client->status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $client->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
            
                                {{-- Joined Date --}}
                                <td class="px-6 py-4 text-sm text-primary-600">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full text-green-600 bg-green-100">
                                        {{ $client->created_at->format('M d, Y') }}
                                    </span>
                                </td>
            
                                {{-- Action Buttons --}}
                                <td class="px-6 py-4 text-sm font-medium">
                                    <div class="flex space-x-2">
                                        {{-- View --}}
                                        <a href="{{ route('admin.clients.show', $client->id) }}" class="text-accent-600 hover:text-accent-900" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        {{-- Edit --}}
                                        <a href="{{ route('admin.clients.edit', $client->id) }}" class="text-secondary-600 hover:text-secondary-900" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        {{-- Delete --}}
                                        <form method="POST" action="{{ route('admin.clients.destroy', $client->id) }}" class="delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="text-red-600 hover:text-red-900 delete-btn" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                    <x-confirm-modal />
                                </td>
                            </tr>
                        @empty
                            {{-- Empty State --}}
                            <tr>
                                <td colspan="11" class="text-center py-4 text-sm text-gray-500">No clients found.</td>
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
                        @if ($clients->total() > 0)
                            Showing {{ $clients->firstItem() }} to {{ $clients->lastItem() }} of {{ $clients->total() }} results
                        @else
                            No results found.
                        @endif
                    </div>

                    {{-- Pagination Links --}}
                    <div class="flex space-x-2">
                        <x-pagination :paginator="$clients" />
                    </div>
                </div>
            </div>
        </div> 
    </div> 
</x-admin.layout.app>
