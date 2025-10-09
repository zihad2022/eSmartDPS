<x-admin.layout.app>
    @php
        $editing = isset($client);
    @endphp

    {{-- Page Title --}}
    <x-slot:title>{{ $editing ? 'Edit Client' : 'Add New Client' }}</x-slot:title>

    {{-- Breadcrumb --}}
    <x-breadcrumb :items="[
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'All Clients', 'url' => route('admin.clients.index')],
        ['label' => $editing ? 'Edit Client' : 'Add New Client'],
    ]" />

    <div>
        <div class="bg-white rounded-2xl shadow-sm p-6 w-full mx-auto">
            <h2 class="text-xl font-semibold text-primary-900 mb-6">
                {{ $editing ? 'Edit Client' : 'Add New Client' }}
            </h2>

            <form method="POST"
                action="{{ $editing ? route('admin.clients.update', $client->id) : route('admin.clients.store') }}"
                enctype="multipart/form-data" class="space-y-8">
                @csrf
                @if ($editing)
                    @method('PUT')
                @endif

                {{-- ===========================
                     Column Grid (3 cols)
                ============================ --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                    {{-- User ID --}}
                    <x-form.input name="user_id" label="User ID" :value="$editing ? $client->user_id : $user_id" placeholder="System generated user ID"
                        required :disabled="true" />

                    {{-- Password --}}
                        <x-form.password-input label="Password" name="password"
                            placeholder="{{ $editing ? 'Leave blank to keep existing password' : 'Enter password' }}"
                            :required="!$editing" />

                    {{-- Status --}}
                    <x-form.select name="status" label="Status" :options="['1' => 'Active', '0' => 'Inactive']" :selected="old('status', $client->status ?? '1')" required />
                </div>

                {{-- ===========================
                     Name & Profile Info
                ============================ --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <x-form.input name="first_name" label="First Name" :value="old('first_name', $client->first_name ?? '')" placeholder="Enter first name"
                        required />

                    <x-form.input name="last_name" label="Last Name" :value="old('last_name', $client->last_name ?? '')" placeholder="Enter last name"
                        required />

                    <x-form.input name="profile_photo" label="Profile Photo" type="file" accept="image/*"
                        :editing="$editing" :previewUrl="$client->profile_photo_url ?? null" />
                </div>

                {{-- ===========================
                     Contact Info
                ============================ --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <x-form.input name="email" label="Email Address" type="email" :value="old('email', $client->email ?? '')"
                        placeholder="Enter email address" required />

                    <x-form.input name="phone" label="Phone Number" type="tel" :value="old('phone', $client->phone ?? '')"
                        placeholder="Enter phone number" />

                    <x-form.select name="package_id" label="Subscription Plan" :options="$packages->pluck('name', 'id')->toArray()" :selected="old('package_id', $editing ? $client->latestClientPackage?->package?->id : null)"
                        required />
                </div>

                {{-- ===========================
                     NID Info
                ============================ --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <x-form.input name="nid_number" label="NID Number" :value="old('nid_number', $client->nid_number ?? '')"
                        placeholder="Enter NID number" />

                    <x-form.input name="nid_card_front" label="NID Card Front" type="file" accept="image/*"
                        :editing="$editing" :previewUrl="$client->nid_card_front_url ?? null" />

                    <x-form.input name="nid_card_back" label="NID Card Back" type="file" accept="image/*"
                        :editing="$editing" :previewUrl="$client->nid_card_back_url ?? null" />
                </div>

                {{-- ===========================
                     Address Info
                ============================ --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <x-form.input name="division" label="Division" :value="old('division', $client->division ?? '')" placeholder="Enter division" />

                    <x-form.input name="district" label="District" :value="old('district', $client->district ?? '')" placeholder="Enter district" />

                    <x-form.input name="postal_code" label="Postal Code" :value="old('postal_code', $client->postal_code ?? '')"
                        placeholder="Enter postal code" />
                </div>

                {{-- Full Address (spans all columns) --}}
                <x-form.input name="address" label="Address" :value="old('address', $client->address ?? '')" placeholder="Enter full address" />

                {{-- ===========================
                     Actions
                ============================ --}}
                <div class="flex justify-end space-x-4 pt-4">
                    <a href="{{ route('admin.clients.index') }}"
                        class="px-4 py-2 border border-gray-300 text-primary-700 rounded-lg hover:bg-gray-50 text-sm transition duration-300">
                        Cancel
                    </a>
                    <button type="submit"
                        class="px-4 py-2 bg-accent-500 text-white text-sm rounded-lg hover:bg-accent-600 transition duration-300">
                        {{ $editing ? 'Update Client' : 'Add Client' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin.layout.app>
