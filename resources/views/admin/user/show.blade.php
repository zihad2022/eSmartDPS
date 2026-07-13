<x-admin.layout.app>
    <x-slot:title>User Details</x-slot:title>
    <x-breadcrumb :items="[
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Users', 'url' => route('admin.users.index')],
        ['label' => $user->name],
    ]" />

    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex flex-col gap-6 md:flex-row md:items-start md:justify-between">
                <div class="flex items-center gap-5">
                    <img src="{{ $user->profile_photo_url ?: 'https://ui-avatars.com/api/?name='.urlencode($user->name) }}"
                        alt="{{ $user->name }}" class="h-24 w-24 rounded-full object-cover">
                    <div>
                        <h2 class="text-2xl font-bold text-primary-900">{{ $user->name }}</h2>
                        <p class="text-sm text-primary-500">{{ $user->email }}</p>
                        <div class="mt-3 flex flex-wrap gap-2">
                            @forelse ($user->roles as $role)
                                <span class="rounded-full bg-accent-50 px-3 py-1 text-xs font-medium text-accent-700">{{ $role->name }}</span>
                            @empty
                                <span class="rounded-full bg-gray-100 px-3 py-1 text-xs text-gray-600">No role assigned</span>
                            @endforelse
                            <span class="rounded-full px-3 py-1 text-xs font-medium {{ $user->status ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $user->status ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                </div>
                @if (auth('admin')->user()->can('edit users') && $canManageUser)
                    <a href="{{ route('admin.users.edit', $user) }}" class="inline-flex items-center justify-center rounded-lg bg-accent-500 px-4 py-2 text-sm text-white hover:bg-accent-600">
                        <i class="fas fa-edit mr-2"></i>Edit User
                    </a>
                @endif
            </div>

            <div class="mt-8 grid grid-cols-1 gap-5 border-t border-gray-100 pt-6 md:grid-cols-2 lg:grid-cols-4">
                <x-display.field label="Username" :value="$user->username" />
                <x-display.field label="Phone" :value="$user->phone ?: '—'" />
                <x-display.field label="Last Login" :value="$user->last_login?->format('M d, Y h:i A') ?: 'Never'" />
                <x-display.field label="Created" :value="$user->created_at->format('M d, Y h:i A')" />
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-primary-900 mb-4">Recent Activity</h3>
            <div class="divide-y divide-gray-100">
                @forelse ($activities as $activity)
                    <div class="py-4 first:pt-0 last:pb-0">
                        <p class="text-sm font-medium text-primary-800">{{ $activity->activity }}</p>
                        <p class="mt-1 text-xs text-primary-500">
                            {{ ($activity->activity_date ?? $activity->created_at)?->format('M d, Y h:i A') }}
                            @if ($activity->ip_address) · {{ $activity->ip_address }} @endif
                        </p>
                    </div>
                @empty
                    <p class="py-6 text-center text-sm text-primary-500">No activity recorded for this user.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-admin.layout.app>
