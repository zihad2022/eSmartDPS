<x-admin.layout.app>
    @php
        // Fetch related client and invoice details
        $client = $invoice->client;

        // Fetch company settings including currency
        $settings = \App\Models\AdminSetting::select('site_name', 'office_address', 'email_address', 'helpline_number', 'currency')->first();
        $currency = $settings->currency ?? '$';
    @endphp

    {{-- Page Title --}}
    <x-slot:title>Invoice #{{ $invoice->invoice_number }}</x-slot:title>

    {{-- Breadcrumbs for navigation --}}
    <x-breadcrumb :items="[
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Invoices', 'url' => route('admin.invoices.index')],
        ['label' => $invoice->invoice_number, 'url' => '#'],
    ]" />

    {{-- Main Container --}}
    <div class="bg-white rounded-xl shadow-sm">

        {{-- Invoice Header: Title & Action Buttons --}}
        <div class="p-6 border-b border-gray-200 flex flex-col md:flex-row md:items-center md:justify-between">
            {{-- Invoice Title --}}
            <h3 class="text-lg font-semibold text-primary-900 mb-4 md:mb-0">
                Invoice #{{ $invoice->invoice_number }}
            </h3>

            {{-- Action Buttons: Print, Download PDF, Send to Client --}}
            <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-4">
                <button class="bg-accent-500 hover:bg-accent-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-300">
                    <i class="fas fa-print mr-2"></i>Print
                </button>
                <button class="bg-gray-100 hover:bg-gray-200 text-primary-700 px-4 py-2 rounded-lg text-sm font-medium transition duration-300">
                    <i class="fas fa-download mr-2"></i>Download PDF
                </button>
                <button class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-300">
                    <i class="fas fa-paper-plane mr-2"></i>Send to Client
                </button>
            </div>
        </div>

        {{-- Invoice Details Section --}}
        <div class="p-6 border-b border-gray-200 grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- From: Company Info --}}
            <div>
                <h4 class="text-sm font-medium text-primary-500 uppercase mb-2">From</h4>
                <p class="text-primary-900 font-semibold">{{ $settings->site_name }}</p>
                <p class="text-primary-600">{{ $settings->office_address }}</p>
                <p class="text-primary-600">{{ $settings->email_address }}</p>
                <p class="text-primary-600">{{ $settings->helpline_number }}</p>
            </div>

            {{-- To: Client Info --}}
            <div>
                <h4 class="text-sm font-medium text-primary-500 uppercase mb-2">Bill To</h4>
                <p class="text-primary-900 font-semibold">{{ $client->first_name }} {{ $client->last_name }}</p>
                <p class="text-primary-600">{{ $client->email }}</p>
                <p class="text-primary-600">{{ $client->address }}, {{ $client->district }}, {{ $client->division }}</p>
                @if($client->phone)
                    <p class="text-primary-600">{{ $client->phone }}</p>
                @endif
            </div>

            {{-- Invoice Metadata --}}
            <div>
                <h4 class="text-sm font-medium text-primary-500 uppercase mb-2">Invoice Details</h4>
                <div class="flex justify-between mb-1">
                    <span class="text-primary-600">Invoice Number:</span>
                    <span class="text-primary-900 font-medium">{{ $invoice->invoice_number }}</span>
                </div>
                <div class="flex justify-between mb-1">
                    <span class="text-primary-600">Issue Date:</span>
                    <span class="text-primary-900">{{ $invoice->created_at->format('M d, Y') }}</span>
                </div>
                <div class="flex justify-between mb-1">
                    <span class="text-primary-600">Due Date:</span>
                    <span class="text-primary-900 font-medium">{{ $invoice->created_at->addDays(30)->format('M d, Y') }}</span>
                </div>
                <div class="flex justify-between mb-1">
                    <span class="text-primary-600">Status:</span>
                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $invoice->status->bgColor() }} {{ $invoice->status->color() }}">
                        {{ $invoice->status->label() }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Invoice Item Table: Since only one package/item, no foreach needed --}}
        <div class="p-6 border-b border-gray-200 overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase">Package</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase">Amount</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr>
                        <td class="px-6 py-4 text-sm text-primary-900">{{ $invoice->package_name }}</td>
                        <td class="px-6 py-4 text-sm text-primary-600">{{ $invoice->package_description }}</td>
                        <td class="px-6 py-4 text-sm text-primary-900 font-semibold">{{ $currency }} {{ number_format($invoice->invoice_amount, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Invoice Summary Section --}}
        <div class="p-6">
            <div class="flex justify-end">
                <div class="w-full md:w-1/3">
                    {{-- Subtotal / Invoice Amount --}}
                    <div class="flex justify-between mb-2">
                        <span class="text-primary-600">Invoice Amount:</span>
                        <span class="text-primary-900">{{ $currency }} {{ number_format($invoice->invoice_amount, 2) }}</span>
                    </div>

                    {{-- Total Amount (can include tax/discount if added later) --}}
                    <div class="flex justify-between pt-4 border-t border-gray-200 font-semibold text-lg">
                        <span class="text-primary-900">Total:</span>
                        <span class="text-primary-900">{{ $currency }} {{ number_format($invoice->total_amount ?? $invoice->invoice_amount, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Invoice Notes Section --}}
        <div class="p-6 border-t border-gray-200 bg-gray-50 rounded-b-xl">
            <h4 class="text-sm font-medium text-primary-500 uppercase mb-2">Notes</h4>
            <p class="text-sm text-primary-600">{{ $invoice->notes ?? 'Thank you for your business. Payment is due within 30 days.' }}</p>
        </div>

    </div>
</x-admin.layout.app>
