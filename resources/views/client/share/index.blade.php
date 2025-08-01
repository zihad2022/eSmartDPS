<x-client.layout.app>
    <!-- Shares Content -->
    <div class="">
        @if (session('success') || session('error'))
            <x-flash-message :type="session('success') ? 'success' : 'error'" :title="session('success') ? 'Success' : 'Error'" :message="session('success') ?? session('error')" />
        @endif
        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 mb-6">
            <x-card.stat-card label="Total Share Plans" :value="$totalShares" icon="fas fa-layer-group" bgColor="bg-accent-100"
                textColor="text-accent-600" />

            <x-card.stat-card label="Active Plans" :value="$activeShares" icon="fas fa-check-circle" bgColor="bg-green-100"
                textColor="text-green-600" />

            <x-card.stat-card label="Inactive Plans" :value="$inactiveShares" icon="fas fa-times-circle" bgColor="bg-red-100"
                textColor="text-red-600" />
            {{-- <x-card.stat-card label="Total Revenue" :value="$totalRevenue" icon="fas fa-coins" bgColor="bg-yellow-100"
                textColor="text-yellow-600" /> --}}
        </div>

        <!-- Shares Table -->
        <div class="bg-white rounded-xl shadow-sm">
            <div class="p-6 border-b border-gray-200">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <h3 class="text-lg font-semibold text-primary-900 mb-4 md:mb-0">All Share Plans</h3>
                    <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-4">

                        <!-- Add Share Button -->
                        <a href="{{ route('client.shares.create') }}"
                            class="bg-accent-500 hover:bg-accent-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-300">
                            Add Share Plan
                        </a>

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
                                Plan Name</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Price</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Status</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Created At</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($shares as $share)
                            <tr class="hover:bg-gray-50">
                                <!-- Name -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-primary-900">
                                    {{ $share->name }}
                                </td>

                                <!-- Price -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-primary-600">
                                    {{ number_format($share->price, 2) }} ৳
                                </td>

                                <!-- Status -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($share->is_active)
                                        <span
                                            class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Active</span>
                                    @else
                                        <span
                                            class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">Inactive</span>
                                    @endif
                                </td>

                                <!-- Created Date -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-primary-600">
                                    {{ $share->created_at->format('M d, Y') }}
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        {{-- <a href="{{ route('client.shares.show', $share) }}"
                                            class="text-accent-600 hover:text-accent-900">
                                            <i class="fas fa-eye"></i>
                                        </a> --}}
                                        <a href="{{ route('client.shares.edit', $share) }}"
                                            class="text-secondary-600 hover:text-secondary-900">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('client.shares.destroy', $share) }}" method="POST"
                                            onsubmit="return confirm('Are you sure?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">No share plans found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-primary-600">
                        @if ($shares->total() > 0)
                            Showing {{ $shares->firstItem() }} to {{ $shares->lastItem() }} of {{ $shares->total() }}
                            results
                        @else
                            No results found.
                        @endif
                    </div>
                    <div class="flex space-x-2">
                        <x-pagination :paginator="$shares" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-client.layout.app>
