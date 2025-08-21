<x-admin.layout.app>
    @php
        // Title Map for dynamic page titles
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

        // Currency setting (fetch once to avoid repeated queries)
        $settings = \App\Models\AdminSetting::select('currency')->first();

        // Status label & colors mapping
        // $statusColors = [
        //     \App\Enums\InvoiceStatus::UNPAID->value => [
        //         'label' => \App\Enums\InvoiceStatus::UNPAID->label(),
        //         'color' => 'red',
        //     ],
        //     \App\Enums\InvoiceStatus::PAID->value => [
        //         'label' => \App\Enums\InvoiceStatus::PAID->label(),
        //         'color' => 'green',
        //     ],
        //     \App\Enums\InvoiceStatus::REFUND_REQUESTED->value => [
        //         'label' => \App\Enums\InvoiceStatus::REFUND_REQUESTED->label(),
        //         'color' => 'yellow',
        //     ],
        //     \App\Enums\InvoiceStatus::REFUNDED->value => [
        //         'label' => \App\Enums\InvoiceStatus::REFUNDED->label(),
        //         'color' => 'blue',
        //     ],
        //     \App\Enums\InvoiceStatus::CANCELLED->value => [
        //         'label' => \App\Enums\InvoiceStatus::CANCELLED->label(),
        //         'color' => 'gray',
        //     ],
        // ];

    @endphp

    <x-slot:title>{{ $pageTitle }}</x-slot:title>
    <x-breadcrumb :items="$breadcrumbItems" />

    <div>
        <!-- Flash Messages -->
        @if (session('success') || session('error'))
            <x-flash-message :type="session('success') ? 'success' : 'error'" :title="session('success') ? 'Success' : 'Error'" :message="session('success') ?? session('error')" />
        @endif

        <!-- Invoices Table -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 mb-6">
            <x-card.stat-card label="Total Invoices" :value="number_format($invoices->total())" iconBgColor="bg-primary-100"
                iconTextColor="text-primary-600" icon="fas fa-file-invoice-dollar" />
        </div>

        <div class="bg-white rounded-xl shadow-sm">
            <!-- Table Header -->
            <div class="p-6 border-b border-gray-200 flex flex-col md:flex-row md:items-center md:justify-between">
                <h3 class="text-lg font-semibold text-primary-900 mb-4 md:mb-0">
                    {{ $pageTitle }}
                </h3>
                <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-4">
                    <a href="{{ route('admin.invoices.create') }}"
                        class="bg-accent-500 hover:bg-accent-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                        Add Invoice
                    </a>
                    <a href="{{ route('admin.invoices.export', ['status' => $status]) }}"
                        class="bg-gray-100 hover:bg-gray-200 text-primary-700 px-4 py-2 rounded-lg text-sm font-medium transition">
                        <i class="fas fa-download mr-2"></i>Export
                    </a>
                </div>
            </div>

            <!-- Table Body -->
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase">SL</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase">Invoice No
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase">Client</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase">Payment
                                Method</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase">Created At
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($invoices as $index => $invoice)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-primary-900">
                                    #{{ $index + $invoices->firstItem() }}
                                </td>
                                <td class="px-6 py-4 text-sm text-primary-900 font-mono">
                                    {{ $invoice->invoice_number }}
                                </td>
                                <td class="px-6 py-4 text-sm text-primary-700">
                                    {{ $invoice->client->first_name }} {{ $invoice->client->last_name }}
                                </td>
                                <td class="px-6 py-4 text-sm text-primary-900 font-semibold">
                                    {{ $settings->currency }} {{ $invoice->invoice_amount }}
                                </td>
                                <td class="px-6 py-4">
                                    {{-- @php
                                        $status = $statusColors[$invoice->status->value] ?? [
                                            'label' => 'Unknown',
                                            'color' => 'gray',
                                        ];
                                    @endphp --}}
                                    {{-- <span
                                        class="px-2 py-1 text-xs rounded-full bg-{{ $status['color'] }}-100 text-{{ $status['color'] }}-800 font-medium">
                                        {{ $status['label'] }}
                                    </span> --}}
                                    <span
                                        class="px-2 py-1 text-xs font-medium rounded {{ $invoice->status->bgColor() }} {{ $invoice->status->color() }}">
                                        {{ $invoice->status->label() }}
                                    </span>

                                </td>
                                <td class="px-6 py-4 text-sm text-primary-700">
                                    {{ ucfirst($invoice->payment_method ?? '-') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-primary-600">
                                    {{ $invoice->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('admin.invoices.show', $invoice->id) }}"
                                            class="text-accent-600 hover:text-accent-900" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.invoices.edit', $invoice->id) }}"
                                            class="text-secondary-600 hover:text-secondary-900" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
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
                            <tr>
                                <td colspan="8" class="text-center py-4 text-sm text-gray-500">No invoices found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                <div class="text-sm text-primary-600">
                    @if ($invoices->total() > 0)
                        Showing {{ $invoices->firstItem() }} to {{ $invoices->lastItem() }} of
                        {{ $invoices->total() }} results
                    @else
                        No results found.
                    @endif
                </div>
                <x-pagination :paginator="$invoices" />
            </div>
        </div>
    </div>
</x-admin.layout.app>
