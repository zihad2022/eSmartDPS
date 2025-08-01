<x-admin.layout.app>
    <!-- Users Content -->
    <div class="p-4 md:p-6">
        @if (session('success') || session('error'))
            <x-flash-message :type="session('success') ? 'success' : 'error'" :title="session('success') ? 'Success' : 'Error'" :message="session('success') ?? session('error')" />
        @endif

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 mb-6">
            <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs md:text-sm text-primary-500 font-medium">Total Users</p>
                        <h3 class="text-xl md:text-2xl font-bold text-primary-900">8</h3>
                    </div>
                    <div class="w-10 h-10 bg-accent-100 text-accent-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user-shield text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs md:text-sm text-primary-500 font-medium">Administrators</p>
                        <h3 class="text-xl md:text-2xl font-bold text-primary-900">3</h3>
                    </div>
                    <div class="w-10 h-10 bg-red-100 text-red-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user-cog text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs md:text-sm text-primary-500 font-medium">Managers</p>
                        <h3 class="text-xl md:text-2xl font-bold text-primary-900">3</h3>
                    </div>
                    <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user-tie text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs md:text-sm text-primary-500 font-medium">Active Users</p>
                        <h3 class="text-xl md:text-2xl font-bold text-primary-900">7</h3>
                    </div>
                    <div class="w-10 h-10 bg-green-100 text-green-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user-check text-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users Table -->
        <div class="bg-white rounded-xl shadow-sm">
            <div class="p-6 border-b border-gray-200">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <h3 class="text-lg font-semibold text-primary-900 mb-4 md:mb-0">System Users</h3>
                    <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-4">
                        <!-- Role Filter -->
                        <select
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-500">
                            <option>All Roles</option>
                            <option>Administrator</option>
                            <option>Manager</option>
                            <option>Viewer</option>
                        </select>

                        <!-- Status Filter -->
                        <select
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-500">
                            <option>All Status</option>
                            <option>Active</option>
                            <option>Inactive</option>
                        </select>

                        <!-- Export Button -->
                        <button
                            class="bg-gray-100 hover:bg-gray-200 text-primary-700 px-4 py-2 rounded-lg text-sm font-medium transition duration-300">
                            <i class="fas fa-download mr-2"></i>Export
                        </button>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
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
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($users as $user)
                            <tr class="hover:bg-gray-50">
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
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 py-1 text-xs font-medium rounded-full 
                                    {{ $user->role === 'admin' ? 'bg-red-100 text-red-800' : ($user->role === 'manager' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-primary-600">—</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-primary-600">
                                    {{ $user->created_at->format('M d, Y h:i A') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 py-1 text-xs font-medium rounded-full 
                                    {{ $user->status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $user->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <!-- View Button -->
                                        <a href="{{ route('admin.users.show', $user->id) }}"
                                            class="text-accent-600 hover:text-accent-900" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        <!-- Edit Button -->
                                        <a href="{{ route('admin.users.edit', $user->id) }}"
                                            class="text-secondary-600 hover:text-secondary-900" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <!-- Reset Password (you may later implement this route) -->
                                        {{-- <a href="{{ route('admin.users.reset-password', $user->id) }}"
                                            class="text-yellow-600 hover:text-yellow-900" title="Reset Password">
                                            <i class="fas fa-key"></i>
                                        </a> --}}

                                        <!-- Delete Button -->
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                                            class="delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="text-red-600 hover:text-red-900 delete-btn"
                                                title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                                <x-confirm-modal />
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-sm text-gray-500">No users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <x-confirm-modal />

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-primary-600">
                        Showing 1 to 8 of 8 results
                    </div>
                    <div class="flex space-x-2">
                        <button
                            class="px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50 disabled:opacity-50"
                            disabled>Previous</button>
                        <button class="px-3 py-1 text-sm bg-accent-500 text-white rounded">1</button>
                        <button
                            class="px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50 disabled:opacity-50"
                            disabled>Next</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin.layout.app>
