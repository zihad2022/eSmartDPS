<div class="pt-6 px-6 w-full">
    <div
        class="bg-red-50 border border-red-200 rounded-xl p-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4 shadow-sm">

        <div class="flex items-start gap-3">
            <span class="text-red-500 text-2xl">
                <i class="fas fa-exclamation-circle"></i>
            </span>

            <div>
                <h2 class="font-semibold text-red-700 text-lg">
                    Payment Required
                </h2>
                <p class="text-sm text-red-600">
                    Your account is inactive. Please complete your payment to access premium features.
                </p>
            </div>
        </div>
        @php
            $client = Auth::guard('client')->user();

            $invoiceId = $client->invoices()->where('status', \App\Enums\InvoiceStatus::UNPAID)->latest()->first()?->id;
        @endphp

        <a href="{{ $invoiceId ? route('client.checkout.create', $invoiceId) : 'javascript:void(0)' }}"
            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-red-600 text-white font-semibold hover:bg-red-700 transition justify-center sm:justify-start">

            <i class="fas fa-credit-card"></i>
            Complete Payment
        </a>


    </div>
</div>
