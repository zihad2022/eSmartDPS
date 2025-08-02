    <header class="bg-white shadow-sm sticky top-0 z-30">
        <div class="flex justify-between items-center px-4 md:px-6 py-4">
            <div class="flex items-center">
                <button id="sidebarToggle"
                    class="text-primary-500 hover:text-primary-700 focus:outline-none mr-4 md:hidden">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <h1 class="text-xl font-semibold text-primary-900">Dashboard</h1>
            </div>

            <div class="flex items-center space-x-2 md:space-x-4">

                <!-- Notifications -->
                <div class="relative">
                    <button id="notificationBtn"
                        class="text-primary-500 hover:text-primary-700 focus:outline-none relative">
                        <i class="fas fa-bell text-xl"></i>
                        <span class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full text-xs pulse-dot"></span>
                    </button>

                    <!-- Notification Dropdown -->
                    <div id="notificationDropdown"
                        class="notification-dropdown absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
                        <div class="p-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-primary-900">Notifications</h3>
                        </div>
                        <div class="max-h-64 overflow-y-auto">
                            <div class="p-4 border-b border-gray-100 hover:bg-gray-50">
                                <div class="flex items-start">
                                    <div
                                        class="w-8 h-8 bg-accent-100 text-accent-600 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                        <i class="fas fa-user-plus text-xs"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-primary-900">New member registration
                                        </p>
                                        <p class="text-xs text-primary-500">Sarah Johnson submitted application
                                        </p>
                                        <p class="text-xs text-primary-400 mt-1">5 minutes ago</p>
                                    </div>
                                </div>
                            </div>
                            <div class="p-4 border-b border-gray-100 hover:bg-gray-50">
                                <div class="flex items-start">
                                    <div
                                        class="w-8 h-8 bg-yellow-100 text-yellow-600 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                        <i class="fas fa-exclamation-triangle text-xs"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-primary-900">Payment overdue</p>
                                        <p class="text-xs text-primary-500">3 members have overdue payments</p>
                                        <p class="text-xs text-primary-400 mt-1">1 hour ago</p>
                                    </div>
                                </div>
                            </div>
                            <div class="p-4 border-b border-gray-100 hover:bg-gray-50">
                                <div class="flex items-start">
                                    <div
                                        class="w-8 h-8 bg-green-100 text-green-600 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                        <i class="fas fa-check text-xs"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-primary-900">Loan approved</p>
                                        <p class="text-xs text-primary-500">Michael Brown's loan application
                                            approved</p>
                                        <p class="text-xs text-primary-400 mt-1">2 hours ago</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="p-4 border-t border-gray-200">
                            <a href="#" class="text-accent-600 hover:text-accent-700 text-sm font-medium">View all
                                notifications</a>
                        </div>
                    </div>
                </div>

                <!-- User Menu -->
                <div class="relative">
                    <button id="profileBtn" class="flex items-center space-x-2 focus:outline-none">
                        <img src="{{ $user->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) }}"
                            class="w-8 h-8 rounded-full border-2 border-accent-500" alt="User">
                        <span class="hidden md:inline-block text-sm font-medium">{{ $user->name }}</span>
                        <i class="fas fa-chevron-down text-xs hidden md:inline-block"></i>
                    </button>

                    <!-- Profile Dropdown -->
                    <div id="profileDropdown"
                        class="profile-dropdown absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
                        <div class="p-4 border-b border-gray-200">
                            <p class="text-sm font-medium text-primary-900">{{ $user->name }}</p>
                            <p class="text-xs text-primary-500">{{ $user->role }}</p>
                        </div>
                        <div class="py-2">
                            <a href="{{ route('admin.profile.edit') }}"
                                class="block px-4 py-2 text-sm text-primary-700 hover:bg-gray-50">
                                <i class="fas fa-user mr-2"></i> Profile
                            </a>
                            <a href="{{ route('admin.settings.general.edit') }}"
                                class="block px-4 py-2 text-sm text-primary-700 hover:bg-gray-50">
                                <i class="fas fa-cog mr-2"></i> Settings
                            </a>
                            <a href="#" class="block px-4 py-2 text-sm text-primary-700 hover:bg-gray-50">
                                <i class="fas fa-bell mr-2"></i> Notifications
                            </a>
                            <div class="border-t border-gray-200 mt-2 pt-2">
                                <form method="POST" action="{{ route('admin.logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-50">
                                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
