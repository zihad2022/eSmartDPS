<x-admin.layout.app>
    <x-slot:title>Invoice #INV-2023-0872</x-slot:title>

    <x-breadcrumb :items="[
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Invoices', 'url' => route('admin.invoices.index')],
        ['label' => 'INV-2023-0872', 'url' => '#'],
    ]" />

    <!-- Main Content -->
    <div class="bg-white rounded-xl shadow-sm">
        <!-- Invoice Header with title and action buttons -->
        <div class="p-6 border-b border-gray-200">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <h3 class="text-lg font-semibold text-primary-900 mb-4 md:mb-0">
                    Invoice #INV-2023-0872
                </h3>

                <!-- Action buttons -->
                <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-4">
                    <button
                        class="bg-accent-500 hover:bg-accent-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-300">
                        <i class="fas fa-print mr-2"></i>Print
                    </button>
                    <button
                        class="bg-gray-100 hover:bg-gray-200 text-primary-700 px-4 py-2 rounded-lg text-sm font-medium transition duration-300">
                        <i class="fas fa-download mr-2"></i>Download PDF
                    </button>
                    <button
                        class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-300">
                        <i class="fas fa-paper-plane mr-2"></i>Send to Client
                    </button>
                </div>
            </div>
        </div>

        <!-- Invoice Details -->
        <div class="p-6 border-b border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- From (Company Info) -->
                <div>
                    <h4 class="text-sm font-medium text-primary-500 uppercase mb-2">From</h4>
                    <p class="text-primary-900 font-semibold">Your Company Name</p>
                    <p class="text-primary-600">123 Business Avenue</p>
                    <p class="text-primary-600">New York, NY 10001</p>
                    <p class="text-primary-600">contact@yourcompany.com</p>
                    <p class="text-primary-600">(555) 123-4567</p>
                </div>

                <!-- To (Client Info) -->
                <div>
                    <h4 class="text-sm font-medium text-primary-500 uppercase mb-2">Bill To</h4>
                    <p class="text-primary-900 font-semibold">Sarah Johnson</p>
                    <p class="text-primary-600">Acme Corporation</p>
                    <p class="text-primary-600">456 Client Street</p>
                    <p class="text-primary-600">San Francisco, CA 94103</p>
                    <p class="text-primary-600">sarah@acmecorp.com</p>
                </div>

                <!-- Invoice Metadata -->
                <div>
                    <h4 class="text-sm font-medium text-primary-500 uppercase mb-2">Invoice Details</h4>
                    <div class="flex justify-between mb-1">
                        <span class="text-primary-600">Invoice Number:</span>
                        <span class="text-primary-900 font-medium">INV-2023-0872</span>
                    </div>
                    <div class="flex justify-between mb-1">
                        <span class="text-primary-600">Issue Date:</span>
                        <span class="text-primary-900">Nov 15, 2023</span>
                    </div>
                    <div class="flex justify-between mb-1">
                        <span class="text-primary-600">Due Date:</span>
                        <span class="text-primary-900 font-medium">Dec 15, 2023</span>
                    </div>
                    <div class="flex justify-between mb-1">
                        <span class="text-primary-600">Status:</span>
                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Paid</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Invoice Items Table -->
        <div class="p-6 border-b border-gray-200">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Item</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Description</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Quantity</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Unit Price</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Amount</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td class="px-6 py-4 text-sm text-primary-900">Website Design</td>
                            <td class="px-6 py-4 text-sm text-primary-600">Homepage and product pages redesign</td>
                            <td class="px-6 py-4 text-sm text-primary-600">1</td>
                            <td class="px-6 py-4 text-sm text-primary-600">$1,200.00</td>
                            <td class="px-6 py-4 text-sm text-primary-600">$1,200.00</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 text-sm text-primary-900">Frontend Development</td>
                            <td class="px-6 py-4 text-sm text-primary-600">React implementation</td>
                            <td class="px-6 py-4 text-sm text-primary-600">20</td>
                            <td class="px-6 py-4 text-sm text-primary-600">$85.00</td>
                            <td class="px-6 py-4 text-sm text-primary-600">$1,700.00</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 text-sm text-primary-900">Backend Integration</td>
                            <td class="px-6 py-4 text-sm text-primary-600">API development and database design</td>
                            <td class="px-6 py-4 text-sm text-primary-600">15</td>
                            <td class="px-6 py-4 text-sm text-primary-600">$95.00</td>
                            <td class="px-6 py-4 text-sm text-primary-600">$1,425.00</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 text-sm text-primary-900">Project Management</td>
                            <td class="px-6 py-4 text-sm text-primary-600">Weekly coordination and reporting</td>
                            <td class="px-6 py-4 text-sm text-primary-600">8</td>
                            <td class="px-6 py-4 text-sm text-primary-600">$75.00</td>
                            <td class="px-6 py-4 text-sm text-primary-600">$600.00</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Invoice Summary -->
        <div class="p-6">
            <div class="flex justify-end">
                <div class="w-full md:w-1/3">
                    <div class="flex justify-between mb-2">
                        <span class="text-primary-600">Subtotal:</span>
                        <span class="text-primary-900">$4,925.00</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="text-primary-600">Tax (10%):</span>
                        <span class="text-primary-900">$492.50</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="text-primary-600">Discount (5%):</span>
                        <span class="text-primary-900">-$246.25</span>
                    </div>
                    <div class="flex justify-between pt-4 border-t border-gray-200 font-semibold text-lg">
                        <span class="text-primary-900">Total:</span>
                        <span class="text-primary-900">$5,171.25</span>
                    </div>
                    <div class="flex justify-between mt-1">
                        <span class="text-primary-600 text-sm">Amount Paid:</span>
                        <span class="text-green-600 text-sm font-medium">$5,171.25</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Invoice Footer Notes -->
        <div class="p-6 border-t border-gray-200 bg-gray-50 rounded-b-xl">
            <h4 class="text-sm font-medium text-primary-500 uppercase mb-2">Notes</h4>
            <p class="text-sm text-primary-600">Thank you for your business. Payment is due within 30 days of invoice
                date. Please make checks payable to Your Company Name and mail to 123 Business Avenue, New York, NY
                10001.</p>
            <p class="text-sm text-primary-600 mt-2">Late payments are subject to fees of 1.5% per month.</p>
        </div>
    </div>
</x-admin.layout.app>
