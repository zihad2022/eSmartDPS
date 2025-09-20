<x-admin.layout.app>
    @php
        $pageTitle = 'Profile';
        $breadcrumb = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['label' => $pageTitle, 'url' => route('admin.profile.edit')],
        ];
    @endphp

    <x-slot:title>{{ $pageTitle }}</x-slot:title>
    <x-breadcrumb :items="$breadcrumb" />

    <div>
        {{-- Flash Messages --}}
        @if (session('success') || session('error'))
            <x-flash-message :type="session('success') ? 'success' : 'error'" :title="session('success') ? 'Success' : 'Error'" :message="session('success') ?? session('error')" />
        @endif

        <div class="bg-white rounded-2xl shadow-sm p-8 mx-auto">
            <h2 class="text-2xl font-bold text-primary-900 mb-6">
                Update {{ $pageTitle }}
            </h2>

            <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data"
                class="space-y-8">
                @csrf
                @method('PUT')

                {{-- Profile Photo --}}
                <div class="flex items-center space-x-6">
                    <div class="w-20 h-20 rounded-full overflow-hidden bg-gray-100">
                        <img src="{{ $user->profile_photo_url ?: 'https://ui-avatars.com/api/?name=' . urlencode($user->name) }}"
                            alt="Profile Photo" class="w-full h-full object-cover">
                    </div>
                    <div>
                        <x-form.label for="profile_photo">Profile Photo</x-form.label>
                        <x-form.input type="file" name="profile_photo" id="profile_photo" class="p-2" />
                        <p class="text-xs text-gray-500 mt-1">Upload JPG/PNG (max 2MB)</p>
                    </div>
                </div>

                {{-- Name & Phone --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-form.input name="name" label="Full Name" :value="old('name', $user->name)" required />
                    <x-form.input name="phone" label="Phone Number" type="tel" :value="old('phone', $user->phone)" required />
                </div>

                {{-- Email & Username --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-form.input name="email" label="Email" type="email" :value="$user->email" disabled />
                    <x-form.input name="username" label="Username" :value="$user->username" disabled />
                </div>

                {{-- Role --}}
                <x-form.input name="role" label="Role" :value="$user->roles->first()->name" disabled />

                {{-- Password --}}
                <div>
                    <x-form.input name="password" label="New Password" type="password" placeholder="Leave blank to keep current password" />
                    <p class="text-xs text-gray-500 mt-1">Leave empty if you don't want to change password.</p>
                </div>

                {{-- Submit --}}
                <div class="flex justify-end">
                    <x-button type="submit" color="accent">Update Profile</x-button>
                </div>
            </form>
        </div>
    </div>
</x-admin.layout.app>
