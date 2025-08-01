<x-client.layout.app>
    <div class="max-w-5xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-primary-900 mb-1">Real Estate Investment</h2>
                    <p class="text-sm text-primary-600">Property Development</p>
                </div>
                <span class="px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                    Active
                </span>
            </div>
        </div>

        <!-- Project Info Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-sm font-medium text-primary-600 mb-2">Investment Amount</h3>
                <p class="text-xl font-semibold text-primary-900">$15,000.00</p>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-sm font-medium text-primary-600 mb-2">Expected Return</h3>
                <p class="text-xl font-semibold text-green-600">15%</p>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-sm font-medium text-primary-600 mb-2">Start Date</h3>
                <p class="text-lg font-semibold text-primary-900">January 15, 2025</p>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-sm font-medium text-primary-600 mb-2">End Date</h3>
                <p class="text-lg font-semibold text-primary-900">December 15, 2025</p>
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <div class="flex justify-between text-sm mb-2">
                <span class="text-primary-600">Progress</span>
                <span class="font-medium text-primary-900">65%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-accent-500 h-2 rounded-full" style="width: 65%"></div>
            </div>
        </div>

        <!-- Description -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-primary-900 mb-2">Description</h3>
            <p class="text-sm text-primary-700 leading-relaxed">
                This real estate project focuses on the development of a residential complex in the suburban area. The
                investment includes land acquisition, construction, and marketing. Expected to yield strong returns due
                to rising property demand.
            </p>
        </div>
    </div>
</x-client.layout.app>
