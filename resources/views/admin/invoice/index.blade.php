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
            Invoices Table Section (component-based)
        ============================ --}}
        <x-data-table
            :page-title="$pageTitle"
            :rows="$invoices"
            :headers="['SL','Invoice No','Client Name','Package','Amount','Payment Method','Status','Paid At','Generated Date','Actions']"
            row-view="admin.invoice.partials.row"
        >
            <x-slot:actions>
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
            </x-slot:actions>
        </x-data-table>
    </div>
</x-admin.layout.app>
