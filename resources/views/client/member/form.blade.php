<x-client.layout.app>
    @php
        $editing = isset($member);
        $pageTitle = $editing ? 'Edit Member' : 'Add New Member';
        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('client.dashboard')],
            ['label' => 'Members', 'url' => route('client.members.index')],
            [
                'label' => $pageTitle,
                'url' => $editing ? route('client.members.edit', $member) : route('client.members.create'),
            ],
        ];
    @endphp

    <x-slot:title>{{ $pageTitle }}</x-slot:title>

    <x-breadcrumb :items="$breadcrumbItems" />

    <div>
        @if (session('success') || session('error'))
            <x-flash-message :type="session('success') ? 'success' : 'error'" :title="session('success') ? 'Success' : 'Error'" :message="session('success') ?? session('error')" />
        @endif

        <div class="bg-white rounded-2xl shadow-sm p-6 w-full mx-auto">
            <h2 class="text-xl font-semibold text-primary-900 mb-6">{{ $pageTitle }}</h2>

            <form method="POST"
                action="{{ $editing ? route('client.members.update', $member->id) : route('client.members.store') }}"
                class="space-y-8" enctype="multipart/form-data">
                @csrf
                @if ($editing)
                    @method('PUT')
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-form.input name="member_id" label="Member ID" :value="$memberId ?? $member->member_id" required
                        placeholder="Enter member ID" :disabled="true" />
                    <x-form.input name="name" label="Full Name" :value="old('name', $member->name ?? '')" required
                        placeholder="Enter full name" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <x-form.input name="email" label="Email" type="email" :value="old('email', $member->email ?? '')"
                        placeholder="Enter email address" required />
                    <x-form.input name="phone" label="Phone" type="tel" :value="old('phone', $member->phone ?? '')"
                        placeholder="Enter phone number" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <x-form.password-input label="Password" name="password"
                        placeholder="{{ $editing ? 'Leave blank to keep existing password' : 'Enter password' }}"
                        :required="!$editing" />
                    <x-form.select name="status" label="Status" :options="['1' => 'Active', '0' => 'Inactive']" :selected="old('status', $member->status ?? '1')" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <x-form.input name="share_quantity" label="Share Quantity" type="number" min="0"
                        :value="old('share_quantity', $member->share_quantity ?? '')" placeholder="Enter share quantity" />
                    <x-form.input name="profile_photo" label="Profile Photo" type="file" :previewUrl="$member->profile_photo_url ?? null" />
                </div>

                <div class="flex justify-end space-x-4 pt-4">
                    <x-buttons.button variant="gray" :href="route('client.members.index')">Cancel</x-buttons.button>
                    <x-buttons.button>{{ $editing ? 'Update Member' : 'Add Member' }}</x-buttons.button>
                </div>
            </form>
        </div>
    </div>
</x-client.layout.app>
