{{-- ============================= --}}
{{-- PAGE: Roles Management         --}}
{{-- DESCRIPTION: Create, edit, and list all roles with permissions --}}
{{-- ============================= --}}

<x-admin.layout.app>
    <x-slot:title>Roles</x-slot:title>

    {{-- Breadcrumb Navigation --}}
    <x-breadcrumb :items="[
        ['label' => 'All Roles', 'url' => route('admin.roles.index')],
        ['label' => 'Add New Role', 'url' => '#'],
    ]" />

    <div class="grid grid-cols-1 gap-8">

        {{-- Flash Messages for Success/Error --}}
        @if (session('success') || session('error'))
            <x-flash-message :type="session('success') ? 'success' : 'error'" :title="session('success') ? 'Success' : 'Error'" :message="session('success') ?? session('error')" />
        @endif

        {{-- ============================= --}}
        {{-- ROLE FORM (Create / Edit)     --}}
        {{-- This form is included from 'admin.role.form' --}}
        {{-- ============================= --}}
        @if (isset($editRole))
            @adminCan('edit roles')
                @include('admin.role.form')
            @endadminCan
        @else
            @adminCan('create roles')
                @include('admin.role.form')
            @endadminCan
        @endif

        {{-- ============================= --}}
        {{-- ROLES TABLE LISTING           --}}
        {{-- ============================= --}}
        <div class="bg-white rounded-xl shadow-sm">

            {{-- Table Header --}}
            <div class="p-6 border-b border-gray-200 flex flex-col md:flex-row md:items-center md:justify-between">
                <h3 class="text-lg font-semibold text-primary-900 mb-4 md:mb-0">All Roles</h3>
                <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-4">
                    {{-- Placeholder for extra actions (e.g., export button) --}}
                </div>
            </div>

            {{-- Table Wrapper for Horizontal Scroll --}}
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                SL</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Role Name</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Permissions</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200">
                        @php
                            // Keep track of the serial number across paginated results
                            $sl = $roles->firstItem() ?? 1;
                        @endphp

                        {{-- Loop through all roles --}}
                        @forelse ($roles as $role)
                            <tr class="hover:bg-gray-50">

                                {{-- Serial Number --}}
                                <td class="px-6 py-4 text-sm text-primary-900">#{{ $sl++ }}</td>

                                {{-- Role Name --}}
                                <td class="px-6 py-4 text-sm text-primary-900">{{ $role->name }}</td>

                                {{-- Permissions Display (show max 3, then +X more) --}}
                                <td class="px-6 py-4 text-sm text-primary-600">
                                    @foreach ($role->permissions as $perm)
                                        @if ($loop->iteration <= 3)
                                            <span
                                                class="inline-block bg-gray-200 text-primary-700 rounded px-2 py-1 text-xs mr-1 mb-1">
                                                {{ $perm->name }}
                                            </span>
                                        @else
                                            <span class="text-gray-500">+{{ $role->permissions->count() - 3 }}
                                                more</span>
                                            @break
                                        @endif
                                    @endforeach
                                </td>

                                {{-- Actions: Edit / Delete --}}
                                <td class="px-6 py-4 text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('admin.roles.show', $role) }}"
                                            class="text-accent-600 hover:text-accent-900" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if (in_array($role->id, $manageableRoleIds, true))
                                            @adminCan('edit roles')
                                                <a href="{{ route('admin.roles.edit', $role) }}"
                                                    class="text-secondary-600 hover:text-secondary-900" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endadminCan

                                            @adminCan('delete roles')
                                                <form method="POST" action="{{ route('admin.roles.destroy', $role) }}"
                                                    class="delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="text-red-600 hover:text-red-900 delete-btn"
                                                        title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endadminCan
                                        @else
                                            <span class="text-gray-500">Protected</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            {{-- No roles found --}}
                            <tr>
                                <td colspan="5" class="text-center py-4 text-primary-600">No roles found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination Footer --}}
            <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                <div class="text-sm text-primary-600">
                    Showing {{ $roles->firstItem() ?? 0 }} to {{ $roles->lastItem() ?? 0 }} of {{ $roles->total() }}
                    results
                </div>
                <x-pagination :paginator="$roles" />
            </div>
        </div>
    </div>
    </x-admin.layout.app>
