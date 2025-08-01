<x-admin.layout.app>
    <x-slot:title>Client Profile</x-slot:title>
    <x-breadcrumb :items="[
        ['label' => 'All Clients', 'url' => route('admin.clients.index')],
        ['label' => 'Client Profile', 'url' => '#'],
    ]"></x-breadcrumb>
    <div>
        <!-- Header -->
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-primary-900">Client Profile</h2>

        </div>

        <!-- Profile Card -->
        <div class="bg-white rounded-xl shadow-sm p-6 md:flex space-y-6 md:space-y-0 md:space-x-8">
            <div class="flex-shrink-0">
                <img src="{{ $client->profile_photo ? asset('storage/' . $client->profile_photo) : 'https://ui-avatars.com/api/?name=' . urlencode($client->first_name . ' ' . $client->last_name) }}"
                    alt="Profile Photo" class="w-32 h-32 rounded-full object-cover">
            </div>
            <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-6 text-sm text-primary-700">
                <div>
                    <p class="text-xs text-primary-500">User ID</p>
                    <p class="font-medium">{{ $client->user_id }}</p>
                </div>
                <div>
                    <p class="text-xs text-primary-500">Email Address</p>
                    <p class="font-medium">{{ $client->email }}</p>
                </div>
                <div>
                    <p class="text-xs text-primary-500">Full Name</p>
                    <p class="font-medium">{{ $client->first_name }} {{ $client->last_name }}</p>
                </div>
                <div>
                    <p class="text-xs text-primary-500">Phone Number</p>
                    <p class="font-medium">{{ $client->phone_number ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-primary-500">NID Number</p>
                    <p class="font-medium">{{ $client->nid_number ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-primary-500">Role</p>
                    <p class="font-medium capitalize">{{ $client->role }}</p>
                </div>
                <div>
                    <p class="text-xs text-primary-500">Status</p>
                    <p>
                        @if ($client->status)
                            <span
                                class="inline-block px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Active</span>
                        @else
                            <span
                                class="inline-block px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">Inactive</span>
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-xs text-primary-500">Subscription</p>
                    <p class="font-medium">
                        {{-- {{ optional($client->subscription)->name ?? '-' }} --}}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-primary-500">Location</p>
                    <p class="font-medium">
                        {{ $client->division ?? '-' }}, {{ $client->district ?? '-' }}<br>
                        {{ $client->address ?? '' }} {{ $client->postal_code ? ' - ' . $client->postal_code : '' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-primary-500">Joined On</p>
                    <p class="font-medium">{{ $client->created_at->format('M d, Y') }}</p>
                </div>
            </div>
        </div>

        <!-- NID Images -->
        @if ($client->nid_card_front || $client->nid_card_back)
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-primary-900 mb-4">NID Documents</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @if ($client->nid_card_front)
                        <div>
                            <p class="text-sm font-medium mb-2 text-primary-700">NID Front</p>
                            <img src="{{ asset('storage/' . $client->nid_card_front) }}" alt="NID Front"
                                class="w-full max-w-xs rounded shadow">
                        </div>
                    @endif
                    @if ($client->nid_card_back)
                        <div>
                            <p class="text-sm font-medium mb-2 text-primary-700">NID Back</p>
                            <img src="{{ asset('storage/' . $client->nid_card_back) }}" alt="NID Back"
                                class="w-full max-w-xs rounded shadow">
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</x-admin.layout.app>
