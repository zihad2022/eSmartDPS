<x-client.layout.app>
    @php
        // Check if editing an existing member or adding a new one
        $editing = isset($member);

        // Set page title dynamically
        $pageTitle = $editing ? 'Edit Member' : 'Add New Member';

        // Breadcrumb items: Dashboard > Members > Current Page
        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('client.dashboard')],
            ['label' => 'Members', 'url' => route('client.members.index')],
            [
                'label' => $pageTitle,
                'url' => $editing ? route('client.members.edit', $member) : route('client.members.create'),
            ],
        ];
    @endphp

    {{-- Set HTML page title --}}
    <x-slot:title>{{ $pageTitle }}</x-slot:title>

    {{-- Render breadcrumb navigation --}}
    <x-breadcrumb :items="$breadcrumbItems" />

    <div class="">
        {{-- Display flash messages if any --}}
        @if (session('success') || session('error'))
            <x-flash-message :type="session('success') ? 'success' : 'error'" :title="session('success') ? 'Success' : 'Error'" :message="session('success') ?? session('error')" />
        @endif

        {{-- Form Container --}}
        <div class="bg-white rounded-2xl shadow-sm p-6 w-full mx-auto">
            {{-- Page Heading --}}
            <h2 class="text-xl font-semibold text-primary-900 mb-6">{{ $pageTitle }}</h2>

            {{-- Member Form --}}
            <form method="POST"
                action="{{ $editing ? route('client.members.update', $member->id) : route('client.members.store') }}"
                class="space-y-8" enctype="multipart/form-data">
                @csrf
                @if ($editing)
                    @method('PUT') {{-- Use PUT method if editing --}}
                @endif

                {{-- First Row: Member ID & Name --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-form.input name="member_id" label="Member ID" :value="$memberId ?? $member->member_id" required
                        placeholder="Enter member ID" :disabled="true" />

                    <x-form.input name="name" label="Full Name" :value="old('name', $member->name ?? '')" required
                        placeholder="Enter full name" />
                </div>

                {{-- Second Row: Email & Phone --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <x-form.input name="email" label="Email" type="email" :value="old('email', $member->email ?? '')"
                        placeholder="Enter email address" required />
                    <x-form.input name="phone" label="Phone" type="tel" :value="old('phone', $member->phone ?? '')"
                        placeholder="Enter phone number" />
                </div>

                {{-- Third Row: Password & Status --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <div>
                        <x-form.input name="password" label="Password" type="password" placeholder="Enter password"
                            :required="!$editing" />
                        @if ($editing)
                            {{-- Inform user they can leave password blank to keep existing --}}
                            <p class="text-xs text-gray-500 mt-1">Leave blank to keep existing password.</p>
                        @endif
                    </div>

                    <x-form.select name="status" label="Status" :options="['1' => 'Active', '0' => 'Inactive']" :selected="old('status', $member->status ?? '1')" />
                </div>

                {{-- Fourth Row: Share Quantity & Profile Photo --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <x-form.input name="share_quantity" label="Share Quantity" type="number" min="0"
                        :value="old('share_quantity', $member->share_quantity ?? '')" placeholder="Enter share quantity" />

                    <x-form.input name="profile_photo" label="Profile Photo" type="file" :previewUrl="$member->profile_photo_url ?? null"/>
                </div>

                {{-- ACTION BUTTONS --}}
                <div class="flex justify-end space-x-4 pt-4">
                    {{-- Cancel Button --}}
                    <a href="{{ route('client.members.index') }}"
                        class="px-4 py-2 border border-gray-300 text-primary-700 rounded-lg hover:bg-gray-50 text-sm transition duration-300">
                        Cancel
                    </a>

                    {{-- Submit Button --}}
                    <button type="submit"
                        class="px-4 py-2 bg-accent-500 text-white text-sm rounded-lg hover:bg-accent-600 transition duration-300">
                        {{ $editing ? 'Update Member' : 'Add Member' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-client.layout.app>
