<x-client.layout.app>
    @php
        $status = request()->status;

        // Title mapping based on status
        $titleMap = [
            'pending' => 'Pending Payments',
            'due' => 'Due Payments',
            'paid' => 'Paid Payments',
            'cancelled' => 'Cancelled Payments',
        ];

        $pageTitle = $titleMap[$status] ?? 'All Payments';

        // Breadcrumb setup
        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('client.dashboard')],
            ['label' => 'Payments', 'url' => route('client.payments.index')],
        ];

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
        ======================== --}}
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
            <x-card.stat-card :label="'Total Amount'" :value="$settings->currency .' '. number_format($totalAmount)" :iconBgColor="'bg-secondary-100'" :iconTextColor="'text-secondary-600'"
                :icon="'fas fa-dollar-sign'" />
        </div>

        {{-- =======================
            Payments Table
        ======================== --}}
        <div class="bg-white rounded-xl shadow-sm">
            {{-- Table Header --}}
            <div class="p-6 border-b border-gray-200">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <h3 class="text-lg font-semibold text-primary-900 mb-4 md:mb-0">{{ $pageTitle }}</h3>
                    <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-4">
                        {{-- Date Filter --}}
                        <input type="date"
                               class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-500">

                        {{-- Status Filter --}}
                        <select class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-500">
                            <option>All Status</option>
                            <option>Pending</option>
                            <option>Paid</option>
                            <option>Cancelled</option>
                        </select>

                        {{-- Export Button --}}
                        <button class="bg-gray-100 hover:bg-gray-200 text-primary-700 px-4 py-2 rounded-lg text-sm font-medium transition duration-300">
                            <i class="fas fa-download mr-2"></i>Export
                        </button>
                    </div>
                </div>
            </div>

            {{-- Table Content --}}
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">SL</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">Payment ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">Member</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">Method</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200">
                        @php $sl = 1; @endphp

                        @forelse ($payments as $payment)
                            <tr class="hover:bg-gray-50">
                                {{-- Serial --}}
                                <td class="px-6 py-4 text-sm text-primary-900 font-mono">#{{ $sl++ }}</td>

                                {{-- Payment ID --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-primary-900">
                                    {{ $payment->payment_id }}
                                </td>

                                {{-- Member Info --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <img src="https://randomuser.me/api/portraits/men/32.jpg" class="w-8 h-8 rounded-full mr-3" alt="Member">
                                        <span class="text-sm font-medium text-primary-900">{{ $payment->member->name }}</span>
                                    </div>
                                </td>

                                {{-- Amount --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-primary-600">
                                    ${{ $payment->amount }}
                                </td>

                                {{-- Date --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-primary-600">
                                    {{ $payment->created_at->format('M d, Y') }}
                                </td>

                                {{-- Method --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-primary-600">
                                    {{ $payment->payment_method ?? 'N/A' }}
                                </td>

                                {{-- Status --}}
                                @php
                                    $status = $payment->status;
                                    $statusLabel = $status->label();

                                    $statusClasses = match ($status) {
                                        \App\Enums\PaymentStatus::PENDING => 'bg-yellow-100 text-yellow-800',
                                        \App\Enums\PaymentStatus::DUE => 'bg-blue-100 text-blue-800',
                                        \App\Enums\PaymentStatus::PAID => 'bg-green-100 text-green-800',
                                        \App\Enums\PaymentStatus::CANCELLED => 'bg-red-100 text-red-800',
                                        default => 'bg-gray-100 text-gray-800',
                                    };
                                @endphp
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $statusClasses }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>

                                {{-- Actions --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('client.payments.show', $payment->id) }}" class="text-accent-600 hover:text-accent-900" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('client.payments.edit', $payment->id) }}" class="text-secondary-600 hover:text-secondary-900" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">No payments found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-primary-600">
                        @if ($payments->total() > 0)
                            Showing {{ $payments->firstItem() }} to {{ $payments->lastItem() }} of {{ $payments->total() }} results
                        @else
                            No results found.
                        @endif
                    </div>
                    <div class="flex space-x-2">
                        <x-pagination :paginator="$payments" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-client.layout.app>
