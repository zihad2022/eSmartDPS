<x-admin.layout.app>
    @php
        $client = $invoice->client;
        $settings = \App\Models\AdminSetting::select(
            'site_name',
            'office_address',
            'email_address',
            'helpline_number',
            'currency'
        )->first();
        $currency = $settings->currency ?? '$';
    @endphp

    <x-slot:title>Invoice #{{ $invoice->invoice_number }}</x-slot:title>

    <x-breadcrumb :items="[
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Invoices', 'url' => route('admin.invoices.index')],
        ['label' => $invoice->invoice_number, 'url' => '#'],
    ]" />


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


    <div class="max-w-5xl mx-auto my-12 p-6 bg-white rounded-2xl shadow-lg border border-accent-200">
        
        {{-- Header --}}
        <div class="flex flex-col md:flex-row justify-between items-center border-b border-accent-100 pb-4 mb-6 relative">
            <div>
                <h2 class="text-3xl font-bold text-accent-900 mb-1">Invoice</h2>
                <p class="text-gray-600 text-sm">Invoice #: <span class="font-mono">{{ $invoice->invoice_number }}</span></p>
                <p class="text-gray-500 text-sm">Issued: {{ $invoice->created_at->format('M d, Y') }}</p>
                <p class="text-gray-500 text-sm">Due: {{ $invoice->due_date->format('M d, Y') }}</p>
            </div>

            {{-- Status Seal --}}
            <div class="absolute top-4 right-4">
                @if ($invoice->status == \App\Enums\InvoiceStatus::PAID)
                    <div class="px-4 py-2 border-2 border-green-400 text-green-600 font-semibold text-sm uppercase rounded-full shadow-sm bg-green-50">
                        Paid
                    </div>
                @elseif ($invoice->status == \App\Enums\InvoiceStatus::UNPAID)
                    <div class="px-4 py-2 border-2 border-red-400 text-red-600 font-semibold text-sm uppercase rounded-full shadow-sm bg-red-50">
                        Unpaid
                    </div>
                @elseif ($invoice->status == \App\Enums\InvoiceStatus::CANCELLED)
                    <div class="px-4 py-2 border-2 border-gray-400 text-gray-600 font-semibold text-sm uppercase rounded-full shadow-sm bg-gray-50">
                        Cancelled
                    </div>
                @elseif ($invoice->status == \App\Enums\InvoiceStatus::REFUNDED)
                    <div class="px-4 py-2 border-2 border-blue-400 text-blue-600 font-semibold text-sm uppercase rounded-full shadow-sm bg-blue-50">
                        Refunded
                    </div>
                @endif
            </div>
        </div>

        {{-- From / To --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 bg-accent-50 p-4 rounded-lg shadow-sm">
            <div>
                <h3 class="text-lg font-semibold text-accent-900 mb-2">From</h3>
                <p class="text-gray-700 font-medium">{{ $settings->site_name }}</p>
                <p class="text-gray-600 text-sm">{{ $settings->office_address }}</p>
                <p class="text-gray-600 text-sm">{{ $settings->email_address }}</p>
                <p class="text-gray-600 text-sm">{{ $settings->helpline_number }}</p>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-accent-900 mb-2">Bill To</h3>
                <p class="text-gray-700 font-medium">{{ $client->first_name }} {{ $client->last_name }}</p>
                <p class="text-gray-600 text-sm">{{ $client->email }}</p>
                <p class="text-gray-600 text-sm">{{ $client->address }}, {{ $client->district }}, {{ $client->division }}</p>
                <p class="text-gray-600 text-sm">{{ $client->phone ?? '-' }}</p>
            </div>
        </div>

        {{-- Package & Amount --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-accent-100 p-4 rounded-lg shadow-sm border border-accent-200">
                <h3 class="text-gray-800 font-semibold mb-2">Package Details</h3>
                <p class="text-gray-700 font-medium">{{ $invoice->package_name }}</p>
                <p class="text-gray-600 text-sm">{{ $invoice->package_description }}</p>
            </div>
            <div class="bg-accent-100 p-4 rounded-lg shadow-sm flex justify-between items-center border border-accent-200">
                <span class="text-gray-700 font-medium">Total Amount</span>
                <span class="text-accent-900 font-bold text-xl">{{ $currency }} {{ number_format($invoice->invoice_amount, 2) }}</span>
            </div>
        </div>

        {{-- Payment Info --}}
        @if ($invoice->status == \App\Enums\InvoiceStatus::PAID)
            <div class="bg-accent-50 p-4 rounded-lg shadow-sm mb-6 border border-accent-200">
                <h3 class="text-accent-900 font-semibold mb-2">Payment Information</h3>
                <p class="text-gray-700 text-sm">Paid At: {{ $invoice->paid_at?->format('M d, Y h:i A') ?? '-' }}</p>
                <p class="text-gray-700 text-sm">Method: {{ $invoice->payment_method ?? '-' }}</p>
                <p class="text-gray-700 text-sm">Transaction ID: {{ $invoice->trx_id ?? '-' }}</p>
            </div>
        @endif

        {{-- Notes --}}
        <div class="bg-gray-50 p-4 rounded-lg shadow-sm border border-accent-200">
            <h3 class="text-accent-900 font-semibold mb-2">Notes</h3>
            <p class="text-sm text-gray-600">{{ $invoice->notes ?? 'Thank you for your business. Payment is due within 30 days.' }}</p>
        </div>

        {{-- Actions --}}
        <div class="flex justify-end mt-6 space-x-4">
            <button onclick="window.print()" 
                class="bg-accent-500 hover:bg-accent-600 text-white px-5 py-2 rounded-lg font-medium shadow-sm transition">
                <i class="fas fa-print mr-2"></i> Print
            </button>
            <form action="{{ route('admin.invoices.send', $invoice->id) }}" method="POST">
                @csrf
                <button type="submit"
                    class="bg-green-500 hover:bg-green-600 text-white px-5 py-2 rounded-lg font-medium shadow-sm transition">
                    <i class="fas fa-paper-plane mr-2"></i> Send to Client
                </button>
            </form>
        </div>
    </div>
</x-admin.layout.app>
