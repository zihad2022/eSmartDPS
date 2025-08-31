<x-admin.layout.app>
    @php
        // ================================
        // Dynamic Page Setup
        // ================================

        // Map status query param to a page title
        $titleMap = [
            'paid' => 'Paid Invoices',
            'unpaid' => 'Unpaid Invoices',
            'refunded' => 'Refunded Invoices',
            'cancelled' => 'Cancelled Invoices',
        ];

        $status = request()->status;
        $pageTitle = $titleMap[$status] ?? 'All Invoices';

        // Breadcrumbs
        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['label' => $pageTitle, 'url' => route('admin.invoices.index', $status ? ['status' => $status] : [])],
        ];

        // Fetch currency setting once (to avoid repeated DB calls)
        $settings = \App\Models\AdminSetting::select('currency')->first();
    @endphp

    {{-- Page Title --}}
    <x-slot:title>{{ $pageTitle }}</x-slot:title>

    {{-- Breadcrumb --}}
    <x-breadcrumb :items="$breadcrumbItems" />

    <div>
        {{-- Flash Messages --}}
        @if (session('success') || session('error'))
            <x-flash-message 
                :type="session('success') ? 'success' : 'error'" 
                :title="session('success') ? 'Success' : 'Error'" 
                :message="session('success') ?? session('error')" 
            />
        @endif

        {{-- Stats Cards --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 mb-6">
            <x-card.stat-card 
                label="Total Invoices" 
                :value="number_format($invoices->total())" 
                iconBgColor="bg-primary-100"
                iconTextColor="text-primary-600" 
                icon="fas fa-file-invoice-dollar" 
            />
        </div>

        {{-- Invoices Table --}}
        <div class="bg-white rounded-xl shadow-sm">

            {{-- Table Header --}}
            <div class="p-6 border-b border-gray-200 flex flex-col md:flex-row md:items-center md:justify-between">
                <h3 class="text-lg font-semibold text-primary-900 mb-4 md:mb-0">
                    {{ $pageTitle }}
                </h3>
                <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-4">
                    {{-- Add Invoice --}}
                    <a href="{{ route('admin.invoices.create') }}"
                        class="bg-accent-500 hover:bg-accent-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                        Add Invoice
                    </a>
                    {{-- Export Invoices --}}
                    <a href="{{ route('admin.invoices.export', ['status' => $status]) }}"
                        class="bg-gray-100 hover:bg-gray-200 text-primary-700 px-4 py-2 rounded-lg text-sm font-medium transition">
                        <i class="fas fa-download mr-2"></i>Export
                    </a>
                </div>
            </div>

            {{-- Table Body --}}
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase">SL</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase">Invoice No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase">Client</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase">Payment Method</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase">Created At</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($invoices as $index => $invoice)
                            <tr class="hover:bg-gray-50">
                                {{-- Serial No --}}
                                <td class="px-6 py-4 text-sm text-primary-900">
                                    #{{ $index + $invoices->firstItem() }}
                                </td>
                                {{-- Invoice Number --}}
                                <td class="px-6 py-4 text-sm text-primary-900 font-mono">
                                    {{ $invoice->invoice_number }}
                                </td>
                                {{-- Client Name --}}
                                <td class="px-6 py-4 text-sm text-primary-700">
                                    {{ $invoice->client->first_name }} {{ $invoice->client->last_name }}
                                </td>
                                {{-- Amount --}}
                                <td class="px-6 py-4 text-sm text-primary-900 font-semibold">
                                    {{ $settings->currency }} {{ number_format($invoice->invoice_amount) }}
                                </td>
                                {{-- Status (color + label from Enum methods) --}}
                                <td class="px-6 py-4">
                                    <span
                                        class="px-2 py-1 text-xs font-medium rounded {{ $invoice->status->bgColor() }} {{ $invoice->status->color() }}">
                                        {{ $invoice->status->label() }}
                                    </span>
                                </td>
                                {{-- Payment Method --}}
                                <td class="px-6 py-4 text-sm text-primary-700">
                                    {{ $invoice->payment_method?->label() ?? 'N/A' }}
                                </td>
                                {{-- Created At --}}
                                <td class="px-6 py-4 text-sm text-primary-600">
                                    {{ $invoice->created_at->format('M d, Y') }}
                                </td>
                                {{-- Actions --}}
                                <td class="px-6 py-4 text-sm font-medium">
                                    <div class="flex space-x-2">
                                        {{-- View --}}
                                        <a href="{{ route('admin.invoices.show', $invoice->id) }}"
                                            class="text-accent-600 hover:text-accent-900" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        {{-- Edit --}}
                                        <a href="{{ route('admin.invoices.edit', $invoice->id) }}"
                                            class="text-secondary-600 hover:text-secondary-900" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        {{-- Delete --}}
                                        <form method="POST"
                                            action="{{ route('admin.invoices.destroy', $invoice->id) }}"
                                            class="delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="text-red-600 hover:text-red-900 delete-btn"
                                                title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                                <x-confirm-modal />
                            </tr>
                        @empty
                            {{-- No invoices found --}}
                            <tr>
                                <td colspan="8" class="text-center py-4 text-sm text-gray-500">No invoices found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                <div class="text-sm text-primary-600">
                    @if ($invoices->total() > 0)
                        Showing {{ $invoices->firstItem() }} to {{ $invoices->lastItem() }} of {{ $invoices->total() }} results
                    @else
                        No results found.
                    @endif
                </div>
                <x-pagination :paginator="$invoices" />
            </div>
        </div>
    </div>
</x-admin.layout.app>
