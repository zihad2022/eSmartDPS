<x-client.layout.app>
    @php
        /**
         * =======================================
         * Page Setup: Status, Title, Breadcrumbs
         * =======================================
         */

        // 1. Retrieve status filter from the request (?status=pending/due/paid/cancelled)
        $status = request()->status;

        // 2. Map status values to human-readable titles
        $titleMap = [
            'pending' => 'Pending Payments',
            'due' => 'Due Payments',
            'paid' => 'Paid Payments',
            'cancelled' => 'Cancelled Payments',
        ];

        // 3. Determine the page title based on filter, fallback to "All Payments"
        $pageTitle = $titleMap[$status] ?? 'All Payments';

        // 4. Base breadcrumb items
        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('client.dashboard')],
            ['label' => 'Payments', 'url' => route('client.payments.index')],
        ];

        // 5. Append specific status breadcrumb if filter applied
        if (isset($titleMap[$status])) {
            $breadcrumbItems[] = [
                'label' => $pageTitle,
                'url' => route('client.payments.index', ['status' => $status]),
            ];
        }
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
            <x-flash-message :type="session('success') ? 'success' : 'error'" :title="session('success') ? 'Success' : 'Error'" :message="session('success') ?? session('error')" />
        @endif

        {{-- ===========================
             Stats Cards Section
             Summary of payments counts and totals
        ============================ --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 mb-6">
            {{-- Total Payments --}}
            <x-card.stat-card :label="'Total Payments'" :value="number_format($totalPayments)" :iconBgColor="'bg-accent-100'" :iconTextColor="'text-accent-600'"
                :icon="'fas fa-money-bill-wave'" />

            {{-- Completed Payments --}}
            <x-card.stat-card :label="'Completed Payments'" :value="number_format($paidCount)" :iconBgColor="'bg-green-100'" :iconTextColor="'text-green-600'"
                :icon="'fas fa-check-circle'" />

            {{-- Pending Payments --}}
            <x-card.stat-card :label="'Pending Payments'" :value="number_format($pendingCount)" :iconBgColor="'bg-yellow-100'" :iconTextColor="'text-yellow-600'"
                :icon="'fas fa-clock'" />

            {{-- Total Amount --}}
            <x-card.stat-card :label="'Total Amount'" :value="$settings->currency . ' ' . number_format($totalAmount)" :iconBgColor="'bg-secondary-100'" :iconTextColor="'text-secondary-600'"
                :icon="'fas fa-dollar-sign'" />
        </div>

        {{-- ===========================
             Payments Table Section
             Table with filters, export, pagination
        ============================ --}}
        <div class="bg-white rounded-xl shadow-sm">

            {{-- ====================================
                 Table Header: Title + Actions
            ==================================== --}}
            <div class="p-6 border-b border-gray-200">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">

                    {{-- Section Title --}}
                    <h3 class="text-lg font-semibold text-primary-900 mb-4 md:mb-0">{{ $pageTitle }}</h3>

                    {{-- Table Actions (Search + Export) --}}
                    <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-4">

                        {{-- Search Form --}}
                        <form method="GET" action="{{ route('client.payments.index') }}"
                            class="relative w-full md:w-auto">
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Search payments..."
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-accent-500 focus:border-accent-500 transition duration-300">
                            <button type="submit"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-accent-500 transition">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                        {{-- Export Button --}}
                        <a href="{{ route('client.payments.export', ['status' => $status]) }}"
                            class="bg-gray-100 hover:bg-gray-200 text-primary-700 px-4 py-2 rounded-lg text-sm font-medium transition duration-300">
                            <i class="fas fa-download mr-2"></i>Export
                        </a>
                    </div>
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
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                #SL</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Payment ID</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Member</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Amount</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Generated Date</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Paid Date</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Due Date</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Method</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Status</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>

                    {{-- Table Rows --}}
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php $sl = 1; @endphp

                        @forelse ($payments as $payment)
                            <tr class="hover:bg-gray-50">
                                {{-- Serial Number --}}
                                <td class="px-6 py-4 text-sm text-primary-900 font-mono">#{{ $sl++ }}</td>

                                {{-- Payment ID --}}
                                <td class="px-6 py-4 text-sm font-medium text-primary-900">{{ $payment->payment_id }}
                                </td>

                                {{-- Member Info --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        {{-- Profile photo or default avatar --}}
                                        <img src="{{ $payment->member->profile_photo ? $payment->member->profile_photo_url : 'https://ui-avatars.com/api/?name=' . urlencode($payment->member->name) }}"
                                            alt="Member" class="w-8 h-8 rounded-full mr-3">
                                        <span
                                            class="text-sm font-medium text-primary-900">{{ $payment->member->name }}</span>
                                    </div>
                                </td>

                                {{-- Amount --}}
                                <td class="px-6 py-4 text-sm text-primary-600">
                                    {{ $settings->currency . ' ' . number_format($payment->amount) }}</td>

                                {{-- Generated Date --}}
                                <td class="px-6 py-4 text-sm text-primary-600">
                                    <span
                                        class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                        {{ $payment->created_at->format('M d, Y') }}
                                    </span>
                                </td>

                                {{-- Paid Date --}}
                                <td class="px-6 py-4 text-sm text-primary-600">
                                    <span
                                        class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                        {{ $payment->paid_at ? $payment->paid_at->format('M d, Y') : 'N/A' }}
                                    </span>
                                </td>

                                {{-- Due Date --}}
                                <td class="px-6 py-4 text-sm text-primary-600">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                        {{ $payment->due_date ? $payment->due_date->format('M d, Y') : 'N/A' }}
                                    </span>
                                </td>

                                {{-- Payment Method --}}
                                <td class="px-6 py-4 text-sm text-primary-600">
                                    <span
                                        class="px-2 py-1 text-xs font-medium rounded-full {{ $payment->payment_method ? $payment->payment_method->bgColor() : 'bg-gray-100' }} {{ $payment->payment_method ? $payment->payment_method->color() : 'text-gray-800' }}">
                                        {{ $payment->payment_method ? $payment->payment_method->label() : 'N/A' }}
                                    </span>
                                </td>

                                {{-- Payment Status --}}
                                <td class="px-6 py-4 text-sm whitespace-nowrap">
                                    <span
                                        class="px-2 py-1 text-xs font-medium rounded-full {{ $payment->status->bgColor() }} {{ $payment->status->color() }}">
                                        {{ $payment->status->label() }}
                                    </span>
                                </td>

                                {{-- Actions --}}
                                <td class="px-6 py-4 text-sm font-medium">
                                    <div class="flex space-x-2">
                                        {{-- View --}}
                                        <a href="{{ route('client.payments.show', $payment->id) }}"
                                            class="text-accent-600 hover:text-accent-900" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        {{-- Edit --}}
                                        <a href="{{ route('client.payments.edit', $payment->id) }}"
                                            class="text-secondary-600 hover:text-secondary-900" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        {{-- Delete --}}
                                        @include('client.payment.destroy')
                                    </div>
                                </td>
                            </tr>
                        @empty
                            {{-- Empty State --}}
                            <tr>
                                <td colspan="10" class="text-center py-4 text-sm text-gray-500">No payments found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ===============================
                 Pagination & Results Info
            =============================== --}}
            <div class="px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    {{-- Results Info --}}
                    <div class="text-sm text-primary-600">
                        @if ($payments->total() > 0)
                            Showing {{ $payments->firstItem() }} to {{ $payments->lastItem() }} of
                            {{ $payments->total() }} results
                        @else
                            No results found.
                        @endif
                    </div>

                    {{-- Pagination Links --}}
                    <div class="flex space-x-2">
                        <x-pagination :paginator="$payments" />
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-client.layout.app>
