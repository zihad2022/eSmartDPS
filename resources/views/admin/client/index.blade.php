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
            <x-flash-message :type="session('success') ? 'success' : 'error'" :title="session('success') ? 'Success' : 'Error'" :message="session('success') ?? session('error')" />
        @endif

        {{-- ===========================
             Stats Cards Section
        ============================ --}}
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 md:gap-6 mb-6">
            {{-- Total Clients --}}
            <x-card.stat-card :label="'Total Clients'" :value="$totalClients" :iconBgColor="'bg-primary-100'" :iconTextColor="'text-primary-600'"
                :icon="'fas fa-users'" />

            {{-- Active Clients --}}
            <x-card.stat-card :label="'Active Clients'" :value="$activeClients" :iconBgColor="'bg-green-100'" :iconTextColor="'text-green-600'"
                :icon="'fas fa-user-check'" />

            {{-- Suspended Clients --}}
            <x-card.stat-card :label="'Suspended Clients'" :value="$inactiveClients" :iconBgColor="'bg-red-100'" :iconTextColor="'text-red-600'"
                :icon="'fas fa-user-times'" />
        </div>

        {{-- ===========================
            Clients Table Section (component-based)
        ============================ --}}
        <x-data-table :page-title="$pageTitle" :rows="$clients" :headers="['SL', 'User ID', 'Name', 'Email', 'Phone', 'Role', 'Status', 'Joined On', 'Actions']" row-view="admin.client.partials.row">
            <x-slot:actions>
                {{-- Search Form --}}
                <form method="GET" action="{{ route('admin.clients.index') }}" class="relative w-full md:w-auto">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search clients..."
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
            </x-slot:actions>
        </x-data-table>
    </div>
</x-admin.layout.app>
