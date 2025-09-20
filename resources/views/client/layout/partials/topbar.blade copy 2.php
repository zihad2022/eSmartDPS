<header class="bg-white shadow sticky top-0 z-30">
    <div class="flex justify-between items-center px-6 py-4 md:px-8">
        <!-- Left: Sidebar Toggle & Page Title -->
        <div class="flex items-center space-x-4">
            <button id="sidebarToggle"
                class="text-primary-500 hover:text-primary-700 focus:outline-none md:hidden p-2 rounded-lg transition">
                <i class="fas fa-bars text-xl"></i>
            </button>

            <h1 class="text-xl font-semibold text-gray-900">Dashboard</h1>

            <!-- Active Package Info -->
            @php
                $client = auth('client')->user();
                $activeClientPackage = $client?->activeClientPackage;
                $activePackage = $activeClientPackage?->package;
            @endphp

            @if ($activePackage)
                @php
                    $endsAt = $activeClientPackage->ends_at;
                    $remaining = $endsAt && $endsAt->isFuture()
                        ? $endsAt->diffForHumans(now(), [
                            'parts' => 2,
                            'short' => true,
                            'syntax' => \Carbon\CarbonInterface::DIFF_RELATIVE_TO_NOW,
                        ])
                        : 'Expired';
                @endphp

                <span class="ml-4 px-3 py-1 rounded-full text-sm font-medium
                    {{ $activePackage->has_trial && $activePackage->trial_days > 0 ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                    {{ $activePackage->has_trial && $activePackage->trial_days > 0 ? 'Free Trial' : 'Paid Subscription' }} ({{ $remaining }})
                </span>
            @else
                <span class="ml-4 px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-500">
                    No Active Subscription
                </span>
            @endif
        </div>

        <!-- Right: Notifications & Profile -->
        <div class="flex items-center space-x-4">
            <!-- Notifications -->
            <div class="relative">
                <button id="notificationBtn"
                    class="text-gray-600 hover:text-gray-800 focus:outline-none p-2 rounded-full transition relative">
                    <i class="fas fa-bell text-lg"></i>
                    <span
                        class="absolute -top-1 -right-1 w-2.5 h-2.5 bg-red-500 rounded-full animate-pulse"></span>
                </button>

                <div id="notificationDropdown"
                    class="hidden absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50 overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-900">Notifications</h3>
                    </div>
                    <div class="max-h-64 overflow-y-auto">
                        <!-- Notification Item -->
                        <div class="p-4 border-b border-gray-100 hover:bg-gray-50 flex items-start space-x-3">
                            <div
                                class="w-9 h-9 bg-blue-100 text-blue-700 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-user-plus text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">New member registration</p>
                                <p class="text-xs text-gray-500">Sarah Johnson submitted application</p>
                                <p class="text-xs text-gray-400 mt-1">5 minutes ago</p>
                            </div>
                        </div>
                        <div class="p-4 border-b border-gray-100 hover:bg-gray-50 flex items-start space-x-3">
                            <div
                                class="w-9 h-9 bg-yellow-100 text-yellow-700 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Payment overdue</p>
                                <p class="text-xs text-gray-500">3 members have overdue payments</p>
                                <p class="text-xs text-gray-400 mt-1">1 hour ago</p>
                            </div>
                        </div>
                        <div class="p-4 border-b border-gray-100 hover:bg-gray-50 flex items-start space-x-3">
                            <div
                                class="w-9 h-9 bg-green-100 text-green-700 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-check text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Loan approved</p>
                                <p class="text-xs text-gray-500">Michael Brown's loan application approved</p>
                                <p class="text-xs text-gray-400 mt-1">2 hours ago</p>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 py-3 border-t border-gray-200 bg-gray-50 text-center">
                        <a href="#" class="text-blue-600 hover:text-blue-700 text-sm font-medium">View all
                            notifications</a>
                    </div>
                </div>
            </div>

            <!-- Profile Menu -->
            <div class="relative">
                <button id="profileBtn"
                    class="flex items-center space-x-2 focus:outline-none rounded-full hover:bg-gray-100 px-2 py-1 transition">
                    <img src="https://randomuser.me/api/portraits/men/32.jpg"
                        class="w-8 h-8 rounded-full border-2 border-blue-500" alt="User">
                    <span class="hidden md:inline-block text-sm font-medium text-gray-900">
                        {{ auth('client')->user()->first_name }} {{ auth('client')->user()->last_name }}
                    </span>
                    <i class="fas fa-chevron-down text-xs hidden md:inline-block text-gray-500"></i>
                </button>

                <div id="profileDropdown"
                    class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-50 overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                        <p class="text-sm font-medium text-gray-900">{{ auth('client')->user()->first_name }} {{ auth('client')->user()->last_name }}</p>
                        <p class="text-xs text-gray-500">{{ auth('client')->user()->role }}</p>
                    </div>
                    <div class="py-2">
                        <a href="{{ route('client.profile.edit') }}"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                            <i class="fas fa-user mr-2"></i> Profile
                        </a>
                        <a href="{{ route('client.settings.general.edit') }}"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                            <i class="fas fa-cog mr-2"></i> Settings
                        </a>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                            <i class="fas fa-bell mr-2"></i> Notifications
                        </a>
                        <div class="border-t border-gray-200 mt-2 pt-2">
                            <form method="POST" action="{{ route('client.logout') }}">
                                @csrf
                                <button type="submit"
                                    class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-50 transition">
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
