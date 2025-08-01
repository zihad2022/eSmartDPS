<x-client.layout.app>
    <div class="">
        <div class="bg-white rounded-2xl shadow-sm p-8">
            @php $editing = isset($user); @endphp

            <h2 class="text-2xl font-bold text-primary-900 mb-6">
                {{ $editing ? 'Edit User' : 'Add New User' }}
            </h2>

            <form method="POST"
                action="{{ $editing ? route('client.users.update', $user->id) : route('client.users.store') }}"
                enctype="multipart/form-data" {{-- Required for profile photo & NID uploads --}} class="space-y-8">
                @csrf
                @if ($editing)
                    @method('PUT')
                @endif

                {{-- Personal Info --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-form.input name="first_name" label="First Name" :value="old('first_name', $user->first_name ?? '')" required
                        placeholder="Enter first name" />

                    <x-form.input name="last_name" label="Last Name" :value="old('last_name', $user->last_name ?? '')" required
                        placeholder="Enter last name" />
                </div>

                {{-- Profile Photo --}}
                <x-form.input name="profile_photo" label="Profile Photo" type="file" accept="image/*"
                    :editing="$editing" :previewUrl="$user->profile_photo_url ?? null" />

                {{-- Authentication --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-form.input name="user_id" label="User ID" :value="isset($editing) && $editing ? $user->user_id : $user_id" required
                        placeholder="System generated user ID" :disabled="true" />

                    <div>
                        <x-form.input name="password" label="Password" type="password" placeholder="Enter password"
                            :required="!$editing" />
                        @if ($editing)
                            <p class="text-xs text-gray-500 mt-1">Leave blank to keep existing password.</p>
                        @endif
                    </div>
                </div>

                {{-- Contact Info --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-form.input name="email" label="Email" type="email" :value="old('email', $user->email ?? '')"
                        placeholder="Enter email address" :disabled="$editing" />

                    <x-form.input name="phone" label="Phone" type="tel" :value="old('phone', $user->phone ?? '')"
                        placeholder="Enter phone number" />
                </div>
                {{-- Location --}}
                {{-- <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-form.input name="division" label="Division" :value="old('division', $user->division ?? '')" placeholder="Enter division" />

                    <x-form.input name="district" label="District" :value="old('district', $user->district ?? '')" placeholder="Enter district" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-form.input name="address" label="Address" :value="old('address', $user->address ?? '')" placeholder="Enter address" />

                    <x-form.input name="postal_code" label="Postal Code" :value="old('postal_code', $user->postal_code ?? '')"
                        placeholder="Enter postal code" />
                </div> --}}

                {{-- Role & Status --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-form.label for="role">Role</x-form.label>
                        <select name="role" id="role"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-accent-500">
                            @foreach (['admin', 'manager', 'editor'] as $role)
                                <option value="{{ $role }}"
                                    {{ old('role', $user->role ?? 'manager') == $role ? 'selected' : '' }}>
                                    {{ ucfirst($role) }}
                                </option>
                            @endforeach
                        </select>
                        @error('role')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <x-form.label for="status">Status</x-form.label>
                        <select name="status" id="status"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-accent-500">
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

                {{-- Actions --}}
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('client.users.index') }}"
                        class="px-4 py-2 border border-gray-300 text-primary-700 rounded-lg hover:bg-gray-50 text-sm transition">
                        Cancel
                    </a>

                    <button type="submit"
                        class="px-4 py-2 bg-accent-500 text-white text-sm rounded-lg hover:bg-accent-600 transition">
                        {{ $editing ? 'Update User' : 'Add User' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-client.layout.app>
