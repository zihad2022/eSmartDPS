<x-admin.layout.app>
    <div class="p-4 md:p-6 max-w-3xl mx-auto">
        <!-- Flash Messages -->
        @if (session('success') || session('error'))
            <x-flash-message :type="session('success') ? 'success' : 'error'" :title="session('success') ? 'Success' : 'Error'" :message="session('success') ?? session('error')" />
        @endif

        <!-- Invoice Header -->
        <div class="bg-white rounded-xl shadow p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-primary-900">Invoice Details</h2>
                <span class="text-sm text-gray-500">#{{ $invoice->invoice_number }}</span>
            </div>

            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-primary-600">Client Name</p>
                        <p class="font-medium text-primary-900">
                            {{ $invoice->client->first_name }} {{ $invoice->client->last_name }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-primary-600">Invoice Date</p>
                        <p class="font-medium text-primary-900">
                            {{ $invoice->created_at->format('F d, Y') }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-primary-600">Amount</p>
                        <p class="font-medium text-primary-900">৳{{ $invoice->invoice_amount }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-primary-600">Status</p>

                        @if ($invoice->status)
                            <p class="font-medium">
                                {{ $invoice->status->label() }}
                            </p>
                        @else
                            <p class="font-medium text-gray-600">Unknown</p>
                        @endif
                    </div>


                    <div>
                        <p class="text-sm text-primary-600">Payment Method</p>
                        <p class="font-medium text-primary-900">
                            {{ $invoice->payment_method ?? '—' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-primary-600">Transaction ID</p>
                        <p class="font-medium text-primary-900">
                            {{ $invoice->trx_id ?? '—' }}
                        </p>
                    </div>

                    <div class="col-span-2">
                        <p class="text-sm text-primary-600">Wallet / Manual Address</p>
                        <p class="font-medium text-primary-900">
                            {{ $invoice->wallet_address ?? '—' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin.layout.app>
