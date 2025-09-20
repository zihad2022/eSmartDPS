<x-client.layout.app>
    <div class="bg-white rounded-2xl shadow-sm p-8">
        <h2 class="text-2xl font-bold text-primary-900 mb-6">User Details</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            {{-- Profile Photo --}}
            <div class="flex items-center space-x-4">
                @if ($user->profile_photo)
                    <img src="{{ $user->profile_photo_url }}" alt="Profile Photo"
                        class="w-20 h-20 rounded-full border object-cover">
                @else
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->first_name . ' ' . $user->last_name) }}&background=0D8ABC&color=fff" class="w-20 h-20 rounded-full border object-cover" alt="Profile Photo">
                @endif
                <div>
                    <p class="text-lg font-semibold text-gray-900">
                        {{ $user->first_name }} {{ $user->last_name }}
                    </p>
                    <p class="text-sm text-gray-500">User ID: {{ $user->user_id }}</p>
                </div>
            </div>

            {{-- Status & Role --}}
            <div class="flex flex-col space-y-2">
                <p><span class="font-semibold">Role:</span>
                    <span
                        class="px-2 py-1 text-xs rounded-full 
                        {{ $user->role === 'admin'
                            ? 'bg-red-100 text-red-700'
                            : ($user->role === 'manager'
                                ? 'bg-blue-100 text-blue-700'
                                : 'bg-gray-100 text-gray-700') }}">
                        {{ ucfirst($user->role) }}
                    </span>
                </p>

                <p><span class="font-semibold">Status:</span>
                    <span
                        class="px-2 py-1 text-xs rounded-full 
                        {{ $user->status ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-700' }}">
                        {{ $user->status ? 'Active' : 'Inactive' }}
                    </span>
                </p>
            </div>
        </div>

        {{-- User Info Grid --}}
        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
            <x-display.field label="Email" :value="$user->email" />
            <x-display.field label="Phone" :value="$user->phone ?? '—'" />
            <x-display.field label="Division" :value="$user->division ?? '—'" />
            <x-display.field label="District" :value="$user->district ?? '—'" />
            <x-display.field label="Address" :value="$user->address ?? '—'" />
            <x-display.field label="Postal Code" :value="$user->postal_code ?? '—'" />
            <x-display.field label="Created At" :value="$user->created_at->format('M d, Y h:i A')" />
            {{-- <x-display.field label="Last Login" :value="$user->last_login_at ? $user->last_login_at->format('M d, Y h:i A') : 'Never'" /> --}}
        </div>

        {{-- Actions --}}
        <div class="flex justify-end mt-8">
            <a href="{{ route('client.users.index') }}"
                class="px-4 py-2 border border-gray-300 text-primary-700 rounded-lg hover:bg-gray-50 text-sm transition">
                Back to Users
            </a>
        </div>
    </div>
</x-client.layout.app>
