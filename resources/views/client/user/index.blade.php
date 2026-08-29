<x-client.layout.app>
    @php
        /**
         * =======================================
         *  🔹 Breadcrumb Items for Navigation
         * =======================================
         */

        $status = request()->status;

        $titleMap = [
            'active' => 'Active System Users',
            'inactive' => 'Inactive System Users',
        ];
        $pageTitle = $titleMap[$status] ?? 'All System Users';

        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('client.dashboard')],
            ['label' => 'All Users', 'url' => route('client.users.index')],
        ];
    @endphp

    {{-- ===========================
         Breadcrumb Component
    ============================ --}}
    <x-breadcrumb :items="$breadcrumbItems" />

    {{-- ===========================
         Page Title
    ============================ --}}
    <x-slot:title>{{ $pageTitle }}</x-slot:title>

    <!-- ======================================================
         USERS DASHBOARD CONTENT
    ======================================================= -->
    <div>
        {{-- =========================
            Flash Messages Section
        ========================== --}}
        @if (session('success') || session('error'))
            <x-flash-message :type="session('success') ? 'success' : 'error'" :title="session('success') ? 'Success' : 'Error'" :message="session('success') ?? session('error')" />
        @endif

        {{-- =========================
            Stats Cards Section
        ========================== --}}
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 md:gap-6 mb-6">
            {{-- Total Users --}}
            <x-card.stat-card :label="'Total Users'" :value="number_format($totalUsers)" :iconBgColor="'bg-gray-100'" :iconTextColor="'text-gray-600'"
                :icon="'fas fa-users'" />

            {{-- Administrators --}}
            <x-card.stat-card :label="'Administrators'" :value="number_format($administratorUsers)" :iconBgColor="'bg-red-100'" :iconTextColor="'text-red-600'"
                :icon="'fas fa-user-shield'" />

            {{-- Managers --}}
            <x-card.stat-card :label="'Managers'" :value="number_format($managerUsers)" :iconBgColor="'bg-blue-100'" :iconTextColor="'text-blue-600'"
                :icon="'fas fa-user-tie'" />

            {{-- Editors --}}
            <x-card.stat-card :label="'Editors'" :value="number_format($editorUsers)" :iconBgColor="'bg-purple-100'" :iconTextColor="'text-purple-600'"
                :icon="'fas fa-user-edit'" />

            {{-- Active Users --}}
            <x-card.stat-card :label="'Active Users'" :value="number_format($activeUsers)" :iconBgColor="'bg-green-100'" :iconTextColor="'text-green-600'"
                :icon="'fas fa-user-check'" />

            {{-- Inactive Users --}}
            <x-card.stat-card :label="'Inactive Users'" :value="number_format($inactiveUsers)" :iconBgColor="'bg-yellow-100'" :iconTextColor="'text-yellow-600'"
                :icon="'fas fa-user-slash'" />
        </div>

        {{-- =========================
            Users Table Section
        ========================== --}}
        <div class="bg-white rounded-xl shadow-sm">

            {{-- ===== Table Header: Title + Filters ===== --}}
            <div class="p-6 border-b border-gray-200">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">

                    {{-- Section Title --}}
                    <h3 class="text-lg font-semibold text-primary-900 mb-4 md:mb-0">
                        {{ $pageTitle }}
                    </h3>

                    {{-- Filters + Export --}}
                    <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-4">

                        {{-- Search Form --}}
                        <form method="GET" action="{{ route('client.users.index') }}" class="relative w-full md:w-auto">
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Search users..."
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-accent-500 focus:border-accent-500 transition duration-300">
                            <button type="submit"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-accent-500 transition">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>

                        {{-- Filters Form --}}
                        <form method="GET" action="{{ route('client.users.index') }}" class="flex space-x-4">

                            {{-- Role Filter --}}
                            <select name="role"
                                class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-500"
                                onchange="this.form.submit()">
                                <option value="">All Roles</option>
                                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Administrator
                                </option>
                                <option value="manager" {{ request('role') == 'manager' ? 'selected' : '' }}>Manager
                                </option>
                                <option value="editor" {{ request('role') == 'editor' ? 'selected' : '' }}>Editor
                                </option>
                            </select>
                        </form>

                        {{-- Export Button --}}
                        <a href="{{ route('client.users.export', [
                            'role' => request('role'),
                            'status' => request('status'),
                        ]) }}"
                            class="bg-gray-100 hover:bg-gray-200 text-primary-700 px-4 py-2 rounded-lg text-sm font-medium transition duration-300">
                            <i class="fas fa-download mr-2"></i>Export
                        </a>

                    </div>
                </div>
            </div>

            {{-- ===== Table Body ===== --}}
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    {{-- Table Head --}}
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                #Sl</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                User ID</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Name</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Email</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Phone</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Role</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Created</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Status</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>

                    {{-- Table Rows --}}
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php $sl = 1; @endphp
                        @forelse ($users as $user)
                            <tr class="hover:bg-gray-50">
                                {{-- SL --}}
                                <td class="px-6 py-4 whitespace-nowrap">#{{ $sl++ }}</td>

                                {{-- User ID --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-primary-600">
                                    {{ $user->user_id }}
                                </td>

                                {{-- User Info --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if ($user->profile_photo)
                                            <img src="{{ $user->profile_photo_url }}"
                                                class="w-10 h-10 rounded-full mr-4" alt="User">
                                        @else
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->first_name . ' ' . $user->last_name) }}&background=0D8ABC&color=fff"
                                                class="w-10 h-10 rounded-full mr-4" alt="User">
                                        @endif
                                        <div class="text-sm font-medium text-primary-900">
                                            {{ $user->first_name }} {{ $user->last_name }}
                                        </div>
                                    </div>
                                </td>

                                {{-- Email --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $user->email }}</td>

                                {{-- Phone --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $user->phone ?? '-' }}</td>

                                {{-- Role --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>

                                {{-- Created Date --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-primary-600">
                                    {{ $user->created_at->format('M d, Y h:i A') }}
                                </td>

                                {{-- Status --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($user->status)
                                        <span
                                            class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Active</span>
                                    @else
                                        <span
                                            class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">Inactive</span>
                                    @endif
                                </td>

                                {{-- Actions --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        {{-- View --}}
                                        <a href="{{ route('client.users.show', $user->id) }}"
                                            class="text-accent-600 hover:text-accent-900" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        {{-- Edit --}}
                                        <a href="{{ route('client.users.edit', $user->id) }}"
                                            class="text-secondary-600 hover:text-secondary-900" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        {{-- Delete (not allowed for Super Admin) --}}
                                        @if ($user->role != 'super-admin')
                                            <form method="POST"
                                                action="{{ route('client.users.destroy', $user->id) }}"
                                                class="delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                    class="text-red-600 hover:text-red-900 delete-btn" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            {{-- Empty State --}}
                            <tr>
                                <td colspan="16" class="text-center py-4">No users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>


            {{-- ===========================
                 🔹 Pagination & Results Info
            ============================ --}}
            <div class="px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    {{-- Results Info --}}
                    <div class="text-sm text-primary-600">
                        @if ($users->total() > 0)
                            Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of
                            {{ $users->total() }} results
                        @else
                            No results found.
                        @endif
                    </div>

                    {{-- Pagination Links --}}
                    <div class="flex space-x-2">
                        <x-pagination :paginator="$users" />
                    </div>
                </div>
            </div>

        </div> {{-- End Users Table Section --}}
    </div>
</x-client.layout.app>
