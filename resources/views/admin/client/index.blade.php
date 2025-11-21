<x-admin.layout.app>

    @php

        $status = request('status');

        $searchQuery = request('search');

        // Page Title
        $pageTitle = ['active' => 'Active Clients', 'inactive' => 'Inactive Clients'][$status] ?? 'All Clients';

        // Breadcrumbs
        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'All Clients', 'url' => route('admin.clients.index')],
        ];

        if ($status) {
            $breadcrumbItems[] = ['label' => $pageTitle, 'url' => route('admin.clients.index', ['status' => $status])];
        }

    @endphp

    <x-slot:title>{{ $pageTitle }}</x-slot:title>

    {{-- Breadcrumb --}}
    <x-breadcrumb :items="$breadcrumbItems" />

    <div class="space-y-6">

        {{-- Flash Messages --}}
        @if (session('success') || session('error'))
            <x-flash-message :type="session('success') ? 'success' : 'error'" :title="session('success') ? 'Success' : 'Error'" :message="session('success') ?? session('error')" />
        @endif

        {{-- Stats --}}
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 md:gap-6">
            <x-card.stat-card label="Total Clients" :value="$totalClients" icon="fas fa-users" iconBgColor="bg-primary-100"
                iconTextColor="text-primary-600" />

            <x-card.stat-card label="Active Clients" :value="$activeClients" icon="fas fa-user-check" iconBgColor="bg-green-100"
                iconTextColor="text-green-600" />

            <x-card.stat-card label="Suspended Clients" :value="$inactiveClients" icon="fas fa-user-times"
                iconBgColor="bg-red-100" iconTextColor="text-red-600" />
        </div>

        {{-- Data Table --}}
        <x-data-table :page-title="$pageTitle" :rows="$clients" :headers="['SL', 'User ID', 'Name', 'Email', 'Phone', 'Role', 'Status', 'Joined On', 'Actions']" row-view="admin.client.partials.row">
            <x-slot:actions>

                {{-- Search --}}
                <x-search-form :action="route('admin.clients.index')" :value="$searchQuery" placeholder="Search clients..." />

                {{-- Add --}}
                <x-buttons.button href="{{ route('admin.clients.create') }}">
                    Add Client
                </x-buttons.button>

                {{-- Export --}}
                <x-buttons.button variant="gray" href="{{ route('admin.clients.export', ['status' => $status]) }}"
                    icon="fas fa-download">
                    Export
                </x-buttons.button>

            </x-slot:actions>
        </x-data-table>
        
    </div>

</x-admin.layout.app>
