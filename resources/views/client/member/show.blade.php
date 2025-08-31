<x-client.layout.app>
    @php
        // Page is for viewing a single member
        $pageTitle = 'Member Details';

        // Breadcrumb items: Dashboard > Members > Member Details
        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('client.dashboard')],
            ['label' => 'Members', 'url' => route('client.members.index')],
            ['label' => $pageTitle, 'url' => route('client.members.show', $member)],
        ];
    @endphp

    {{-- Set HTML page title --}}
    <x-slot:title>{{ $pageTitle }}</x-slot:title>

    {{-- Render breadcrumb navigation --}}
    <x-breadcrumb :items="$breadcrumbItems" />

    <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-sm p-6">
        {{-- Member Heading --}}
        <div class="flex items-center space-x-4 mb-6">
            {{-- Member Avatar --}}
            @if ($member->profile_photo)
                <img src="{{ $member->profile_photo_url }}" alt="Member Avatar" class="w-20 h-20 rounded-full">
            @else
                <img src="https://ui-avatars.com/api/?name={{ urlencode($member->name) }}&background=0D8ABC&color=fff"
                    alt="Member Avatar" class="w-20 h-20 rounded-full">
            @endif

            {{-- Member Name and Status --}}
            <div>
                <h2 class="text-2xl font-semibold text-primary-900">{{ $member->name }}</h2>
                <p class="text-sm text-primary-500">{{ $member->email }}</p>

                {{-- Status Badge --}}
                @if ($member->status)
                    <span class="mt-1 inline-block px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Active</span>
                @else
                    <span class="mt-1 inline-block px-3 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">Inactive</span>
                @endif
            </div>
        </div>

        {{-- Member Details Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Member ID --}}
            <div>
                <p class="text-sm font-medium text-primary-500">Member ID</p>
                <p class="text-sm text-primary-900">{{ $member->member_id }}</p>
            </div>

            {{-- Phone Number --}}
            <div>
                <p class="text-sm font-medium text-primary-500">Phone</p>
                <p class="text-sm text-primary-900">{{ $member->phone ?? 'N/A' }}</p>
            </div>

            {{-- Share Quantity --}}
            <div>
                <p class="text-sm font-medium text-primary-500">Share Quantity</p>
                <p class="text-sm text-primary-900">{{ number_format($member->share_quantity) }}</p>
            </div>

            {{-- Total Balance --}}
            <div>
                <p class="text-sm font-medium text-primary-500">Total Balance</p>
                <p class="text-sm text-green-600 font-semibold">${{ number_format($member->total_balance, 2) }}</p>
            </div>

            {{-- Join Date --}}
            <div>
                <p class="text-sm font-medium text-primary-500">Join Date</p>
                <p class="text-sm text-primary-900">{{ $member->created_at->format('M d, Y') }}</p>
            </div>

            {{-- Status (repeated for clarity) --}}
            <div>
                <p class="text-sm font-medium text-primary-500">Status</p>
                <p class="text-sm text-primary-900">{{ $member->status ? 'Active' : 'Inactive' }}</p>
            </div>
        </div>
    </div>
</x-client.layout.app>
