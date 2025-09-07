@php
    use App\Enums\PaymentMethod;
    use App\Enums\PaymentStatus;
@endphp

<x-client.layout.app>
    {{-- =======================
        Page Title & Breadcrumb
        - Sets the title for the page
        - Adds breadcrumb navigation for better UX
    ======================== --}}
    <x-slot:title>Payment Details</x-slot:title>
    <x-breadcrumb :items="[
        ['label' => 'Dashboard', 'url' => route('client.dashboard')],
        ['label' => 'All Payments', 'url' => route('client.payments.index')],
        ['label' => 'Payment Details'],
    ]" />

    <div>
        {{-- =======================
            Page Header
            - Displays the main heading of the page
        ======================== --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
            <h1 class="text-2xl font-bold text-gray-800 mb-2 md:mb-0">Payment Details</h1>
        </div>

        {{-- =======================
            Summary Cards
            - Quick overview of Payment ID, Member, and Amount
        ======================== --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            {{-- Payment ID --}}
            <div class="bg-gray-50 border-l-4 border-green-500 p-4 rounded-lg shadow hover:shadow-lg transition">
                <p class="text-sm text-gray-500 font-medium">Payment ID</p>
                <h3 class="text-lg font-semibold text-gray-800">{{ $payment->payment_id }}</h3>
            </div>

            {{-- Member Info --}}
            <div class="bg-gray-50 border-l-4 border-green-500 p-4 rounded-lg shadow hover:shadow-lg transition">
                <p class="text-sm text-gray-500 font-medium">Member</p>
                <h3 class="text-lg font-semibold text-gray-800">
                    {{ $payment->member->name }} ({{ $payment->member->member_id }})
                </h3>
            </div>

            {{-- Payment Amount --}}
            <div class="bg-gray-50 border-l-4 border-green-500 p-4 rounded-lg shadow hover:shadow-lg transition">
                <p class="text-sm text-gray-500 font-medium">Amount</p>
                <h3 class="text-lg font-semibold text-gray-800">
                    ${{ number_format($payment->amount, 2) }} {{ $payment->currency }}
                </h3>
            </div>
        </div>

        {{-- =======================
            Payment Details Section
            - Shows all details about this payment
        ======================== --}}
        <div class="bg-white rounded-xl shadow p-6 mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Payment Method --}}
                <div class="bg-gray-50 p-4 rounded-lg shadow">
                    <p class="text-sm text-gray-500 font-medium">Payment Method</p>
                    <h3 class="text-lg font-semibold text-gray-800">
                        {{ $payment->payment_method ? $payment->payment_method->label() : 'N/A' }}
                    </h3>
                </div>

                {{-- Payment Status --}}
                <div class="bg-gray-50 p-4 rounded-lg shadow">
                    <p class="text-sm text-gray-500 font-medium">Status</p>
                    <span class="inline-block px-3 py-1 text-xs font-medium rounded-full
                        {{ $payment->status->bgColor() }} {{ $payment->status->color() }}">
                        {{ $payment->status->label() }}
                    </span>
                </div>

                {{-- Transaction ID --}}
                <div class="bg-gray-50 p-4 rounded-lg shadow">
                    <p class="text-sm text-gray-500 font-medium">Transaction ID</p>
                    <h3 class="text-lg font-semibold text-gray-800">{{ $payment->transaction_id ?? 'N/A' }}</h3>
                </div>

                {{-- Reference Number --}}
                <div class="bg-gray-50 p-4 rounded-lg shadow">
                    <p class="text-sm text-gray-500 font-medium">Reference</p>
                    <h3 class="text-lg font-semibold text-gray-800">{{ $payment->reference ?? 'N/A' }}</h3>
                </div>

                {{-- Paid Date --}}
                <div class="bg-gray-50 p-4 rounded-lg shadow">
                    <p class="text-sm text-gray-500 font-medium">Paid At</p>
                    <h3 class="text-lg font-semibold text-gray-800">
                        {{ $payment->paid_at ? $payment->paid_at->format('M d, Y H:i') : 'N/A' }}
                    </h3>
                </div>

                {{-- Due Date --}}
                <div class="bg-gray-50 p-4 rounded-lg shadow">
                    <p class="text-sm text-gray-500 font-medium">Due Date</p>
                    <h3 class="text-lg font-semibold text-gray-800">
                        {{ $payment->due_date ? $payment->due_date->format('M d, Y') : 'N/A' }}
                    </h3>
                </div>
            </div>

            {{-- Notes / Meta Information --}}
            <div class="mt-6 bg-gray-50 p-4 rounded-lg shadow md:col-span-2">
                <p class="text-sm text-gray-500 font-medium">Notes / Meta</p>
                <pre class="text-sm text-gray-800 bg-white p-2 rounded">{{ $payment->meta ?? 'None' }}</pre>
            </div>
        </div>

        {{-- =======================
            Actions
            - Buttons for navigation (Back, Edit)
        ======================== --}}
        <div class="flex justify-end space-x-4">
            {{-- Back to Payment List --}}
            <a href="{{ route('client.payments.index') }}"
               class="px-4 py-2 border border-gray-400 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition">
                Back
            </a>

            {{-- Edit Payment --}}
            <a href="{{ route('client.payments.edit', $payment) }}"
               class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium transition">
                Edit
            </a>
        </div>
    </div>
</x-client.layout.app>
