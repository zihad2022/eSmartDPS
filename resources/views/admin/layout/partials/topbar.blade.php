<header class="bg-white shadow-sm sticky top-0 z-30">
    <div class="flex justify-between items-center px-4 md:px-6 py-4">
        <div class="flex items-center">
            <button id="sidebarToggle" type="button"
                class="text-primary-500 hover:text-primary-700 focus:outline-none mr-4 md:hidden"
                aria-label="Open sidebar">
                <i class="fas fa-bars text-xl"></i>
            </button>
            <div id="clock"
                class="hidden md:flex items-center justify-center px-3 py-1.5 bg-green-50 text-green-700 border border-green-200 rounded-lg shadow-sm font-mono text-sm font-semibold tracking-wider">
                --
            </div>
        </div>

        <div class="flex items-center space-x-2 md:space-x-4">
            @adminCan('view user activities')
                <div class="relative">
                    <button id="notificationBtn" type="button"
                        class="text-primary-500 hover:text-primary-700 focus:outline-none relative"
                        aria-label="Recent admin activity" aria-expanded="false">
                        <i class="fas fa-bell text-xl"></i>
                        @if ($recentActivities->isNotEmpty())
                            <span class="absolute -top-2 -right-2 min-w-4 h-4 px-1 bg-red-500 text-white rounded-full text-[10px] leading-4 text-center">
                                {{ min($recentActivities->count(), 9) }}
                            </span>
                        @endif
                    </button>

                    <div id="notificationDropdown"
                        class="notification-dropdown absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
                        <div class="p-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-primary-900">Recent Activity</h3>
                        </div>
                        <div class="max-h-72 overflow-y-auto">
                            @forelse ($recentActivities as $activity)
                                <a href="{{ route('admin.users.activities') }}"
                                    class="block p-4 border-b border-gray-100 hover:bg-gray-50">
                                    <div class="flex items-start">
                                        <div class="w-8 h-8 bg-accent-100 text-accent-600 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                            <i class="fas fa-history text-xs"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium text-primary-900 break-words">{{ $activity->activity }}</p>
                                            <p class="text-xs text-primary-500 mt-1">
                                                {{ $activity->causer?->name ?? 'System' }} ·
                                                {{ ($activity->activity_date ?? $activity->created_at)?->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <p class="p-6 text-center text-sm text-primary-500">No recent admin activity.</p>
                            @endforelse
                        </div>
                        <div class="p-4 border-t border-gray-200">
                            <a href="{{ route('admin.users.activities') }}"
                                class="text-accent-600 hover:text-accent-700 text-sm font-medium">
                                View all activity
                            </a>
                        </div>
                    </div>
                </div>
            @endadminCan

            <div class="relative">
                <button id="profileBtn" type="button" class="flex items-center space-x-2 focus:outline-none"
                    aria-label="Open profile menu" aria-expanded="false">
                    <img src="{{ $user->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) }}"
                        class="w-8 h-8 rounded-full border-2 border-accent-500" alt="{{ $user->name }}">
                    <span class="hidden md:inline-block text-sm font-medium">{{ $user->name }}</span>
                    <i class="fas fa-chevron-down text-xs hidden md:inline-block"></i>
                </button>

                <div id="profileDropdown"
                    class="profile-dropdown absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
                    <div class="p-4 border-b border-gray-200">
                        <p class="text-sm font-medium text-primary-900">{{ $user->name }}</p>
                        <p class="text-xs text-primary-500">{{ $user->roles->first()?->name ?? 'Administrator' }}</p>
                    </div>
                    <div class="py-2">
                        @adminCan('view profile')
                            <a href="{{ route('admin.profile.edit') }}"
                                class="block px-4 py-2 text-sm text-primary-700 hover:bg-gray-50">
                                <i class="fas fa-user mr-2"></i> Profile
                            </a>
                        @endadminCan
                        @adminCan('view settings')
                            <a href="{{ route('admin.settings.general.edit') }}"
                                class="block px-4 py-2 text-sm text-primary-700 hover:bg-gray-50">
                                <i class="fas fa-cog mr-2"></i> Settings
                            </a>
                        @endadminCan
                        @adminCan('view user activities')
                            <a href="{{ route('admin.users.activities') }}"
                                class="block px-4 py-2 text-sm text-primary-700 hover:bg-gray-50">
                                <i class="fas fa-history mr-2"></i> Activity
                            </a>
                        @endadminCan
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

<script>
    function updateClock() {
        const clock = document.getElementById('clock');
        if (!clock) return;

        const now = new Date();
        const hours = now.getHours();
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        const ampm = hours >= 12 ? 'PM' : 'AM';
        const formattedHours = String(hours % 12 || 12).padStart(2, '0');
        const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        const formattedDate = `${dayNames[now.getDay()]}, ${String(now.getDate()).padStart(2, '0')} ${monthNames[now.getMonth()]} ${now.getFullYear()}`;

        clock.innerHTML = `<span class="mr-2">${formattedDate}</span><span class="font-bold">${formattedHours}:${minutes}:${seconds} ${ampm}</span>`;
    }

    setInterval(updateClock, 1000);
    updateClock();
</script>
