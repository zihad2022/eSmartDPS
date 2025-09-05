<x-client.layout.app>
    @php
        $pageTitle = 'Member Details';
        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('client.dashboard')],
            ['label' => 'Members', 'url' => route('client.members.index')],
            ['label' => $pageTitle, 'url' => route('client.members.show', $member)],
        ];
    @endphp

    <x-slot:title>{{ $pageTitle }}</x-slot:title>
    <x-breadcrumb :items="$breadcrumbItems" />

    <div>
        <!-- Page Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
            <h1 class="text-2xl font-bold text-gray-800 mb-2 md:mb-0">{{ $pageTitle }}</h1>
        </div>

        <!-- Main Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-gray-50 border-l-4 border-green-500 p-4 rounded-lg shadow hover:shadow-lg transition">
                <p class="text-sm text-gray-500 font-medium">Member ID</p>
                <h3 class="text-lg font-semibold text-gray-800">{{ $member->member_id }}</h3>
            </div>
            <div class="bg-gray-50 border-l-4 border-green-500 p-4 rounded-lg shadow hover:shadow-lg transition">
                <p class="text-sm text-gray-500 font-medium">Name</p>
                <h3 class="text-lg font-semibold text-gray-800">{{ $member->name }}</h3>
            </div>
            <div class="bg-gray-50 border-l-4 border-green-500 p-4 rounded-lg shadow hover:shadow-lg transition">
                <p class="text-sm text-gray-500 font-medium">Email</p>
                <h3 class="text-lg font-semibold text-gray-800">{{ $member->email }}</h3>
            </div>
        </div>

        <!-- Details Section -->
        <div class="bg-white rounded-xl shadow p-6 mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Avatar -->
                <div class="bg-gray-50 p-4 rounded-lg shadow flex items-center space-x-4">
                    @if ($member->profile_photo)
                        <img src="{{ $member->profile_photo_url }}" alt="Avatar" class="w-20 h-20 rounded-full">
                    @else
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($member->name) }}&background=007BFF&color=fff"
                             alt="Avatar" class="w-20 h-20 rounded-full">
                    @endif
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Avatar</p>
                        <h3 class="text-lg font-semibold text-gray-800">{{ $member->name }}</h3>
                    </div>
                </div>

                <!-- Status -->
                <div class="bg-gray-50 p-4 rounded-lg shadow flex flex-col justify-center">
                    <p class="text-sm text-gray-500 font-medium">Status</p>
                    @if ($member->status)
                        <span class="inline-block px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Active</span>
                    @else
                        <span class="inline-block px-3 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">Inactive</span>
                    @endif
                </div>

                <!-- Phone -->
                <div class="bg-gray-50 p-4 rounded-lg shadow">
                    <p class="text-sm text-gray-500 font-medium">Phone</p>
                    <h3 class="text-lg font-semibold text-gray-800">{{ $member->phone ?? 'N/A' }}</h3>
                </div>

                <!-- Share Quantity -->
                <div class="bg-gray-50 p-4 rounded-lg shadow">
                    <p class="text-sm text-gray-500 font-medium">Share Quantity</p>
                    <h3 class="text-lg font-semibold text-gray-800">{{ number_format($member->share_quantity) }}</h3>
                </div>

                <!-- Total Balance -->
                <div class="bg-gray-50 p-4 rounded-lg shadow">
                    <p class="text-sm text-gray-500 font-medium">Total Balance</p>
                    <h3 class="text-lg font-semibold text-gray-800">${{ number_format($member->total_balance, 2) }}</h3>
                </div>

                <!-- Join Date -->
                <div class="bg-gray-50 p-4 rounded-lg shadow">
                    <p class="text-sm text-gray-500 font-medium">Join Date</p>
                    <h3 class="text-lg font-semibold text-gray-800">{{ $member->created_at->format('M d, Y') }}</h3>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('client.members.index') }}"
               class="px-4 py-2 border border-gray-400 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition">Back</a>
            <a href="{{ route('client.members.edit', $member) }}"
               class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium transition">Edit</a>
        </div>
    </div>
</x-client.layout.app>
