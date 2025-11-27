<x-client.layout.app>
    <x-breadcrumb :items="[
        ['label' => 'Dashboard', 'url' => route('client.dashboard')],
        ['label' => 'Profile', 'url' => route('client.profile.edit')],
    ]" />

    <div>
        {{-- Flash Messages --}}
        @if (session('success') || session('error'))
            <x-flash-message :type="session('success') ? 'success' : 'error'" :title="session('success') ? 'Success' : 'Error'" :message="session('success') ?? session('error')" />
        @endif

        <div class="bg-white rounded-2xl shadow-sm p-8 mx-auto">
            <h2 class="text-2xl font-bold text-primary-900 mb-6">
                Update Profile
            </h2>

            <form method="POST" action="{{ route('client.profile.update') }}" enctype="multipart/form-data"
                class="space-y-8">
                @csrf
                @method('PUT')

                {{-- Profile Photo --}}
                <div class="flex items-center space-x-6">
                    <div class="w-20 h-20 rounded-full overflow-hidden bg-gray-100">
                        <img src="{{ $client->profile_photo
                            ? asset('storage/' . $client->profile_photo)
                            : 'https://ui-avatars.com/api/?name=' . urlencode($client->first_name . ' ' . $client->last_name) }}"
                            alt="Profile Photo" class="w-full h-full object-cover">
                    </div>
                    <div>
                        <x-form.label for="profile_photo">Profile Photo</x-form.label>
                        <input type="file" name="profile_photo" id="profile_photo"
                            class="block w-full text-sm text-gray-700 border border-gray-300 rounded-lg p-2">
                        <p class="text-xs text-gray-500 mt-1">Upload JPG/PNG (max 2MB)</p>
                    </div>
                </div>

                {{-- Personal Information --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <x-form.input name="first_name" label="First Name" :value="old('first_name', $client->first_name)" required />
                    <x-form.input name="last_name" label="Last Name" :value="old('last_name', $client->last_name)" required />
                    <x-form.input name="phone" label="Phone Number" type="tel" :value="old('phone', $client->phone)" />
                </div>

                {{-- Login & Security --}}
               <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

    <x-form.input name="email" label="Email" type="email" :value="$client->email" disabled />

    <x-form.input name="user_id" label="User ID" :value="$client->user_id" disabled />

    <!-- Current Password -->
    <div>
        <x-form.input
            name="current_password"
            label="Current Password"
            type="password"
            placeholder="Enter current password"
        />
        <p class="text-xs text-gray-500 mt-1">Required only when changing your password.</p>
    </div>

    <!-- New Password -->
    <div>
        <x-form.input
            name="new_password"
            label="New Password"
            type="password"
            placeholder="Enter new password"
        />
        <p class="text-xs text-gray-500 mt-1">Leave empty if you don't want to change your password.</p>
    </div>

</div>


                {{-- Role (Disabled) --}}
                <div>
                    <x-form.label>Role</x-form.label>
                    <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-sm"
                        value="{{ ucfirst($client->role) }}" disabled>
                </div>

                {{-- NID Information --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <x-form.input name="nid_number" label="NID Number" :value="old('nid_number', $client->nid_number)" />
                    <x-form.input name="nid_card_front" type="file" label="NID Card Front" />
                    <x-form.input name="nid_card_back" type="file" label="NID Card Back" />
                </div>

                {{-- Location --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <x-form.input name="division" label="Division" :value="old('division', $client->division)" />
                    <x-form.input name="district" label="District" :value="old('district', $client->district)" />
                    <x-form.input name="postal_code" label="Postal Code" :value="old('postal_code', $client->postal_code)" />
                </div>
                <div>
                    <x-form.input name="address" label="Address" :value="old('address', $client->address)" />
                </div>

                {{-- Submit --}}
                <div class="flex justify-end">
                    <x-button type="submit" color="accent">Update Profile</x-button>
                </div>
            </form>
        </div>
    </div>
</x-client.layout.app>
