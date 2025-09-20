<x-client.layout.app>
    @php
         // ✅ Determine if we are editing an existing user or creating a new one
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
    <div class="">
        {{-- ✅ Flash Messages (Success or Error) --}}
        @if (session('success') || session('error'))
            <x-flash-message :type="session('success') ? 'success' : 'error'" :title="session('success') ? 'Success' : 'Error'" :message="session('success') ?? session('error')" />
        @endif

        <div class="bg-white rounded-2xl shadow-sm p-8">
           

            <h2 class="text-2xl font-bold text-primary-900 mb-6">
                {{ $editing ? 'Edit User' : 'Add New User' }}
            </h2>

            {{-- ✅ Form (Create or Update) --}}
            <form method="POST"
                action="{{ $editing ? route('client.users.update', $user->id) : route('client.users.store') }}"
                enctype="multipart/form-data" {{-- ✅ Needed for file uploads like profile photo --}} class="space-y-8">

                @csrf
                @if ($editing)
                    {{-- ✅ Use PUT method for updating --}}
                    @method('PUT')
                @endif

                {{-- 🔹 Personal Info --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-form.input name="first_name" label="First Name" :value="old('first_name', $user->first_name ?? '')" required
                        placeholder="Enter first name" />

                    <x-form.input name="last_name" label="Last Name" :value="old('last_name', $user->last_name ?? '')" required
                        placeholder="Enter last name" />
                </div>

                {{-- 🔹 Profile Photo Upload --}}
                <x-form.input name="profile_photo" label="Profile Photo" type="file" accept="image/*"
                    :editing="$editing" :previewUrl="$user->profile_photo_url ?? null" />

                {{-- 🔹 Authentication (User ID & Password) --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- User ID (readonly) --}}
                    <x-form.input name="user_id" label="User ID" :value="$editing ? $user->user_id : $user_id" required
                        placeholder="System generated user ID" :disabled="true" />

                    {{-- Password --}}
                    <div>
                        <x-form.input name="password" label="Password" type="password" placeholder="Enter password"
                            :required="!$editing" />
                        @if ($editing)
                            <p class="text-xs text-gray-500 mt-1">
                                Leave blank to keep existing password.
                            </p>
                        @endif
                    </div>
                </div>

                {{-- 🔹 Contact Info --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-form.input name="email" label="Email" type="email" :value="old('email', $user->email ?? '')"
                        placeholder="Enter email address" :disabled="$editing" {{-- ✅ Prevent changing email on edit --}} />

                    <x-form.input name="phone" label="Phone" type="tel" :value="old('phone', $user->phone ?? '')"
                        placeholder="Enter phone number" />
                </div>

                {{-- 🔹 Role & Status --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Role --}}
                    <div>
                        <x-form.label for="role">Role</x-form.label>

                        @if ($editing)
                            {{-- Editing mode --}}
                            @if ($user->role === 'super_admin')
                                {{-- Show only text if user is super_admin --}}
                                <input type="text" value="{{ ucfirst($user->role) }}" disabled
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm bg-gray-100">
                            @else
                                {{-- Editable select for other roles --}}
                                <select name="role" id="role"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-accent-500">
                                    @foreach (['admin', 'manager', 'editor'] as $role)
                                        <option value="{{ $role }}"
                                            {{ old('role', $user->role) == $role ? 'selected' : '' }}>
                                            {{ ucfirst($role) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            @endif
                        @else
                            {{-- Creating new user always show select --}}
                            <select name="role" id="role"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-accent-500">
                                @foreach (['admin', 'manager', 'editor'] as $role)
                                    <option value="{{ $role }}"
                                        {{ old('role', 'manager') == $role ? 'selected' : '' }}>
                                        {{ ucfirst($role) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>

                    {{-- Status --}}
                    <div>
                        <x-form.label for="status">Status</x-form.label>
                        <select name="status" id="status"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-accent-500">
                            <option value="1" {{ old('status', $user->status ?? 1) == 1 ? 'selected' : '' }}>
                                Active</option>
                            <option value="0" {{ old('status', $user->status ?? 1) == 0 ? 'selected' : '' }}>
                                Inactive</option>
                        </select>
                        @error('status')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- 🔹 Actions --}}
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
