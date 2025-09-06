@php
    use App\Enums\PaymentMethod;
    use App\Enums\PaymentStatus;
@endphp

<x-client.layout.app>
    {{-- Keep breadcrumb as is --}}
    <x-slot:title>Payment Details</x-slot:title>
    <x-breadcrumb :items="[
        ['label' => 'Dashboard', 'url' => route('client.dashboard')],
        ['label' => 'All Payments', 'url' => route('client.payments.index')],
        ['label' => 'Payment Details'],
    ]" />

    <div class="max-w-7xl mx-auto mt-6 p-6">
        <!-- Page Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
            <h1 class="text-2xl font-bold text-green-700 mb-2 md:mb-0">Payment Details</h1>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg shadow hover:shadow-lg transition">
                <p class="text-sm text-green-700 font-medium">Payment ID</p>
                <h3 class="text-lg font-semibold text-green-900">{{ $payment->payment_id }}</h3>
            </div>
            <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg shadow hover:shadow-lg transition">
                <p class="text-sm text-green-700 font-medium">Member</p>
                <h3 class="text-lg font-semibold text-green-900">{{ $payment->member->name }} ({{ $payment->member->member_id }})</h3>
            </div>
            <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg shadow hover:shadow-lg transition">
                <p class="text-sm text-green-700 font-medium">Amount</p>
                <h3 class="text-lg font-semibold text-green-900">${{ number_format($payment->amount, 2) }} {{ $payment->currency }}</h3>
            </div>
        </div>

        <!-- Details Section -->
        <div class="bg-white rounded-xl shadow p-6 mb-8">
            <h2 class="text-xl font-bold text-green-700 mb-4">Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-green-50 p-4 rounded-lg shadow">
                    <p class="text-sm text-green-700 font-medium">Payment Method</p>
                    <h3 class="text-lg font-semibold text-green-900">
                        {{ $payment->payment_method ? PaymentMethod::from($payment->payment_method)->label() : 'N/A' }}
                    </h3>
                </div>
                <div class="bg-green-50 p-4 rounded-lg shadow">
                    <p class="text-sm text-green-700 font-medium">Status</p>
                    <span class="inline-block px-3 py-1 text-xs font-medium rounded-full
                        {{ $payment->status->bgColor() }} {{ $payment->status->color() }}">
                        {{ $payment->status->label() }}
                    </span>
                </div>
                <div class="bg-green-50 p-4 rounded-lg shadow">
                    <p class="text-sm text-green-700 font-medium">Transaction ID</p>
                    <h3 class="text-lg font-semibold text-green-900">{{ $payment->transaction_id ?? 'N/A' }}</h3>
                </div>
                <div class="bg-green-50 p-4 rounded-lg shadow">
                    <p class="text-sm text-green-700 font-medium">Reference</p>
                    <h3 class="text-lg font-semibold text-green-900">{{ $payment->reference ?? 'N/A' }}</h3>
                </div>
                <div class="bg-green-50 p-4 rounded-lg shadow">
                    <p class="text-sm text-green-700 font-medium">Paid At</p>
                    <h3 class="text-lg font-semibold text-green-900">
                        {{ $payment->paid_at ? $payment->paid_at->format('M d, Y H:i') : 'N/A' }}
                    </h3>
                </div>
                <div class="bg-green-50 p-4 rounded-lg shadow">
                    <p class="text-sm text-green-700 font-medium">Due Date</p>
                    <h3 class="text-lg font-semibold text-green-900">
                        {{ $payment->due_date ? $payment->due_date->format('M d, Y') : 'N/A' }}
                    </h3>
                </div>
            </div>

            <!-- Meta / Notes -->
            <div class="mt-6 bg-green-50 p-4 rounded-lg shadow md:col-span-2">
                <p class="text-sm text-green-700 font-medium">Notes / Meta</p>
                <pre class="text-sm text-green-900 bg-white p-2 rounded">{{ $payment->meta ?? 'None' }}</pre>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('client.payments.index') }}"
                class="px-4 py-2 border border-green-500 text-green-700 rounded-lg hover:bg-green-50 font-medium transition">Back</a>
        </div>
    </div>
</x-client.layout.app>
