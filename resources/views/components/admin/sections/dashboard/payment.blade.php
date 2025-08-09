<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Recent Payments -->
    <div class="bg-white rounded-xl shadow-sm p-6 dashboard-card">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-primary-900">Recent Payments</h3>
            <a href="payments.html" class="text-accent-600 hover:text-accent-700 text-sm font-medium">View
                All</a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="px-4 py-3 text-left text-xs font-medium text-primary-500 uppercase">Member
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-primary-500 uppercase">Amount
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-primary-500 uppercase">Status
                        </th>
                    </tr>
                </thead>
                <tbody>
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

    <!-- Active Projects -->
    {{-- <div class="bg-white rounded-xl shadow-sm p-6 dashboard-card">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-primary-900">Active Projects</h3>
            <a href="projects.html" class="text-accent-600 hover:text-accent-700 text-sm font-medium">View
                All</a>
        </div>

        <div class="space-y-4">
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex justify-between items-start">
                    <div>
                        <h4 class="text-primary-900 font-medium">Real Estate Investment</h4>
                        <p class="text-sm text-primary-600 mt-1">Expected Return: 15%</p>
                    </div>
                    <div class="bg-accent-100 text-accent-600 px-3 py-1 rounded-full text-xs font-medium">
                        Active
                    </div>
                </div>
                <div class="flex items-center mt-3 text-sm text-primary-600">
                    <i class="fas fa-dollar-sign mr-2"></i>
                    <span>$15,000 invested</span>
                </div>
            </div>

            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex justify-between items-start">
                    <div>
                        <h4 class="text-primary-900 font-medium">Market Investment</h4>
                        <p class="text-sm text-primary-600 mt-1">Expected Return: 12%</p>
                    </div>
                    <div class="bg-secondary-100 text-secondary-600 px-3 py-1 rounded-full text-xs font-medium">
                        Planning
                    </div>
                </div>
                <div class="flex items-center mt-3 text-sm text-primary-600">
                    <i class="fas fa-dollar-sign mr-2"></i>
                    <span>$8,500 invested</span>
                </div>
            </div>
        </div>
    </div> --}}
</div>
