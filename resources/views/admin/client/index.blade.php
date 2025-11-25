<x-admin.layout.app>

    @php
        $status = request('status');
        $searchQuery = request('search');

        $pageTitle =
            [
                'active' => 'Active Clients',
                'inactive' => 'Inactive Clients',
            ][$status] ?? 'All Clients';

        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'All Clients', 'url' => route('admin.clients.index')],
        ];

        if ($status) {
            $breadcrumbItems[] = [
                'label' => $pageTitle,
                'url' => route('admin.clients.index', ['status' => $status]),
            ];
        }
    @endphp

    <x-slot:title>{{ $pageTitle }}</x-slot:title>

    <x-breadcrumb :items="$breadcrumbItems" />

    <div class="space-y-6">

        @if (session('success') || session('error'))
            <x-flash-message :type="session('success') ? 'success' : 'error'" :title="session('success') ? 'Success' : 'Error'" :message="session('success') ?? session('error')" />
        @endif

        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 md:gap-6">
            <x-card.stat-card label="Total Clients" :value="$totalClients" icon="fas fa-users" iconBgColor="bg-primary-100"
                iconTextColor="text-primary-600" />
            <x-card.stat-card label="Active Clients" :value="$activeClients" icon="fas fa-user-check" iconBgColor="bg-green-100"
                iconTextColor="text-green-600" />
            <x-card.stat-card label="Suspended Clients" :value="$inactiveClients" icon="fas fa-user-times"
                iconBgColor="bg-red-100" iconTextColor="text-red-600" />
        </div>

        <x-admin.table :pageTitle="$pageTitle" :columns="['SL', 'User ID', 'Name', 'Email', 'Phone', 'Role', 'Status', 'Joined On', 'Actions']">

            <x-slot:header>
                <x-search-form :action="route('admin.clients.index')" :value="$searchQuery" placeholder="Search clients..." />
                <x-buttons.button href="{{ route('admin.clients.create') }}">Add Client</x-buttons.button>
                <x-buttons.button variant="gray" href="{{ route('admin.clients.export', ['status' => $status]) }}"
                    icon="fas fa-download">Export</x-buttons.button>
            </x-slot:header>

            @forelse ($clients as $client)
                <tr>
                    <td class="px-6 py-4">{{ $loop->iteration }}</td>
                    <td class="px-6 py-4">{{ $client->user_id }}</td>
                    <td class="px-6 py-4">{{ $client->first_name . ' ' . $client->last_name }}</td>
                    <td class="px-6 py-4">{{ $client->email }}</td>
                    <td class="px-6 py-4">{{ $client->phone }}</td>
                    <td class="px-6 py-4">{{ $client->role }}</td>
                    <td class="px-6 py-4">{{ $client->status ? 'Active' : 'Inactive' }}</td>
                    <td class="px-6 py-4">{{ $client->created_at->format('Y-m-d') }}</td>
                    <td class="px-6 py-4 text-sm font-medium">
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.clients.show', $client->id) }}"
                                class="text-accent-600 hover:text-accent-900" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.clients.edit', $client->id) }}"
                                class="text-secondary-600 hover:text-secondary-900" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.clients.destroy', $client->id) }}"
                                class="delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="text-red-600 hover:text-red-900 delete-btn"
                                    title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            <a href="{{ route('admin.client.impersonate', $client->id) }}" target="_blank"
                                class="text-yellow-500 hover:text-yellow-700" title="Login as Client">
                                <i class="fas fa-user-shield"></i>
                            </a>
                        </div>
                        <x-confirm-modal />
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center py-4 text-sm text-gray-500">No clients found.</td>
                </tr>
            @endforelse

            <x-slot:footer>
                @if ($clients instanceof \Illuminate\Pagination\AbstractPaginator)
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-primary-600">
                            @if ($clients->total() > 0)
                                Showing {{ $clients->firstItem() }} to {{ $clients->lastItem() }} of
                                {{ $clients->total() }} results
                            @else
                                No results found.
                            @endif
                        </div>
                        <div class="flex space-x-2">
                            <x-pagination :paginator="$clients" />
                        </div>
                    </div>
                @endif
            </x-slot:footer>

        </x-admin.table>
    </div>

</x-admin.layout.app>
