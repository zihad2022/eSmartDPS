<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Recent Payments -->
    <div class="bg-white rounded-xl shadow-sm p-6 dashboard-card">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-primary-900">Recent Payments</h3>
            <a href="{{ route('admin.invoices.index') }}"
                class="text-accent-600 hover:text-accent-700 text-sm font-medium">
                View All
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="px-4 py-3 text-left text-xs font-medium text-primary-500 uppercase">Client</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-primary-500 uppercase">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-primary-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($recentPayments as $payment)
                        <tr class="border-b border-gray-200">
                            <td class="px-4 py-3">
                                {{ $payment->client->first_name }} {{ $payment->client->last_name }}
                            </td>
                            <td class="px-4 py-3 text-sm">{{ $payment->invoice_amount }}</td>
                            <td class="px-4 py-3">
                                <span
                                    class="px-2 py-1 text-xs font-medium rounded-full {{ $payment->status->color() }}">
                                    {{ $payment->status->label() }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
