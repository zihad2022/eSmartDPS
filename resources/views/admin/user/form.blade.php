<x-admin.layout.app>
    @php
        $editing = isset($user);
        $title = $editing ? 'Edit User' : 'Add New User';

        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'All Users', 'url' => route('admin.users.index')],
            ['label' => $title, 'url' => '#'],
        ];

        // Only hide the status field for super-admin (ID = 1)
        $showStatus = !$editing || ($editing && $user->id !== 1);
    @endphp

    <x-slot:title>{{ $title }}</x-slot:title>
    <x-breadcrumb :items="$breadcrumbItems" />

    <x-form-card :title="$title" :action="$editing ? route('admin.users.update', $user->id) : route('admin.users.store')" :method="$editing ? 'PUT' : 'POST'" multipart>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">
            <x-form.input name="name" label="Full Name" :value="old('name', $user->name ?? '')" placeholder="Enter full name" required />

            <x-form.input name="username" label="Username" :value="old('username', $user->username ?? '')" placeholder="Enter username" :disabled="$editing"
                required />

            <x-form.input name="email" type="email" label="Email" :value="old('email', $user->email ?? '')" placeholder="Email address"
                :disabled="$editing" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">
            <x-form.input name="phone" type="tel" label="Phone" :value="old('phone', $user->phone ?? '')" placeholder="Phone number" />
            <div>
                <x-form.input name="password" type="password" label="Password" placeholder="Enter password"
                    :required="!$editing" />
                @if ($editing)
                    <p class="text-xs text-gray-500 mt-1">
                        Leave blank to keep the current password.
                    </p>
                @endif
            </div>

            <div>
                <x-form.label for="role">Role</x-form.label>
                <select name="role" id="role"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-accent-500">
                    @foreach ($roles as $role)
                        <option value="{{ $role->name }}"
                            {{ old('role', isset($user) && $user->roles->isNotEmpty() ? $user->roles->first()->name : '') == $role->name ? 'selected' : '' }}>
                            {{ ucfirst($role->name) }}
                        </option>
                    @endforeach
                </select>
                @error('role')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">
            {{-- Hide for super-admin --}}
            @if ($showStatus)
                <div>
                    <x-form.label for="status">Status</x-form.label>
                    <select name="status" id="status"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-accent-500">
                        <option value="1" {{ old('status', $user->status ?? '1') == '1' ? 'selected' : '' }}>Active
                        </option>
                        <option value="0" {{ old('status', $user->status ?? '1') == '0' ? 'selected' : '' }}>
                            Inactive</option>
                    </select>
                    @error('status')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            @endif
        </div>
        
        {{-- Actions --}}
        <x-slot:footer>
            <x-buttons.button variant="gray" href="{{ route('admin.users.index') }}">
                Cancel
            </x-buttons.button>

            <x-buttons.button>
                {{ $editing ? 'Update User' : 'Add User' }}
            </x-buttons.button>
        </x-slot:footer>
    </x-form-card>

</x-admin.layout.app>
