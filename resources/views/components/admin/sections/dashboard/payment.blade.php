@php
    $settings = \App\Models\AdminSetting::select('currency')->first();
@endphp
<div class="grid grid-cols-1 gap-6">
    <div class="bg-white rounded-xl shadow-sm p-6 dashboard-card">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-primary-900">Recent Payments</h3>
            <a href="{{ route('admin.invoices.index') }}"
               class="text-accent-600 hover:text-accent-700 text-sm font-medium">
                View All
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full table-auto">
                <thead>
                    <tr class="border-b border-gray-200 text-left text-xs font-medium text-primary-500 uppercase">
                        <th class="px-4 py-3">#SL</th>
                        <th class="px-4 py-3">Invoice #</th>
                        <th class="px-4 py-3">Client Name</th>
                        <th class="px-4 py-3">Package</th>
                        <th class="px-4 py-3">Amount</th>
                        <th class="px-4 py-3">Payment Method</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Paid At</th>
                        <th class="px-4 py-3">Generated Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentPayments as $payment)
                        <tr class="border-b border-gray-200 text-sm">
                            <td class="px-4 py-3 font-medium text-primary-700">
                                #{{ $loop->iteration }}
                            </td>
                            <td class="px-4 py-3 font-medium text-primary-700">
                                {{ $payment->invoice_number }}
                            </td>
                            <td class="px-4 py-3">
                                {{ $payment->client->first_name }} {{ $payment->client->last_name }}
                            </td>
                            <td class="px-4 py-3">
                                {{ $payment->package_name }}
                            </td>
                            <td class="px-4 py-3">
                                {{ $settings->currency }} {{ number_format($payment->invoice_amount) }}
                            </td>
                            <td class="px-4 py-3">
                                {{ $payment->payment_method ?? '—' }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $payment->status->color() }}">
                                    {{ $payment->status->label() }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-500">
                                {{ $payment->paid_at ? \Carbon\Carbon::parse($payment->paid_at)->format('d M Y') : '—' }}
                            </td>
                            <td class="px-4 py-3 text-gray-500">
                                {{ \Carbon\Carbon::parse($payment->created_at)->format('d M Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-6 text-center text-gray-500">
                                No recent payments found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
