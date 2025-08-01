<x-client.layout.app>
    <div class="">
        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 mb-6">
            <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs md:text-sm text-primary-500 font-medium">Total Payments</p>
                        <h3 class="text-xl md:text-2xl font-bold text-primary-900">{{ $totalPayments }}</h3>
                    </div>
                    <div class="w-10 h-10 bg-accent-100 text-accent-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-money-bill-wave text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs md:text-sm text-primary-500 font-medium">Completed</p>
                        <h3 class="text-xl md:text-2xl font-bold text-primary-900">{{ $paidCount }}</h3>
                    </div>
                    <div class="w-10 h-10 bg-green-100 text-green-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs md:text-sm text-primary-500 font-medium">Pending</p>
                        <h3 class="text-xl md:text-2xl font-bold text-primary-900">{{ $pendingCount }}</h3>
                    </div>
                    <div class="w-10 h-10 bg-yellow-100 text-yellow-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs md:text-sm text-primary-500 font-medium">Total Amount</p>
                        <h3 class="text-xl md:text-2xl font-bold text-primary-900">${{ $totalAmount }}</h3>
                    </div>
                    <div
                        class="w-10 h-10 bg-secondary-100 text-secondary-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-dollar-sign text-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payments Table -->
        <div class="bg-white rounded-xl shadow-sm">
            <div class="p-6 border-b border-gray-200">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <h3 class="text-lg font-semibold text-primary-900 mb-4 md:mb-0">All Payments</h3>
                    <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-4">
                        <!-- Date Filter -->
                        <input type="date"
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-500">

                        <!-- Status Filter -->
                        <select
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-500">
                            <option>All Status</option>
                            <option>Completed</option>
                            <option>Pending</option>
                            <option>Failed</option>
                        </select>

                        <!-- Export Button -->
                        <button
                            class="bg-gray-100 hover:bg-gray-200 text-primary-700 px-4 py-2 rounded-lg text-sm font-medium transition duration-300">
                            <i class="fas fa-download mr-2"></i>Export
                        </button>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                sl</th>
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
                                Date</th>
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
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php
                            $sl = 1;
                        @endphp
                        @forelse ($payments as $payment)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-primary-900 font-mono">
                                    #{{ $sl++ }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-primary-900">
                                    {{ $payment->payment_id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <img src="https://randomuser.me/api/portraits/men/32.jpg"
                                            class="w-8 h-8 rounded-full mr-3" alt="Member">
                                        <span
                                            class="text-sm font-medium text-primary-900">{{ $payment->member->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-primary-600">${{ $payment->amount }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-primary-600">
                                    {{ $payment->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-primary-600">
                                    {{-- {{ $payment->payment_method->label() ?? 'N/A' }}</td> --}}
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
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button class="text-accent-600 hover:text-accent-900" title="View">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <a href="{{ route('client.payments.edit', $payment->id) }}"
                                            class="text-secondary-600 hover:text-secondary-900" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">No payments found.</td>
                            </tr>
                        @endforelse

                        {{-- <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-primary-900">#PAY002</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <img src="https://randomuser.me/api/portraits/women/44.jpg"
                                        class="w-8 h-8 rounded-full mr-3" alt="Member">
                                    <span class="text-sm font-medium text-primary-900">Jane Smith</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-primary-600">$500.00</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-primary-600">May 14, 2025</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-primary-600">Cash</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <button class="text-accent-600 hover:text-accent-900" title="View">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="text-secondary-600 hover:text-secondary-900" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="text-red-600 hover:text-red-900" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-primary-900">#PAY003</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <img src="https://randomuser.me/api/portraits/men/86.jpg"
                                        class="w-8 h-8 rounded-full mr-3" alt="Member">
                                    <span class="text-sm font-medium text-primary-900">Robert Johnson</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-primary-600">$350.00</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-primary-600">May 13, 2025</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-primary-600">Mobile Money</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Completed</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <button class="text-accent-600 hover:text-accent-900" title="View">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="text-secondary-600 hover:text-secondary-900" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="text-red-600 hover:text-red-900" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-primary-900">#PAY004</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <img src="https://randomuser.me/api/portraits/women/67.jpg"
                                        class="w-8 h-8 rounded-full mr-3" alt="Member">
                                    <span class="text-sm font-medium text-primary-900">Sarah Johnson</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-primary-600">$200.00</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-primary-600">May 12, 2025</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-primary-600">Bank Transfer</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">Failed</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <button class="text-accent-600 hover:text-accent-900" title="View">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="text-secondary-600 hover:text-secondary-900" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="text-red-600 hover:text-red-900" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr> --}}
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-primary-600">
                        @if ($payments->total() > 0)
                            Showing {{ $payments->firstItem() }} to {{ $payments->lastItem() }} of
                            {{ $payments->total() }} results
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
