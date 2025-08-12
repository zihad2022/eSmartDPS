<x-admin.layout.app>
    @php
        // Check if we are editing an existing client or adding a new one
        $editing = isset($client);
    @endphp

    {{-- Set dynamic page title based on editing or creating --}}
    @if ($editing)
        <x-slot:title>Edit Client</x-slot:title>
    @else
        <x-slot:title>Add New Client</x-slot:title>
    @endif

    {{-- Breadcrumb navigation to help users understand their location --}}
    <x-breadcrumb :items="[
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'All Clients', 'url' => route('admin.clients.index')],
        ['label' => $editing ? 'Edit Client' : 'Add New Client'],
    ]" />

    <div>
        {{-- Main container card for form --}}
        <div class="bg-white rounded-2xl shadow-sm p-6 w-full mx-auto">

            {{-- Form heading --}}
            <h2 class="text-xl font-semibold text-primary-900 mb-6">
                {{ $editing ? 'Edit Client' : 'Add New Client' }}
            </h2>

            {{-- 
                Client form.
                Method: POST (with PUT override if editing)
                Action: Updates or creates a client depending on context
                enctype: multipart/form-data for file uploads (profile photo, NID images)
            --}}
            <form method="POST"
                action="{{ $editing ? route('admin.clients.update', $client->id) : route('admin.clients.store') }}"
                enctype="multipart/form-data" class="space-y-8">
                @csrf

                {{-- Use PUT method if editing --}}
                @if ($editing)
                    @method('PUT')
                @endif

                {{-- 
                    User ID and Password inputs.
                    User ID is shown but disabled (system generated).
                    Password is required only on create, optional on edit.
                --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-form.input name="user_id" label="User ID" :value="$editing ? $client->user_id : $user_id" required
                        placeholder="System generated user ID" :disabled="true" />
                    <x-form.input name="password" label="Password" type="password"
                        placeholder="{{ $editing ? 'Leave blank to keep existing password' : 'Enter password' }}"
                        :required="!$editing" />
                </div>

                {{-- First and Last Name --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-form.input name="first_name" label="First Name" :value="old('first_name', $client->first_name ?? '')" required
                        placeholder="Enter first name" />
                    <x-form.input name="last_name" label="Last Name" :value="old('last_name', $client->last_name ?? '')" required
                        placeholder="Enter last name" />
                </div>

                {{-- Profile photo upload with preview --}}
                <div>
                    <x-form.input name="profile_photo" label="Profile Photo" type="file" accept="image/*"
                        :editing="$editing" :previewUrl="$client->profile_photo_url ?? null" />
                </div>

                {{-- Email and Phone Number --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-form.input name="email" label="Email Address" type="email" required :value="old('email', $client->email ?? '')"
                        placeholder="Enter email address" />
                    <x-form.input name="phone_number" label="Phone Number" type="tel" :value="old('phone_number', $client->phone_number ?? '')"
                        placeholder="Enter phone number" />
                </div>

                {{-- NID Number --}}
                <x-form.input name="nid_number" label="NID Number" :value="old('nid_number', $client->nid_number ?? '')" placeholder="Enter NID number" />

                {{-- NID Card Front and Back upload with preview --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-form.input name="nid_card_front" label="NID Card Front" type="file" accept="image/*"
                        :editing="$editing" :previewUrl="$client->nid_card_front_url ?? null" />
                    <x-form.input name="nid_card_back" label="NID Card Back" type="file" accept="image/*"
                        :editing="$editing" :previewUrl="$client->nid_card_back_url ?? null" />
                </div>

                {{-- Division and District --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-form.input name="division" label="Division" :value="old('division', $client->division ?? '')" placeholder="Enter division" />
                    <x-form.input name="district" label="District" :value="old('district', $client->district ?? '')" placeholder="Enter district" />
                </div>

                {{-- Address --}}
                <x-form.input name="address" label="Address" :value="old('address', $client->address ?? '')" placeholder="Enter address" />

                {{-- Postal Code --}}
                <x-form.input name="postal_code" label="Postal Code" :value="old('postal_code', $client->postal_code ?? '')"
                    placeholder="Enter postal code" />

                {{-- Status and Subscription Plan (package) --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-form.select name="status" label="Status" :options="['1' => 'Active', '0' => 'Inactive']" :selected="old('status', $client->status ?? '1')" required />

                    <x-form.select name="package_id" label="Subscription Plan" :options="$packages->pluck('name', 'id')" :selected="old('package_id', $client->ClientPackage->package->id ?? '')"
                        required :isOptionLabel="true" optionLabel="Select Subscription" />
                </div>

                {{-- =================== Form Actions ===================
                     Buttons to cancel (go back) or submit (create/update client)
                --}}
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
