<x-admin.layout.app>
    <x-slot:title>Role Details</x-slot:title>
    <x-breadcrumb :items="[
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Roles', 'url' => route('admin.roles.index')],
        ['label' => $role->name],
    ]" />

    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-primary-900">{{ $role->name }}</h2>
                    <p class="mt-1 text-sm text-primary-500">{{ $role->permissions->count() }} permissions · {{ $role->users->count() }} assigned users</p>
                </div>
                @if ($role->can_be_managed_by_current_admin && auth('admin')->user()->can('edit roles'))
                    <a href="{{ route('admin.roles.edit', $role) }}" class="rounded-lg bg-accent-500 px-4 py-2 text-sm text-white hover:bg-accent-600">
                        <i class="fas fa-edit mr-2"></i>Edit Role
                    </a>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-primary-900 mb-4">Permissions</h3>
            <div class="flex flex-wrap gap-2">
                @forelse ($role->permissions->sortBy('name') as $permission)
                    <span class="rounded-full bg-gray-100 px-3 py-1.5 text-xs font-medium text-primary-700">{{ $permission->name }}</span>
                @empty
                    <p class="text-sm text-primary-500">No permissions assigned.</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-primary-900 mb-4">Assigned Users</h3>
            <div class="divide-y divide-gray-100">
                @forelse ($role->users as $assignedUser)
                    @adminCan('view users')
                        <a href="{{ route('admin.users.show', $assignedUser) }}" class="flex items-center justify-between py-3 first:pt-0 last:pb-0 hover:text-accent-600">
                            <span class="text-sm font-medium">{{ $assignedUser->name }}</span>
                            <span class="text-xs text-primary-500">{{ $assignedUser->email }}</span>
                        </a>
                    @else
                        <div class="flex items-center justify-between py-3 first:pt-0 last:pb-0">
                            <span class="text-sm font-medium">{{ $assignedUser->name }}</span>
                            <span class="text-xs text-primary-500">{{ $assignedUser->email }}</span>
                        </div>
                    @endadminCan
                @empty
                    <p class="text-sm text-primary-500">No users are assigned to this role.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-admin.layout.app>
