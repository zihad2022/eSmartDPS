<x-admin.layout.app>
    @php
        /**
         * =======================================
         * Page Setup: Title, Breadcrumbs
         * =======================================
         */

        // 1. Define the page title
        $pageTitle = 'Client Profile';

        // 2. Breadcrumb items
        $breadcrumbItems = [
            ['label' => 'All Clients', 'url' => route('admin.clients.index')],
            ['label' => 'Client Profile', 'url' => '#'],
        ];
    @endphp

    {{-- ===========================
         Set HTML Page Title
    ============================ --}}
    <x-slot:title>{{ $pageTitle }}</x-slot:title>

    {{-- ===========================
         Breadcrumb Navigation
    ============================ --}}
    <x-breadcrumb :items="$breadcrumbItems" />

    {{-- ===========================
         Main Content Wrapper
    ============================ --}}
    <div class="space-y-6">

        {{-- ===========================
             Profile Header
        ============================ --}}
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-primary-900">{{ $pageTitle }}</h2>
        </div>

        {{-- ===========================
             Profile Card Section
        ============================ --}}
        <div class="bg-white rounded-xl shadow-sm p-6 md:flex space-y-6 md:space-y-0 md:space-x-8">

            {{-- Avatar --}}
            <div class="flex-shrink-0">
                @php
                    $avatarUrl = $client->profile_photo_url ?? ('https://ui-avatars.com/api/?name=' . urlencode($client->first_name . ' ' . $client->last_name));
                @endphp
                <img src="{{ $avatarUrl }}" alt="Profile Photo" class="w-32 h-32 rounded-full object-cover">
            </div>

            {{-- Client Details --}}
            <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-6 text-sm text-primary-700">

                <x-display.field label="User ID" :value="$client->user_id" />
                <x-display.field label="Email Address" :value="$client->email" />
                <x-display.field label="Full Name" :value="$client->first_name . ' ' . $client->last_name" />
                <x-display.field label="Phone" :value="$client->phone ?? '-'" />
                <x-display.field label="Role" :value="ucfirst($client->role)" />

                {{-- Status --}}
                <div>
                    <p class="text-xs text-primary-500">Status</p>
                    <p>
                        @if ($client->status)
                            <span class="inline-block px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Active</span>
                        @else
                            <span class="inline-block px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">Inactive</span>
                        @endif
                    </p>
                </div>

                {{-- Location --}}
                <div>
                    <p class="text-xs text-primary-500">Location</p>
                    <p class="font-medium">
                        {{ $client->division ?? '-' }}, {{ $client->district ?? '-' }}<br>
                        {{ $client->address ?? '' }}{{ $client->postal_code ? ' - ' . $client->postal_code : '' }}
                    </p>
                </div>

                <x-display.field label="Joined On" :value="$client->created_at->format('M d, Y')" />
            </div>
        </div>

        {{-- ===========================
             🔹 NID Documents Section
        ============================ --}}
        @if ($client->nid_card_front || $client->nid_card_back)
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-primary-900 mb-4">NID Documents</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- NID Front --}}
                    @if ($client->nid_card_front)
                        <div>
                            <p class="text-sm font-medium mb-2 text-primary-700">NID Front</p>
                            <img src="{{ $client->nid_card_front_url }}" 
                                 alt="NID Front" class="w-full max-w-xs rounded shadow">
                        </div>
                    @endif

                    {{-- NID Back --}}
                    @if ($client->nid_card_back)
                        <div>
                            <p class="text-sm font-medium mb-2 text-primary-700">NID Back</p>
                            <img src="{{ $client->nid_card_back_url }}" 
                                 alt="NID Back" class="w-full max-w-xs rounded shadow">
                        </div>
                    @endif

                </div>
            </div>
        @endif

    </div> {{-- End Main Content --}}
</x-admin.layout.app>
