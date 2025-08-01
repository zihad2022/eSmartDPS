<x-member.layout.app>
    <!-- Welcome Section -->
    <div class="mb-8">
        <h1 class="text-3xl font-display font-bold text-primary-900 mb-2">Welcome back, <span
                id="welcomeName">John</span>!</h1>
        <p class="text-primary-600">Here's your savings overview and recent activity.</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="card-hover bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-primary-500 font-medium">Total Balance</p>
                    <h3 class="text-2xl font-bold text-primary-900">${{ $totalBalance }}</h3>
                </div>
                <div class="w-12 h-12 bg-accent-100 text-accent-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-wallet text-xl"></i>
                </div>
            </div>
        </div>

        <div class="card-hover bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-primary-500 font-medium">Monthly Savings</p>
                    <h3 class="text-2xl font-bold text-primary-900">{{ $monthlySavings }}</h3>
                </div>
                <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-piggy-bank text-xl"></i>
                </div>
            </div>
        </div>

        <div class="card-hover bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-primary-500 font-medium">Total Shares</p>
                    <h3 class="text-2xl font-bold text-primary-900">{{ $totalShares }}</h3>
                </div>
                <div class="w-12 h-12 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-chart-pie text-xl"></i>
                </div>
            </div>
        </div>

        <div class="card-hover bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-primary-500 font-medium">Interest Earned</p>
                    <h3 class="text-2xl font-bold text-primary-900">$145.50</h3>
                </div>
                <div class="w-12 h-12 bg-green-100 text-green-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-percentage text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Savings Goal Progress -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-8 border border-gray-100">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-primary-900">Savings Goal Progress</h3>
            <span class="text-sm text-primary-600">83% Complete</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-3 mb-4">
            <div class="progress-bar bg-gradient-to-r from-accent-500 to-accent-600 h-3 rounded-full"
                style="width: 83%"></div>
        </div>
        <div class="flex justify-between text-sm text-primary-600">
            <span>Current: $2,450</span>
            <span>Goal: $3,000</span>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-primary-900">Recent Transactions</h3>
                    <button class="text-accent-600 hover:text-accent-700 text-sm font-medium">View All</button>
                </div>
            </div>

            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div
                                class="w-10 h-10 bg-green-100 text-green-600 rounded-full flex items-center justify-center">
                                <i class="fas fa-plus text-sm"></i>
                            </div>
                            <div>
                                <p class="font-medium text-primary-900">Monthly Deposit</p>
                                <p class="text-sm text-primary-500">May 15, 2025</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-green-600">+$250.00</p>
                            <p class="text-xs text-primary-500">Confirmed</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div
                                class="w-10 h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center">
                                <i class="fas fa-percentage text-sm"></i>
                            </div>
                            <div>
                                <p class="font-medium text-primary-900">Interest Payment</p>
                                <p class="text-sm text-primary-500">May 1, 2025</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-blue-600">+$12.25</p>
                            <p class="text-xs text-primary-500">Confirmed</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div
                                class="w-10 h-10 bg-green-100 text-green-600 rounded-full flex items-center justify-center">
                                <i class="fas fa-plus text-sm"></i>
                            </div>
                            <div>
                                <p class="font-medium text-primary-900">Monthly Deposit</p>
                                <p class="text-sm text-primary-500">Apr 15, 2025</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-green-600">+$250.00</p>
                            <p class="text-xs text-primary-500">Confirmed</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div
                                class="w-10 h-10 bg-yellow-100 text-yellow-600 rounded-full flex items-center justify-center">
                                <i class="fas fa-clock text-sm"></i>
                            </div>
                            <div>
                                <p class="font-medium text-primary-900">Pending Deposit</p>
                                <p class="text-sm text-primary-500">May 20, 2025</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-yellow-600">+$250.00</p>
                            <p class="text-xs text-primary-500">Pending</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-primary-900">Account Information</h3>
            </div>

            <div class="p-6">
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-primary-900">Member ID</p>
                            <p class="text-sm text-primary-500">{{ Auth::guard('member')->user()->member_id }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-medium text-primary-900">Join Date</p>
                            <p class="text-sm text-primary-500">
                                {{ Auth::guard('member')->user()->created_at->format('F j, Y') }}</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-primary-900">Account Status</p>
                            @if (Auth::guard('member')->user()->status === 1)
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Active
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Inactive
                                </span>
                            @endif
                        </div>
                        <div class="text-right">
                            <p class="font-medium text-primary-900">Next Payment Due</p>
                            <p class="text-sm text-primary-500">
                                {{ Auth::guard('member')->user()->created_at->addMonths(1)->format('F j, Y') }}</p>
                        </div>
                    </div>

                    <div class="bg-accent-50 rounded-lg p-4">
                        <div class="flex items-center space-x-3">
                            <div
                                class="w-10 h-10 bg-accent-100 text-accent-600 rounded-full flex items-center justify-center">
                                <i class="fas fa-info text-sm"></i>
                            </div>
                            <div>
                                <p class="font-medium text-accent-800">Payment Reminder</p>
                                <p class="text-sm text-accent-600">Your next monthly payment of $250 is due in 10
                                    days.</p>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('member.payment.create') }}" method="GET"
                        class="pt-4 border-t border-gray-200">
                        <button
                            class="w-full bg-accent-500 text-white px-4 py-3 rounded-lg font-medium hover:bg-accent-600 transition duration-300">
                            <i class="fas fa-upload mr-2"></i>Submit Payment Proof
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-8 bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <h3 class="text-lg font-semibold text-primary-900 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <button
                class="flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition duration-300">
                <div
                    class="w-12 h-12 bg-accent-100 text-accent-600 rounded-full flex items-center justify-center mb-2">
                    <i class="fas fa-download"></i>
                </div>
                <span class="text-sm font-medium text-primary-900">Download Statement</span>
            </button>

            <button
                class="flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition duration-300">
                <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mb-2">
                    <i class="fas fa-calculator"></i>
                </div>
                <span class="text-sm font-medium text-primary-900">Savings Calculator</span>
            </button>

            <button
                class="flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition duration-300">
                <div
                    class="w-12 h-12 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center mb-2">
                    <i class="fas fa-chart-line"></i>
                </div>
                <span class="text-sm font-medium text-primary-900">View Reports</span>
            </button>

            <button
                class="flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition duration-300">
                <div class="w-12 h-12 bg-green-100 text-green-600 rounded-full flex items-center justify-center mb-2">
                    <i class="fas fa-headset"></i>
                </div>
                <span class="text-sm font-medium text-primary-900">Contact Support</span>
            </button>
        </div>
    </div>
</x-member.layout.app>
