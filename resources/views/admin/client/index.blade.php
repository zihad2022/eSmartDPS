<x-admin.layout.app>
    @php
        $status = request()->status;
        $titleMap = [
            'active' => 'Active Clients',
            'inactive' => 'Inactive Clients',
        ];

        $pageTitle = $titleMap[$status] ?? 'All Clients';

        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'All Clients', 'url' => route('admin.clients.index')],
        ];

        if (isset($titleMap[$status])) {
            $breadcrumbItems[] = [
                'label' => $pageTitle,
                'url' => route('admin.clients.index', ['status' => $status]),
            ];
        }
    @endphp

    <x-slot:title>{{ $pageTitle }}</x-slot:title>
    <x-breadcrumb :items="$breadcrumbItems" />


    <!-- Members Content -->
    <div>
        @if (session('success') || session('error'))
            <x-flash-message :type="session('success') ? 'success' : 'error'" :title="session('success') ? 'Success' : 'Error'" :message="session('success') ?? session('error')" />
        @endif
        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 mb-6">
            <x-card.stat-card :label="'Total Members'" :value="$totalMembers" :bgColor="'bg-primary-100'" :textColor="'text-primary-600'"
                :icon="'fas fa-users'" />

            <x-card.stat-card :label="'Active Members'" :value="$activeMembers" :bgColor="'bg-green-100'" :textColor="'text-green-600'"
                :icon="'fas fa-user-check'" />

            {{-- <x-card.stat-card :label="'Inactive Members'" :value="$inactiveMembers" :bgColor="'bg-red-100'" :textColor="'text-red-600'"
                :icon="'fas fa-user-times'" />  --}}


            <x-card.stat-card :label="'Total Shares'" :value="0" :bgColor="'bg-red-100'" :textColor="'text-red-600'"
                :icon="'fas fa-chart-pie'" />

            <x-card.stat-card :label="'Suspended'" :value="$inactiveMembers" :bgColor="'bg-red-100'" :textColor="'text-red-600'"
                :icon="'fas fa-user-times'" />
        </div>

        <!-- Clients Table -->
        <div class="bg-white rounded-xl shadow-sm">
            <div class="p-6 border-b border-gray-200">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <h3 class="text-lg font-semibold text-primary-900 mb-4 md:mb-0">
                        {{ $pageTitle }}
                    </h3>
                    <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-4">
                        <a href="{{ route('admin.clients.create') }}"
                            class="bg-accent-500 hover:bg-accent-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-300">
                            Add Client
                        </a>
                        <a href="{{ route('admin.clients.export') }}"
                            class="bg-gray-100 hover:bg-gray-200 text-primary-700 px-4 py-2 rounded-lg text-sm font-medium transition duration-300">
                            <i class="fas fa-download mr-2"></i>Export
                        </a>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
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

                    <tbody class="bg-white divide-y divide-gray-200">
                        @php $sl = $clients->firstItem() ?? 1; @endphp

                        @forelse ($clients as $client)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-primary-900 font-mono">#{{ $sl++ }}</td>

                                <td class="px-6 py-4 text-sm text-primary-900 font-mono">{{ $client->user_id }}</td>

                                <td class="px-6 py-4 whitespace-nowrap flex items-center">
                                    <img src="{{ $client->profile_photo
                                        ? asset('storage/' . $client->profile_photo)
                                        : 'https://ui-avatars.com/api/?name=' . urlencode($client->first_name . ' ' . $client->last_name) }}"
                                        alt="Profile Photo" class="w-10 h-10 rounded-full mr-3">
                                    <span class="text-sm font-medium text-primary-900">
                                        {{ $client->first_name }} {{ $client->last_name }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-sm text-primary-600">{{ $client->email }}</td>

                                <td class="px-6 py-4">
                                    <span
                                        class="px-2 py-1 text-xs font-medium rounded-full 
                                        {{ $client->status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $client->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-sm text-primary-600">
                                    {{ $client->created_at->format('M d, Y') }}
                                </td>

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
                                    </div>
                                </td>

                                <x-confirm-modal />
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-sm text-gray-500">No clients found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>
            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
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
            </div>
        </div>
    </div>
</x-admin.layout.app>
