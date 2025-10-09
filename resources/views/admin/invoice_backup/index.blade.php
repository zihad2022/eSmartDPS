<x-admin.layout.app>
    @php
        /**
         * =======================================
         * Page Setup: Status, Title, Breadcrumbs
         * =======================================
         */

        // 1. Retrieve status filter from the request (?status=paid/unpaid/refunded/cancelled)
        $status = request()->status;

        // 2. Map status values to human-readable titles
        $titleMap = [
            'paid' => 'Paid Invoices',
            'unpaid' => 'Unpaid Invoices',
            'refunded' => 'Refunded Invoices',
            'cancelled' => 'Cancelled Invoices',
        ];

        // 3. Determine page title based on filter, fallback to "All Invoices"
        $pageTitle = $titleMap[$status] ?? 'All Invoices';

        // 4. Base breadcrumb items
        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['label' => $pageTitle, 'url' => route('admin.invoices.index', $status ? ['status' => $status] : [])],
        ];

        // 5. Fetch currency setting once to avoid repeated DB calls
        $settings = \App\Models\AdminSetting::select('currency')->first();
    @endphp

    {{-- ===========================
         Set HTML Page Title
    ============================ --}}
    <x-slot:title>{{ $pageTitle }}</x-slot:title>

    {{-- ===========================
         Breadcrumb Navigation
    ============================ --}}
    <x-breadcrumb :items="$breadcrumbItems" />

    {{-- ===========================
         Main Content Wrapper
    ============================ --}}
    <div>

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

        {{-- ===========================
             Stats Cards Section
        ============================ --}}
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 md:gap-6 mb-6">
            {{-- Total Invoices --}}
            <x-card.stat-card
                :label="'Total Invoices'"
                :value="number_format($totalInvoices)"
                :iconBgColor="'bg-primary-100'"
                :iconTextColor="'text-primary-600'"
                :icon="'fas fa-file-invoice-dollar'"
            />
            {{-- Paid Invoices --}}
            <x-card.stat-card
                :label="'Paid Invoices'"
                :value="number_format($totalPaidInvoices)"
                :iconBgColor="'bg-green-100'"
                :iconTextColor="'text-green-600'"
                :icon="'fas fa-file-invoice-dollar'"
            />
            {{-- Unpaid Invoices --}}
            <x-card.stat-card
                :label="'Unpaid Invoices'"
                :value="number_format($totalUnpaidInvoices)"
                :iconBgColor="'bg-red-100'"
                :iconTextColor="'text-red-600'"
                :icon="'fas fa-file-invoice-dollar'"
            />
            {{-- Refunded Invoices --}}
            <x-card.stat-card
                :label="'Refunded Invoices'"
                :value="number_format($totalRefundedInvoices)"
                :iconBgColor="'bg-yellow-100'"
                :iconTextColor="'text-yellow-600'"
                :icon="'fas fa-file-invoice-dollar'"
            />
            {{-- Cancelled Invoices --}}
            <x-card.stat-card
                :label="'Cancelled Invoices'"
                :value="number_format($totalCancelledInvoices)"
                :iconBgColor="'bg-gray-100'"
                :iconTextColor="'text-gray-600'"
                :icon="'fas fa-file-invoice-dollar'"
            />
        </div>

        {{-- ===========================
             Invoices Table Section
        ============================ --}}
        <div class="bg-white rounded-xl shadow-sm">

            {{-- ====================================
                 Table Header: Title + Actions
            ==================================== --}}
            <div class="p-6 border-b border-gray-200 flex flex-col md:flex-row md:items-center md:justify-between">
                {{-- Section Title --}}
                <h3 class="text-lg font-semibold text-primary-900 mb-4 md:mb-0">
                    {{ $pageTitle }}
                </h3>

                {{-- Table Actions (Search + Add + Export) --}}
                <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-4">

                     {{-- Search Form --}}
                     <form method="GET" action="{{ route('admin.invoices.index') }}" class="relative w-full md:w-auto">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search invoices..."
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-accent-500 focus:border-accent-500 transition duration-300">
                        <button type="submit"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-accent-500 transition">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>

                    {{-- Add Invoice --}}
                    <a href="{{ route('admin.invoices.create') }}"
                        class="bg-accent-500 hover:bg-accent-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                        Add Invoice
                    </a>

                    {{-- Export Invoice --}}
                    <a href="{{ route('admin.invoices.export', ['status' => $status]) }}"
                        class="bg-gray-100 hover:bg-gray-200 text-primary-700 px-4 py-2 rounded-lg text-sm font-medium transition">
                        <i class="fas fa-download mr-2"></i>Export
                    </a>
                </div>
            </div>

            {{-- =============================
                 Table Body
            ============================= --}}
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    {{-- Table Headers --}}
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase">SL</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase">Invoice No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase">Client Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase">Package</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase">Payment Method</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase">Due Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase">Paid At</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase">Generated Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase">Actions</th>
                        </tr>
                    </thead>

                    {{-- Table Rows --}}
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($invoices as $index => $invoice)
                            <tr class="hover:bg-gray-50">
                                {{-- Serial Number --}}
                                <td class="px-6 py-4 text-sm text-primary-900">#{{ $index + $invoices->firstItem() }}</td>

                                {{-- Invoice Number --}}
                                <td class="px-6 py-4 text-sm text-primary-900 font-mono">{{ $invoice->invoice_number }}</td>

                                {{-- Client Name --}}
                                <td class="px-6 py-4 text-sm text-primary-700">{{ $invoice->client->first_name }} {{ $invoice->client->last_name }}</td>

                                {{-- Package --}}
                                <td class="px-6 py-4 text-sm text-primary-900 font-semibold">{{ $invoice->package_name }}</td>

                                {{-- Amount --}}
                                <td class="px-6 py-4 text-sm text-primary-900 font-mono">{{ $settings->currency }} {{ number_format($invoice->invoice_amount) }}</td>

                                {{-- Payment Method --}}
                                <td class="px-6 py-4 text-sm text-primary-700">{{ $invoice->payment_method?->label() ?? 'N/A' }}</td>

                                {{-- Status Badge --}}
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $invoice->status->bgColor() }} {{ $invoice->status->color() }}">
                                        {{ $invoice->status->label() }}
                                    </span>
                                </td>

                                {{-- Due Date --}}
                                <td class="px-6 py-4 text-sm text-primary-600">
                                    @php
                                        $isPastDue = $invoice->due_date && $invoice->due_date->isPast() && !$invoice->paid_at;
                                    @endphp
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $isPastDue ? 'text-red-600 bg-red-100' : 'text-green-600 bg-green-100' }}">
                                        {{ $invoice->due_date?->format('M d, Y') ?? '-' }}
                                    </span>
                                </td>

                                {{-- Paid At --}}
                                <td class="px-6 py-4 text-sm text-primary-600">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $invoice->paid_at ? 'text-green-600 bg-green-100' : 'text-gray-600 bg-gray-100' }}">
                                        {{ $invoice->paid_at?->format('M d, Y') ?? '-' }}
                                    </span>
                                </td>

                                {{-- Generated Date --}}
                                <td class="px-6 py-4 text-sm text-primary-600">{{ $invoice->created_at->format('M d, Y') }}</td>

                                {{-- Action Buttons --}}
                                <td class="px-6 py-4 text-sm font-medium">
                                    <div class="flex space-x-2">
                                        {{-- View --}}
                                        <a href="{{ route('admin.invoices.show', $invoice->id) }}" class="text-accent-600 hover:text-accent-900" title="View"><i class="fas fa-eye"></i></a>
                                        {{-- Edit --}}
                                        <a href="{{ route('admin.invoices.edit', $invoice->id) }}" class="text-secondary-600 hover:text-secondary-900" title="Edit"><i class="fas fa-edit"></i></a>
                                        {{-- Delete --}}
                                        <form method="POST" action="{{ route('admin.invoices.destroy', $invoice->id) }}" class="delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="text-red-600 hover:text-red-900 delete-btn" title="Delete"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                    <x-confirm-modal />
                                </td>
                            </tr>
                        @empty
                            {{-- Empty State --}}
                            <tr>
                                <td colspan="11" class="text-center py-4 text-sm text-gray-500">No invoices found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ===============================
                 Pagination & Results Info
            =============================== --}}
            <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                {{-- Results Info --}}
                <div class="text-sm text-primary-600">
                    @if ($invoices->total() > 0)
                        Showing {{ $invoices->firstItem() }} to {{ $invoices->lastItem() }} of {{ $invoices->total() }} results
                    @else
                        No results found.
                    @endif
                </div>

                {{-- Pagination Links --}}
                <x-pagination :paginator="$invoices" />
            </div>
        </div>
    </div>
</x-admin.layout.app>
