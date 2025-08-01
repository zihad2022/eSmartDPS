<x-client.layout.app>
    <!-- Ledger Categories Content -->
    <div class="p-4 md:p-6">
        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 mb-6">
            <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs md:text-sm text-primary-500 font-medium">Total Categories</p>
                        <h3 class="text-xl md:text-2xl font-bold text-primary-900">
                            {{ $categories->total() ?? $categories->count() }}</h3>
                    </div>
                    <div class="w-10 h-10 bg-accent-100 text-accent-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-folder text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs md:text-sm text-primary-500 font-medium">Income Categories</p>
                        <h3 class="text-xl md:text-2xl font-bold text-primary-900">{{ $incomeCount ?? 0 }}</h3>
                    </div>
                    <div class="w-10 h-10 bg-green-100 text-green-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-arrow-up text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs md:text-sm text-primary-500 font-medium">Expense Categories</p>
                        <h3 class="text-xl md:text-2xl font-bold text-primary-900">{{ $expenseCount ?? 0 }}</h3>
                    </div>
                    <div class="w-10 h-10 bg-red-100 text-red-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-arrow-down text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs md:text-sm text-primary-500 font-medium">Active Categories</p>
                        <h3 class="text-xl md:text-2xl font-bold text-primary-900">{{ $activeCount ?? 0 }}</h3>
                    </div>
                    <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ledger Categories Table -->
        <div class="bg-white rounded-xl shadow-sm">
            <div class="p-6 border-b border-gray-200">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <h3 class="text-lg font-semibold text-primary-900 mb-4 md:mb-0">Ledger Categories</h3>
                    <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-4">
                        <!-- Type Filter -->
                        <select
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-500">
                            <option value="">All Types</option>
                            <option value="income" {{ request('type') == 'income' ? 'selected' : '' }}>Income</option>
                            <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>Expense
                            </option>
                        </select>

                        <!-- Status Filter -->
                        <select
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-500">
                            <option value="">All Status</option>
                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                        </select>

                        <!-- Export Button -->
                        <button
                            class="bg-gray-100 hover:bg-gray-200 text-primary-700 px-4 py-2 rounded-lg text-sm font-medium transition duration-300">
                            <i class="fas fa-download mr-2"></i>Export
                        </button>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Name</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Type</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Description</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Created</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Status</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($categories as $category)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-primary-900">{{ $category->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 py-1 text-xs font-medium rounded-full
                                        {{ $category->type === 'income' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ucfirst($category->type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 max-w-xs truncate whitespace-nowrap text-sm text-primary-600">
                                    {{ $category->description ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-primary-600">
                                    {{ $category->created_at->format('M d, Y h:i A') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($category->is_active)
                                        <span
                                            class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Active</span>
                                    @else
                                        <span
                                            class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-600">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('client.ledger-categories.show', $category->id) }}"
                                            class="text-accent-600 hover:text-accent-900" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('client.ledger-categories.edit', $category->id) }}"
                                            class="text-secondary-600 hover:text-secondary-900" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">No categories found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-primary-600">
                        Showing {{ $categories->firstItem() ?? 0 }} to {{ $categories->lastItem() ?? 0 }} of
                        {{ $categories->total() ?? $categories->count() }} results
                    </div>
                    <div class="flex space-x-2">
                        {{ $categories->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-client.layout.app>
