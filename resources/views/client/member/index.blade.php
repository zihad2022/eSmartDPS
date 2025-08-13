<x-client.layout.app>
    <!-- Members Content -->
    <div class="">
        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 mb-6">
            <x-card.stat-card label="Total Members" :value="$totalMembers" icon="fas fa-users" bgColor="bg-accent-100"
                textColor="text-accent-600" />

            <x-card.stat-card label="Active Members" :value="$activeMembers" icon="fas fa-user-check" bgColor="bg-green-100"
                textColor="text-green-600" />

            <x-card.stat-card label="Total Shares" :value="$totalShares" icon="fas fa-chart-pie text-lg"
                bgColor="bg-secondary-100" textColor="text-secondary-600" />

            <x-card.stat-card label="Inactive Members" :value="$inactiveMembers" icon="fas fa-user-times" bgColor="bg-red-100"
                textColor="text-red-600" />
        </div>

        <!-- Members Table -->
        <div class="bg-white rounded-xl shadow-sm">
            <div class="p-6 border-b border-gray-200">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <h3 class="text-lg font-semibold text-primary-900 mb-4 md:mb-0">All Members</h3>
                    <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-4">
                        <!-- Filter Dropdown -->
                        {{-- <select
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-500">
                            <option>All Status</option>
                            <option>Active</option>
                            <option>Inactive</option>
                        </select> --}}

                        <!-- Export Button -->
                        <a href="{{ route('client.members.create') }}"
                            class="bg-accent-500 hover:bg-accent-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-300">
                            {{-- <i class="fas fa-download mr-2"></i> --}}
                            Add Member
                        </a>
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
                                SL</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Member</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Join Date</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Status</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php
                            $sl = 1;
                        @endphp
                        @forelse ($members as $member)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">#{{ $sl++ }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <img src="https://randomuser.me/api/portraits/men/32.jpg"
                                            class="w-10 h-10 rounded-full mr-4" alt="Member">
                                        <div>
                                            <div class="text-sm font-medium text-primary-900">{{ $member->name }}</div>
                                            <div class="text-sm text-primary-500">{{ $member->email }}</div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Join Date --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-primary-600">
                                    {{ $member->created_at->format('M d, Y') }}
                                </td>
                                {{-- Status --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($member->status)
                                        <span
                                            class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Active</span>
                                    @else
                                        <span
                                            class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">Inactive</span>
                                    @endif
                                </td>

                                {{-- Actions --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button class="text-accent-600 hover:text-accent-900">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <a href="{{ route('client.members.edit', $member) }}"
                                            class="text-secondary-600 hover:text-secondary-900">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">No members found.</td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-primary-600">
                        @if ($members->total() > 0)
                            Showing {{ $members->firstItem() }} to {{ $members->lastItem() }} of {{ $members->total() }}
                            results
                        @else
                            No results found.
                        @endif
                    </div>
                    <div class="flex space-x-2">
                        <x-pagination :paginator="$members" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-client.layout.app>
