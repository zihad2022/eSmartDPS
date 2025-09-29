<x-client.layout.app>
    {{-- Set Page Title --}}
    <x-slot name="title">Dashboard</x-slot>

    {{-- ================= Welcome Banner ================= --}}
  <x-client.dashboard.welcome-banner/>

    {{-- ================= Stats Cards ================= --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-6">
        <x-card.stat-card label="Total Members" :value="$totalMembers" icon="fas fa-users" iconBgColor="bg-blue-100"
            iconTextColor="text-blue-600" />

        <x-card.stat-card label="Total Balance" :value="$settings->currency . ' ' . number_format($totalBalance)" icon="fas fa-wallet" iconBgColor="bg-green-100"
            iconTextColor="text-green-600" />

        <x-card.stat-card label="Total Users" :value="number_format($totalUsers)" icon="fas fa-user-friends" iconBgColor="bg-purple-100"
            iconTextColor="text-purple-600" />

        <x-card.stat-card label="Total Projects" :value="number_format($totalProjects)" icon="fas fa-project-diagram"
            iconBgColor="bg-yellow-100" iconTextColor="text-yellow-600" />
    </div>
   {{-- ===========================
             Flash Messages Section
        ============================ --}}
        @if (session('success') || session('error'))
            <x-flash-message
                :type="session('success') ? 'success' : 'error'"
                :title="session('success') ? 'Success' : 'Error'"
                :message="session('success') ?? session('error')"
            />
        @endif

    {{-- ================= Charts and Recent Activities ================= --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        {{-- Financial Chart --}}
        <div class="bg-white rounded-xl shadow-sm p-6 lg:col-span-2 dashboard-card">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-semibold text-primary-900">Financial Overview</h3>
                <div class="flex space-x-2">
                    {{-- Filter Buttons --}}
                    <button
                        class="px-3 py-1 text-xs font-medium bg-accent-100 text-accent-600 rounded-lg">Monthly</button>
                    <button
                        class="px-3 py-1 text-xs font-medium bg-gray-100 text-primary-600 rounded-lg">Yearly</button>
                </div>
            </div>
            {{-- Chart Canvas --}}
            <div class="h-64">
                <canvas id="financialChart"></canvas>
            </div>
        </div>

        {{-- Recent Activities --}}
        <div class="bg-white rounded-xl shadow-sm p-6 dashboard-card">
            <h3 class="text-lg font-semibold text-primary-900 mb-6">Recent Activities</h3>
            <div class="space-y-4">
                {{-- Activity Item 1 --}}
                <div class="flex items-start">
                    <div
                        class="w-8 h-8 bg-accent-100 text-accent-600 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                        <i class="fas fa-user-plus text-xs"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-primary-900">New member joined</p>
                        <p class="text-xs text-primary-500">Sarah Johnson with 5 shares</p>
                        <p class="text-xs text-primary-400 mt-1">2 hours ago</p>
                    </div>
                </div>

                {{-- Activity Item 2 --}}
                <div class="flex items-start">
                    <div
                        class="w-8 h-8 bg-secondary-100 text-secondary-600 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                        <i class="fas fa-money-bill-wave text-xs"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-primary-900">Payment received</p>
                        <p class="text-xs text-primary-500">$500 from Michael Brown</p>
                        <p class="text-xs text-primary-400 mt-1">5 hours ago</p>
                    </div>
                </div>

                {{-- Activity Item 3 --}}
                <div class="flex items-start">
                    <div
                        class="w-8 h-8 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                        <i class="fas fa-project-diagram text-xs"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-primary-900">New project created</p>
                        <p class="text-xs text-primary-500">Real Estate Investment</p>
                        <p class="text-xs text-primary-400 mt-1">1 day ago</p>
                    </div>
                </div>
            </div>
            <a href="#" class="block text-center text-accent-600 hover:text-accent-700 text-sm font-medium mt-6">
                View All Activities
            </a>
        </div>
    </div>

    {{-- ================= Recent Payments and Active Projects ================= --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Recent Payments Table --}}
        <div class="bg-white rounded-xl shadow-sm p-6 dashboard-card">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-semibold text-primary-900">Recent Payments</h3>
                <a href="payments.html" class="text-accent-600 hover:text-accent-700 text-sm font-medium">View All</a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="px-4 py-3 text-left text-xs font-medium text-primary-500 uppercase">Member</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-primary-500 uppercase">Amount</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-primary-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Payment Row 1 --}}
                        <tr class="border-b border-gray-200">
                            <td class="px-4 py-3">
                                <div class="flex items-center">
                                    <img src="https://randomuser.me/api/portraits/men/32.jpg"
                                        class="w-8 h-8 rounded-full mr-3" alt="Member">
                                    <span class="text-sm font-medium">John Doe</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm">$250.00</td>
                            <td class="px-4 py-3">
                                <span
                                    class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Completed</span>
                            </td>
                        </tr>

                        {{-- Payment Row 2 --}}
                        <tr class="border-b border-gray-200">
                            <td class="px-4 py-3">
                                <div class="flex items-center">
                                    <img src="https://randomuser.me/api/portraits/women/44.jpg"
                                        class="w-8 h-8 rounded-full mr-3" alt="Member">
                                    <span class="text-sm font-medium">Jane Smith</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm">$500.00</td>
                            <td class="px-4 py-3">
                                <span
                                    class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Active Projects --}}
        <div class="bg-white rounded-xl shadow-sm p-6 dashboard-card">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-semibold text-primary-900">Active Projects</h3>
                <a href="projects.html" class="text-accent-600 hover:text-accent-700 text-sm font-medium">View All</a>
            </div>

            <div class="space-y-4">
                {{-- Project 1 --}}
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="text-primary-900 font-medium">Real Estate Investment</h4>
                            <p class="text-sm text-primary-600 mt-1">Expected Return: 15%</p>
                        </div>
                        <div class="bg-accent-100 text-accent-600 px-3 py-1 rounded-full text-xs font-medium">Active
                        </div>
                    </div>
                    <div class="flex items-center mt-3 text-sm text-primary-600">
                        <i class="fas fa-dollar-sign mr-2"></i>
                        <span>$15,000 invested</span>
                    </div>
                </div>

                {{-- Project 2 --}}
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="text-primary-900 font-medium">Market Investment</h4>
                            <p class="text-sm text-primary-600 mt-1">Expected Return: 12%</p>
                        </div>
                        <div class="bg-secondary-100 text-secondary-600 px-3 py-1 rounded-full text-xs font-medium">
                            Planning</div>
                    </div>
                    <div class="flex items-center mt-3 text-sm text-primary-600">
                        <i class="fas fa-dollar-sign mr-2"></i>
                        <span>$8,500 invested</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-client.layout.app>
