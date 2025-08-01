<x-admin.layout.app>
    <x-breadcrumb :items="[
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Profile', 'url' => route('admin.profile.edit')],
    ]" />
    <div>
        @if (session('success') || session('error'))
            <x-flash-message :type="session('success') ? 'success' : 'error'" :title="session('success') ? 'Success' : 'Error'" :message="session('success') ?? session('error')" />
        @endif
        <div class="bg-white rounded-2xl shadow-sm p-8  mx-auto">
            @php $editing = isset($user); @endphp

            <h2 class="text-2xl font-bold text-primary-900 mb-6">
                Update Profile
            </h2>

            <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data"
                class="space-y-8">
                @csrf
                @method('PUT')

                {{-- Profile Photo --}}
                <div class="flex items-center space-x-6">
                    <div class="w-20 h-20 rounded-full overflow-hidden bg-gray-100">
                        <img src="{{ $user->profile_photo_url
                            ? $user->profile_photo_url
                            : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) }}"
                            alt="Profile Photo" class="w-full h-full object-cover">
                    </div>
                    <div>
                        <x-form.label for="profile_photo">Profile Photo</x-form.label>
                        <input type="file" name="profile_photo" id="profile_photo"
                            class="block w-full text-sm text-gray-700 border border-gray-300 rounded-lg p-2">
                        <p class="text-xs text-gray-500 mt-1">Upload JPG/PNG (max 2MB)</p>
                    </div>
                </div>

                {{-- Name & Phone --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-form.input name="name" label="Full Name" :value="old('name', $user->name)" required />

                    <x-form.input name="phone" label="Phone Number" type="tel" :value="old('phone', $user->phone)" required />
                </div>

                {{-- Email & Username (Disabled) --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-form.input name="email" label="Email" type="email" :value="$user->email" disabled />

                    <x-form.input name="username" label="Username" :value="$user->username" disabled />
                </div>

                {{-- Role (Disabled for normal users) --}}
                <div>
                    <x-form.label>Role</x-form.label>
                    <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-sm"
                        value="{{ ucfirst($user->role) }}" disabled>
                </div>

                {{-- Password (Optional) --}}
                <div>
                    <x-form.input name="password" label="New Password" type="password"
                        placeholder="Leave blank to keep current password" />
                    <p class="text-xs text-gray-500 mt-1">Leave empty if you don't want to change password.</p>
                </div>

                {{-- Status (Only editable by admins) --}}
                @if (auth('admin')->user()->role === 'admin')
                    <div>
                        <x-form.label for="status">Status</x-form.label>
                        <select name="status" id="status"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm">
                            <option value="1" {{ old('status', $user->status) == 1 ? 'selected' : '' }}>Active
                            </option>
                            <option value="0" {{ old('status', $user->status) == 0 ? 'selected' : '' }}>Inactive
                            </option>
                        </select>
                    </div>
                @endif

                {{-- Submit --}}
                <div class="flex justify-end space-x-4">
                    <button type="submit"
                        class="px-6 py-2 bg-accent-500 text-white rounded-lg hover:bg-accent-600 transition">
                        Update Profile
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin.layout.app>
