<x-admin.layout.app>
    @php
        /**
         * =======================================
         * Page Setup: Status, Title, Breadcrumbs
         * =======================================
         */

        // 1. Retrieve status filter from the request (?status=active/inactive)
        $status = request()->status;

        // 2. Map status values to human-readable titles
        $titleMap = [
            'active' => 'Active System Users',
            'inactive' => 'Inactive System Users',
        ];

        // 3. Determine the page title based on filter, fallback to "All Users"
        $pageTitle = $titleMap[$status] ?? 'All System Users';

        // 4. Base breadcrumb items
        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'All Users', 'url' => route('admin.users.index')],
        ];

        // 5. Append specific status breadcrumb if filter applied
        if (isset($titleMap[$status])) {
            $breadcrumbItems[] = [
                'label' => $pageTitle,
                'url' => route('admin.users.index', ['status' => $status]),
            ];
        }
    @endphp

    {{-- ===========================
         Set HTML Page Title
    ============================ --}}
    <x-slot:title>{{ $pageTitle }}</x-slot:title>

    {{-- ===========================
         Breadcrumb Navigation
    ============================ --}}
    <x-breadcrumb :items="$breadcrumbItems" />

    {{-- ===========================
         Main Content Wrapper
    ============================ --}}
    <div>

        {{-- ===========================
             Flash Messages Section
        ============================ --}}
        @if (session('success') || session('error'))
            <x-flash-message :type="session('success') ? 'success' : 'error'" :title="session('success') ? 'Success' : 'Error'" :message="session('success') ?? session('error')" />
        @endif

        {{-- ===========================
             Stats Cards Section (Optional)
        ============================ --}}
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-3 gap-4 md:gap-6 mb-6 w-full">
            <x-card.stat-card :label="'Total Users'" :value="number_format($totalUsers)" :iconBgColor="'bg-primary-100'" :iconTextColor="'text-primary-600'"
                :icon="'fas fa-users'" />
            <x-card.stat-card :label="'Active Users'" :value="number_format($activeUsers)" :iconBgColor="'bg-green-100'" :iconTextColor="'text-green-600'"
                :icon="'fas fa-user-check'" />
            <x-card.stat-card :label="'Inactive Users'" :value="number_format($inactiveUsers)" :iconBgColor="'bg-red-100'" :iconTextColor="'text-red-600'"
                :icon="'fas fa-user-times'" />
        </div>



        {{-- ===========================
             Users Table Section
        ============================ --}}
        <div class="bg-white rounded-xl shadow-sm">

            {{-- ====================================
                 Table Header: Section Title + Actions
            ==================================== --}}
            <div class="p-6 border-b border-gray-200">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">

                    {{-- Section Title --}}
                    <h3 class="text-lg font-semibold text-primary-900 mb-4 md:mb-0">
                        {{ $pageTitle }}
                    </h3>

                    {{-- Table Actions (Role Filter + Export) --}}
                    <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-4">

                        {{-- Search Form --}}
                        <form method="GET" action="{{ route('admin.users.index') }}" class="relative w-full md:w-auto">
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Search users..."
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-accent-500 focus:border-accent-500 transition duration-300">
                            <button type="submit"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-accent-500 transition">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>

                        <a href="{{ route('admin.users.create') }}"
                            class="bg-accent-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-accent-600 transition">
                            Add User
                        </a>

                        {{-- Export Button --}}
                        <a href="{{ route('admin.users.export', ['status' => $status]) }}"
                            class="bg-gray-100 hover:bg-gray-200 text-primary-700 px-4 py-2 rounded-lg text-sm font-medium transition duration-300">
                            <i class="fas fa-download mr-2"></i>Export
                        </a>
                    </div>
                </div>
            </div>

            {{-- =============================
                 Table Body
            ============================= --}}
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    {{-- Table Headers --}}
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                User</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Role</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Last Login</th>
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
                        @forelse ($users as $user)
                            <tr class="hover:bg-gray-50">

                                {{-- User Info --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <img src="{{ $user->profile_photo_url ? $user->profile_photo_url : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) }}"
                                            class="w-10 h-10 rounded-full mr-4" alt="{{ $user->name }}">
                                        <div>
                                            <div class="text-sm font-medium text-primary-900">{{ $user->name }}</div>
                                            <div class="text-sm text-primary-500">{{ $user->email }} ·
                                                {{ $user->username }}</div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Role --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php $roleName = $user->getRoleNames()->first(); @endphp
                                    <span
                                        class="px-2 py-1 text-xs font-medium rounded-full
                                        {{ $roleName === 'admin' ? 'bg-red-100 text-red-800' : ($roleName === 'manager' ? 'bg-blue-100 text-blue-800' : ($roleName === 'super-admin' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800')) }}">
                                        {{ ucfirst($roleName) }}
                                    </span>
                                </td>

                                {{-- Last Login --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-primary-600">
                                    {{ $user->last_login ? $user->last_login->format('M d, Y h:i A') : '—' }}
                                </td>

                                {{-- Created At --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-primary-600">
                                    {{ $user->created_at->format('M d, Y h:i A') }}</td>

                                {{-- Status Badge --}}
                                {{-- <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 py-1 text-xs font-medium rounded-full 
                                        {{ $user->status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $user->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td> --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <livewire:user-status-toggle :admin="$user" :key="$user->id" />
                                </td>


                                {{-- Actions --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        {{-- View --}}
                                        <a href="{{ route('admin.users.show', $user->id) }}"
                                            class="text-accent-600 hover:text-accent-900" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        {{-- Edit --}}
                                        <a href="{{ route('admin.users.edit', $user->id) }}"
                                            class="text-secondary-600 hover:text-secondary-900" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        {{-- Delete --}}
                                        @if ($user->roles->first()->name !== 'super-admin')
                                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
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
                                    <x-confirm-modal />
                                </td>
                            </tr>
                        @empty
                            {{-- Empty State --}}
                            <tr>
                                <td colspan="6" class="text-center py-4 text-sm text-gray-500">No users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ===============================
                 Pagination & Results Info
            =============================== --}}
            <div class="px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    {{-- Results Info --}}
                    <div class="text-sm text-primary-600">
                        Showing 1 to {{ $users->count() }} of {{ $users->total() ?? $users->count() }} results
                    </div>

                    {{-- Pagination Links --}}
                    <div class="flex space-x-2">
                        <x-pagination :paginator="$users" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin.layout.app>
