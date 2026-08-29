<x-client.layout.app>
    @php
        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('client.dashboard')],
            ['label' => 'Ledger Categories', 'url' => route('client.ledger-categories.index')],
        ];
    @endphp
    <x-breadcrumb :items="$breadcrumbItems" />
    <x-slot:title>Ledger Categories</x-slot:title>

    <div class="grid grid-cols-1 gap-8">
        {{-- Display flash messages (success or error) if any --}}
        @if (session('success') || session('error'))
            <x-flash-message :type="session('success') ? 'success' : 'error'" :title="session('success') ? 'Success' : 'Error'" :message="session('success') ?? session('error')" />
        @endif

        {{-- 🔼 FORM SECTION --}}
        @include('client.ledger-category.form')

        {{-- 🔽 STATS CARDS --}}
        {{-- <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
            <x-card.stat-card :label="'Total Categories'" :value="$ledgerCategories->total() ?? $ledgerCategories->count()" :bgColor="'bg-primary-100'" :textColor="'text-primary-600'"
                :icon="'fas fa-folder'" />
            <x-card.stat-card :label="'Active Categories'" :value="$activeCount" :bgColor="'bg-green-100'" :textColor="'text-green-600'"
                :icon="'fas fa-check-circle'" />
            <x-card.stat-card :label="'Inactive Categories'" :value="$inactiveCount" :bgColor="'bg-red-100'" :textColor="'text-red-600'"
                :icon="'fas fa-times-circle'" />
        </div> --}}

        {{-- 🔽 TABLE SECTION --}}
        <div class="bg-white rounded-xl shadow-sm">
            <!-- Header -->
            <div class="p-6 border-b border-gray-200 flex flex-col md:flex-row md:items-center md:justify-between">
                <h3 class="text-lg font-semibold text-primary-900 mb-4 md:mb-0">Ledger Categories</h3>
                <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-4">
                    <a href="{{ route('client.ledger-categories.create') }}"
                        class="bg-accent-500 hover:bg-accent-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-300">
                        Add New
                    </a>
                    <button
                        class="bg-gray-100 hover:bg-gray-200 text-primary-700 px-4 py-2 rounded-lg text-sm font-medium transition duration-300">
                        <i class="fas fa-download mr-2"></i> Export
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Name</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Description</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Created</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($ledgerCategories as $category)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-primary-900">{{ $category->name }}</td>
                                <td class="px-6 py-4 text-sm text-primary-600 truncate max-w-xs">
                                    {{ $category->description ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-primary-600">
                                    {{ $category->created_at->format('M d, Y h:i A') }}</td>
                                <td class="px-6 py-4 text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('client.ledger-categories.edit', $category->id) }}"
                                            class="text-secondary-600 hover:text-secondary-900" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('client.ledger-categories.destroy', $category->id) }}"
                                            method="POST"
                                            class="delete-form inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="text-red-600 hover:text-red-900 delete-btn"
                                                title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-primary-600">No categories found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Footer / Pagination -->
            <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                <div class="text-sm text-primary-600">
                    Showing {{ $ledgerCategories->firstItem() ?? 0 }} to {{ $ledgerCategories->lastItem() ?? 0 }} of
                    {{ $ledgerCategories->total() }}
                </div>
                <x-pagination :paginator="$ledgerCategories" />
            </div>
        </div>

    </div>
</x-client.layout.app>
