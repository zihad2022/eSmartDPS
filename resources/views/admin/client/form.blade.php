<x-admin.layout.app>
    @php
        $editing = isset($client);
        $title = $editing ? 'Edit Client' : 'Add New Client';
        $action = $editing ? route('admin.clients.update', $client->id) : route('admin.clients.store');
    @endphp

    <x-slot:title>{{ $title }}</x-slot:title>

    <x-breadcrumb :items="[
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'All Clients', 'url' => route('admin.clients.index')],
        ['label' => $title],
    ]" />

    <x-form-card :title="$title" :action="$action" :method="$editing ? 'PUT' : 'POST'" multipart>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- User ID --}}
            <x-form.input name="user_id" label="User ID" :value="$editing ? $client->user_id : $user_id" placeholder="System generated user ID" required
                :disabled="true" />

            {{-- Password --}}
            <x-form.password-input label="Password" name="password"
                placeholder="{{ $editing ? 'Leave blank to keep existing password' : 'Enter password' }}"
                :required="!$editing" />

            {{-- Status --}}
            <x-form.select name="status" label="Status" :options="['1' => 'Active', '0' => 'Inactive']" :selected="old('status', $client->status ?? '1')" required />
        </div>

        {{-- Name & Profile Info --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <x-form.input name="first_name" label="First Name" :value="old('first_name', $client->first_name ?? '')" placeholder="Enter first name"
                required />

            <x-form.input name="last_name" label="Last Name" :value="old('last_name', $client->last_name ?? '')" placeholder="Enter last name"
                required />

            <x-form.input name="profile_photo" label="Profile Photo" type="file" accept="image/*" :editing="$editing"
                :previewUrl="$client->profile_photo_url ?? null" />
        </div>

        {{-- Contact Info --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <x-form.input name="email" label="Email Address" type="email" :value="old('email', $client->email ?? '')"
                placeholder="Enter email address" required />

            <x-form.input name="phone" label="Phone Number" type="tel" :value="old('phone', $client->phone ?? '')"
                placeholder="Enter phone number" />

            <x-form.select name="package_id" label="Subscription Plan" :options="$packages->pluck('name', 'id')->toArray()" :selected="old('package_id', $editing ? $client->latestClientPackage?->package?->id : null)" required />
        </div>

        {{-- NID Info --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <x-form.input name="nid_number" label="NID Number" :value="old('nid_number', $client->nid_number ?? '')" placeholder="Enter NID number" />

            <x-form.input name="nid_card_front" label="NID Card Front" type="file" accept="image/*"
                :editing="$editing" :previewUrl="$client->nid_card_front_url ?? null" />

            <x-form.input name="nid_card_back" label="NID Card Back" type="file" accept="image/*" :editing="$editing"
                :previewUrl="$client->nid_card_back_url ?? null" />
        </div>

        {{-- Address Info --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <x-form.input name="division" label="Division" :value="old('division', $client->division ?? '')" placeholder="Enter division" />

            <x-form.input name="district" label="District" :value="old('district', $client->district ?? '')" placeholder="Enter district" />

            <x-form.input name="postal_code" label="Postal Code" :value="old('postal_code', $client->postal_code ?? '')" placeholder="Enter postal code" />
        </div>
        <x-form.textarea name="address" label="Address" :value="old('address', $client->address ?? '')" placeholder="Enter full address" />

        {{-- Footer Buttons --}}
        <x-slot:footer>
            <x-buttons.button variant="gray" :href="route('admin.clients.index')">Cancel</x-buttons.button>
            <x-buttons.button>{{ $editing ? 'Update Client' : 'Add Client' }}</x-buttons.button>
        </x-slot:footer>
    </x-form-card>
</x-admin.layout.app>
