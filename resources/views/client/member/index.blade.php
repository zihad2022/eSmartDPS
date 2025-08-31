<x-client.layout.app>
    @php
        // Get the status query parameter from the request (e.g., 'active', 'inactive')
        $status = request()->status;

        // Map status values to human-readable titles for the Members page
        $titleMap = [
            'active' => 'Active Members',
            'inactive' => 'Inactive Members',
        ];

        // Determine the page title based on the status filter, default to 'All Members'
        $pageTitle = $titleMap[$status] ?? 'All Members';

        // Base breadcrumb items: Dashboard > Members
        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('client.dashboard')],
            ['label' => 'Members', 'url' => route('client.members.index')],
        ];

        // Append specific status breadcrumb if a valid status is set
        if (isset($titleMap[$status])) {
            $breadcrumbItems[] = [
                'label' => $pageTitle,
                'url' => route('client.members.index', ['status' => $status]),
            ];
        }
    @endphp

    {{-- Set the HTML page title dynamically --}}
    <x-slot:title>{{ $pageTitle }}</x-slot:title>

    {{-- Render breadcrumb navigation --}}
    <x-breadcrumb :items="$breadcrumbItems" />

    {{-- Main Members Page Content --}}
    <div class="">
        {{-- Display flash messages if any --}}
        @if (session('success') || session('error'))
            <x-flash-message 
                :type="session('success') ? 'success' : 'error'" 
                :title="session('success') ? 'Success' : 'Error'" 
                :message="session('success') ?? session('error')" 
            />
        @endif

        {{-- Stats Cards Section --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 mb-6">
            <x-card.stat-card 
                label="Total Members" 
                :value="$totalMembers" 
                icon="fas fa-users" 
                iconBgColor="bg-accent-100"
                iconTextColor="text-accent-600" 
            />

            {{-- Active Members --}}
            <x-card.stat-card 
                label="Active Members" 
                :value="$activeMembers" 
                icon="fas fa-user-check" 
                iconBgColor="bg-green-100"
                iconTextColor="text-green-600" 
            />

            {{-- Inactive Members --}}
            <x-card.stat-card 
                label="Inactive Members" 
                :value="$inactiveMembers" 
                icon="fas fa-user-times"
                iconBgColor="bg-red-100" 
                iconTextColor="text-red-600" 
            />

            {{-- Total Shares --}}
            <x-card.stat-card 
                label="Total Shares" 
                :value="$totalShares" 
                icon="fas fa-chart-pie text-lg"
                iconBgColor="bg-secondary-100" 
                iconTextColor="text-secondary-600" 
            />
        </div>

        {{-- Members Table Section --}}
        <div class="bg-white rounded-xl shadow-sm">
            {{-- Table Header with Title and Buttons --}}
            <div class="p-6 border-b border-gray-200">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <h3 class="text-lg font-semibold text-primary-900 mb-4 md:mb-0">{{ $pageTitle }}</h3>

                    <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-4">
                        <a href="{{ route('client.members.create') }}"
                            class="bg-accent-500 hover:bg-accent-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-300">
                            Add Member
                        </a>
                        <a href="{{ route('client.members.export', ['status' => $status]) }}"
                            class="bg-gray-100 hover:bg-gray-200 text-primary-700 px-4 py-2 rounded-lg text-sm font-medium transition duration-300">
                            <i class="fas fa-download mr-2"></i>Export
                        </a>
                    </div>
                </div>
            </div>

            {{-- Table Body --}}
            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden">
                    {{-- Table Header --}}
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">SL</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">Member</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">Phone</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">Shares</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">Balance</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">Join Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>

                    {{-- Table Rows --}}
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($members as $index => $member)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">#{{ $index + 1 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if ($member->profile_photo)
                                            <img src="{{ $member->profile_photo_url }}" class="w-10 h-10 rounded-full mr-4" alt="Member Avatar">
                                        @else
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($member->name) }}&background=0D8ABC&color=fff" class="w-10 h-10 rounded-full mr-4" alt="Member Avatar">
                                        @endif
                                        <div>
                                            <div class="text-sm font-medium text-primary-900">{{ $member->name }}</div>
                                            <div class="text-sm text-primary-500">{{ $member->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-primary-600">{{ $member->phone ?: 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-primary-900">{{ number_format($member->share_quantity) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">${{ number_format($member->total_balance, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-primary-600">{{ $member->created_at->format('M d, Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($member->status)
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Active</span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('client.members.show', $member) }}" class="text-accent-600 hover:text-accent-900"><i class="fas fa-eye"></i></a>
                                        <a href="{{ route('client.members.edit', $member) }}" class="text-secondary-600 hover:text-secondary-900"><i class="fas fa-edit"></i></a>
                                        <form action="{{ route('client.members.destroy', $member) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="text-red-600 hover:text-red-900 delete-btn" title="Delete"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                    <x-confirm-modal />
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-primary-500">No members found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-primary-600">
                        @if ($members->total() > 0)
                            Showing {{ $members->firstItem() }} to {{ $members->lastItem() }} of {{ $members->total() }} results
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
