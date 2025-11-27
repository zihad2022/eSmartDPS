<x-client.layout.app>

    @php
        $editing = isset($user);
        $pageTitle = $editing ? 'Edit User' : 'Add New User';

        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('client.dashboard')],
            ['label' => 'All Users', 'url' => route('client.users.index')],
            ['label' => $pageTitle],
        ];
    @endphp

    <x-breadcrumb :items="$breadcrumbItems" />
    <x-slot:title>{{ $pageTitle }}</x-slot:title>

    <div>

        {{-- Flash Message --}}
        @if (session('success') || session('error'))
            <x-flash-message :type="session('success') ? 'success' : 'error'" :title="session('success') ? 'Success' : 'Error'" :message="session('success') ?? session('error')" />
        @endif

        <div class="bg-white rounded-2xl shadow-sm p-8">

            <h2 class="text-2xl font-bold text-primary-900 mb-6">{{ $pageTitle }}</h2>

            {{-- Form --}}
            <form method="POST"
                action="{{ $editing ? route('client.users.update', $user->id) : route('client.users.store') }}"
                enctype="multipart/form-data" class="space-y-10">

                @csrf
                @if ($editing)
                    @method('PUT')
                @endif

                {{-- Personal Information --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-form.input name="first_name" label="First Name" :value="old('first_name', $user->first_name ?? '')" required />

                    <x-form.input name="last_name" label="Last Name" :value="old('last_name', $user->last_name ?? '')" required />
                </div>

                {{-- Profile Photo --}}
                <x-form.input name="profile_photo" type="file" label="Profile Photo" accept="image/*"
                    :previewUrl="$user->profile_photo ?? null" />

                {{-- Authentication --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- User ID (auto-generated, readonly) --}}
                    <x-form.input name="user_id" label="User ID" :value="$editing ? $user->user_id : $user_id" disabled />

                    {{-- Password --}}
                    <div>
                        <x-form.input name="password" type="password" label="Password" :required="!$editing"
                            placeholder="{{ $editing ? 'Leave blank to keep existing password' : 'Enter password' }}" />

                        @if ($editing)
                            <p class="text-xs text-gray-500 mt-1">Leave blank to keep existing password.</p>
                        @endif
                    </div>
                </div>

                {{-- Contact Information --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-form.input name="email" type="email" label="Email" :value="old('email', $user->email ?? '')" :disabled="$editing" />

                    <x-form.input name="phone" label="Phone" :value="old('phone', $user->phone ?? '')" />
                </div>

                {{-- NID Information --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-form.input name="nid_number" label="NID Number" :value="old('nid_number', $user->nid_number ?? '')" />

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-form.input name="nid_card_front" type="file" label="NID Front" accept="image/*"
                            :previewUrl="$user->nid_card_front ?? null" />

                        <x-form.input name="nid_card_back" type="file" label="NID Back" accept="image/*"
                            :previewUrl="$user->nid_card_back ?? null" />
                    </div>
                </div>

                {{-- Location Information --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-form.input name="division" label="Division" :value="old('division', $user->division ?? '')" />

                    <x-form.input name="district" label="District" :value="old('district', $user->district ?? '')" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-form.input name="address" label="Address" :value="old('address', $user->address ?? '')" />

                    <x-form.input name="postal_code" label="Postal Code" :value="old('postal_code', $user->postal_code ?? '')" />
                </div>

                {{-- Role & Status --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- Role --}}
                    <div>
                        <x-form.label for="role">Role</x-form.label>

                        @if ($editing && $user->role === 'super_admin')
                            <input type="text" value="Super Admin" disabled
                                class="w-full px-4 py-2 border rounded-lg bg-gray-100 text-sm">
                        @else
                            <select name="role" id="role"
                                class="w-full px-4 py-2 border rounded-lg text-sm focus:ring-accent-500">
                                @foreach (['admin', 'manager', 'editor'] as $role)
                                    <option value="{{ $role }}"
                                        {{ old('role', $user->role ?? 'manager') == $role ? 'selected' : '' }}>
                                        {{ ucfirst($role) }}
                                    </option>
                                @endforeach
                            </select>
                        @endif

                        @error('role')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Status --}}
                    <div>
                        <x-form.label for="status">Status</x-form.label>

                        <select name="status" id="status"
                            class="w-full px-4 py-2 border rounded-lg text-sm focus:ring-accent-500">
                            <option value="1" {{ old('status', $user->status ?? 1) == 1 ? 'selected' : '' }}>Active
                            </option>
                            <option value="0" {{ old('status', $user->status ?? 1) == 0 ? 'selected' : '' }}>
                                Inactive</option>
                        </select>

                        @error('status')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('client.users.index') }}"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-primary-700 hover:bg-gray-50 text-sm">
                        Cancel
                    </a>

                    <button type="submit"
                        class="px-4 py-2 bg-accent-500 text-white rounded-lg text-sm hover:bg-accent-600">
                        {{ $editing ? 'Update User' : 'Add User' }}
                    </button>
                </div>

            </form>

        </div>
    </div>

</x-client.layout.app>
