<x-client.layout.app>
    <div class="max-w-7xl mx-auto mt-10 p-6">
        <!-- Page Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
            <h1 class="text-2xl font-bold text-gray-800 mb-2 md:mb-0">Payment Details</h1>
        </div>

        <!-- Main Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-gray-50 border-l-4 border-green-500 p-4 rounded-lg shadow hover:shadow-lg transition">
                <p class="text-sm text-gray-500 font-medium">Payment ID</p>
                <h3 class="text-lg font-semibold text-gray-800">PAY-123456</h3>
            </div>
            <div class="bg-gray-50 border-l-4 border-green-500 p-4 rounded-lg shadow hover:shadow-lg transition">
                <p class="text-sm text-gray-500 font-medium">Member</p>
                <h3 class="text-lg font-semibold text-gray-800">John Doe (M-001)</h3>
            </div>
            <div class="bg-gray-50 border-l-4 border-green-500 p-4 rounded-lg shadow hover:shadow-lg transition">
                <p class="text-sm text-gray-500 font-medium">Amount</p>
                <h3 class="text-lg font-semibold text-gray-800">$1,200.00 USD</h3>
            </div>
        </div>

        <!-- Details Section -->
        <div class="bg-white rounded-xl shadow p-6 mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div class="bg-gray-50 p-4 rounded-lg shadow">
                    <p class="text-sm text-gray-500 font-medium">Payment Method</p>
                    <h3 class="text-lg font-semibold text-gray-800">Bank Transfer</h3>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg shadow">
                    <p class="text-sm text-gray-500 font-medium">Status</p>
                    <span class="inline-block px-3 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-700">Paid</span>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg shadow">
                    <p class="text-sm text-gray-500 font-medium">Transaction ID</p>
                    <h3 class="text-lg font-semibold text-gray-800">TXN-987654</h3>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg shadow">
                    <p class="text-sm text-gray-500 font-medium">Reference</p>
                    <h3 class="text-lg font-semibold text-gray-800">INV-2025</h3>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg shadow">
                    <p class="text-sm text-gray-500 font-medium">Paid At</p>
                    <h3 class="text-lg font-semibold text-gray-800">Sep 05, 2025 10:00 AM</h3>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg shadow">
                    <p class="text-sm text-gray-500 font-medium">Due Date</p>
                    <h3 class="text-lg font-semibold text-gray-800">Sep 10, 2025</h3>
                </div>
            </div>

            <!-- Notes / Meta -->
            <div class="mt-6 bg-gray-50 p-4 rounded-lg shadow md:col-span-2">
                <p class="text-sm text-gray-500 font-medium">Notes / Meta</p>
                <pre class="text-sm text-gray-800 bg-white p-2 rounded">{"note": "Payment received in full."}</pre>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-end space-x-4">
            <a href="#"
               class="px-4 py-2 border border-gray-400 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition">Back</a>
            <a href="#"
               class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium transition">Edit</a>
        </div>
    </div>
</x-client.layout.app>
