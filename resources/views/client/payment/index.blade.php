<x-client.layout.app>
    @php
        $status = request()->status;

        // ======================
        // Title mapping by status
        // ======================
        $titleMap = [
            'pending' => 'Pending Payments',
            'due' => 'Due Payments',
            'paid' => 'Paid Payments',
            'cancelled' => 'Cancelled Payments',
        ];

        // Default to "All Payments" if no specific status is selected
        $pageTitle = $titleMap[$status] ?? 'All Payments';

        // ======================
        // Breadcrumb setup
        // ======================
        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('client.dashboard')],
            ['label' => 'Payments', 'url' => route('client.payments.index')],
        ];

        // Add specific status breadcrumb if filtering
        if (isset($titleMap[$status])) {
            $breadcrumbItems[] = [
                'label' => $pageTitle,
                'url' => route('client.payments.index', ['status' => $status]),
            ];
        }
    @endphp

    {{-- =======================
        Page Title & Breadcrumb
    ======================== --}}
    <x-slot:title>{{ $pageTitle }}</x-slot:title>
    <x-breadcrumb :items="$breadcrumbItems" />

    <div class="">
        {{-- =======================
            Stats Cards (Summary)
            Quick overview of payment counts and totals
        ======================== --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 mb-6">
            {{-- Total number of payments --}}
            <x-card.stat-card :label="'Total Payments'" :value="number_format($totalPayments)" :iconBgColor="'bg-accent-100'" :iconTextColor="'text-accent-600'"
                :icon="'fas fa-money-bill-wave'" />

            {{-- Number of completed/paid payments --}}
            <x-card.stat-card :label="'Completed Payments'" :value="number_format($paidCount)" :iconBgColor="'bg-green-100'" :iconTextColor="'text-green-600'"
                :icon="'fas fa-check-circle'" />

            {{-- Number of pending payments --}}
            <x-card.stat-card :label="'Pending Payments'" :value="number_format($pendingCount)" :iconBgColor="'bg-yellow-100'" :iconTextColor="'text-yellow-600'"
                :icon="'fas fa-clock'" />

            {{-- Total payment amount (with currency) --}}
            <x-card.stat-card :label="'Total Amount'" :value="$settings->currency .' '. number_format($totalAmount)" :iconBgColor="'bg-secondary-100'" :iconTextColor="'text-secondary-600'"
                :icon="'fas fa-dollar-sign'" />
        </div>

        {{-- =======================
            Payments Table Section
            Main data table with filters, export, pagination
        ======================== --}}
        <div class="bg-white rounded-xl shadow-sm">
            {{-- ---------- Table Header: Title, Filters, Export ---------- --}}
            <div class="p-6 border-b border-gray-200">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    {{-- Table Title --}}
                    <h3 class="text-lg font-semibold text-primary-900 mb-4 md:mb-0">{{ $pageTitle }}</h3>

                    {{-- Filters + Export button --}}
                    <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-4">
                        {{-- Date Filter (currently placeholder, needs backend handling) --}}
                        <input type="date"
                               class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-500">

                        {{-- Status Filter (currently placeholder, needs backend handling) --}}
                        <select class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-500">
                            <option>All Status</option>
                            <option>Pending</option>
                            <option>Paid</option>
                            <option>Cancelled</option>
                        </select>

                        {{-- Export Button (CSV/Excel export can be added later) --}}
                        <button class="bg-gray-100 hover:bg-gray-200 text-primary-700 px-4 py-2 rounded-lg text-sm font-medium transition duration-300">
                            <i class="fas fa-download mr-2"></i>Export
                        </button>
                    </div>
                </div>
            </div>

            {{-- ---------- Table Content ---------- --}}
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            {{-- Table headers --}}
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">SL</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">Payment ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">Member</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">Generated Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">Paid At</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">Due Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">Method</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200">
                        @php $sl = 1; @endphp

                        {{-- Loop payments list --}}
                        @forelse ($payments as $payment)
                            <tr class="hover:bg-gray-50">
                                {{-- Serial number --}}
                                <td class="px-6 py-4 text-sm text-primary-900 font-mono">#{{ $sl++ }}</td>

                                {{-- Payment ID --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-primary-900">
                                    {{ $payment->payment_id }}
                                </td>

                                {{-- Member Info (photo + name) --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if ($payment->member->profile_photo)
                                            {{-- Show uploaded profile photo --}}
                                            <img src="{{ $payment->member->profile_photo_url }}" class="w-8 h-8 rounded-full mr-3" alt="Member">
                                        @else
                                            {{-- Default avatar if no photo --}}
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($payment->member->name) }}&background=007BFF&color=fff"
                                                 alt="Member" class="w-8 h-8 rounded-full mr-3">
                                        @endif
                                        <span class="text-sm font-medium text-primary-900">{{ $payment->member->name }}</span>
                                    </div>
                                </td>

                                {{-- Amount with currency --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-primary-600">
                                    {{ $settings->currency .' '. number_format($payment->amount) }}
                                </td>

                                {{-- Generated Date --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-primary-600">
                                    {{ $payment->created_at->format('M d, Y') }}
                                </td>

                                {{-- Paid Date (with badge) --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-primary-600">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                        {{ $payment->paid_at ? $payment->paid_at->format('M d, Y') : 'N/A' }}
                                    </span>
                                </td>

                                {{-- Due Date (with badge) --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-primary-600">
                                   <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                        {{ $payment->due_date ? $payment->due_date->format('M d, Y') : 'N/A' }}
                                   </span>
                                </td>

                                {{-- Payment Method --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-primary-600">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $payment->payment_method->bgColor() }} {{ $payment->payment_method->color() }}">
                                        {{ $payment->payment_method->label() ?? 'N/A' }}
                                    </span>
                                </td>

                                {{-- Payment Status --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $payment->status->bgColor() }} {{ $payment->status->color() }}">
                                        {{ $payment->status->label() }}
                                    </span>
                                </td>

                                {{-- Action buttons (view, edit, delete) --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        {{-- View --}}
                                        <a href="{{ route('client.payments.show', $payment->id) }}" class="text-accent-600 hover:text-accent-900" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        {{-- Edit --}}
                                        <a href="{{ route('client.payments.edit', $payment->id) }}" class="text-secondary-600 hover:text-secondary-900" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        {{-- Delete (with confirm modal) --}}
                                        <form method="POST" action="{{ route('client.payments.destroy', $payment->id) }}"
                                              class="delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="text-red-600 hover:text-red-900 delete-btn"
                                                title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                    {{-- Confirmation modal component --}}
                                    <x-confirm-modal />
                                </td>
                            </tr>
                        @empty
                            {{-- If no payments found --}}
                            <tr>
                                <td colspan="8" class="text-center py-4">No payments found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ---------- Pagination Section ---------- --}}
            <div class="px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-primary-600">
                        @if ($payments->total() > 0)
                            {{-- Showing X to Y of Z results --}}
                            Showing {{ $payments->firstItem() }} to {{ $payments->lastItem() }} of {{ $payments->total() }} results
                        @else
                            No results found.
                        @endif
                    </div>
                    <div class="flex space-x-2">
                        {{-- Custom pagination component --}}
                        <x-pagination :paginator="$payments" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-client.layout.app>
