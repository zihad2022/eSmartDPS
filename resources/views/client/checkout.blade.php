<x-client.layout.app>
    <x-slot:title>Complete Your Payment</x-slot:title>

    <section class="min-h-screen py-16">
        <div class="max-w-3xl mx-auto">

            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-10">

                <!-- Header -->
                <div class="mb-8 text-center">
                    <h1 class="text-3xl font-extrabold text-gray-900 mb-2">
                        Complete Your Payment
                    </h1>
                    <p class="text-gray-600 text-sm">
                        Review your invoice and choose a payment method.
                    </p>
                </div>

                <!-- Combined Card -->
                <div class="space-y-10">

                    <!-- INVOICE SUMMARY -->
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                            <i class="fas fa-receipt text-accent-500"></i>
                            Invoice Summary
                        </h2>

                        <div class="space-y-4 text-sm">

                            <div class="flex justify-between">
                                <span class="text-gray-600">Invoice #</span>
                                <strong class="text-gray-900">{{ $invoice->invoice_number }}</strong>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-gray-600">Plan</span>
                                <strong class="text-gray-900">{{ $invoice->package_name }}</strong>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-gray-600">Billing Period</span>
                                <strong class="text-gray-900">
                                    {{ \Carbon\Carbon::parse($invoice->billing_start)->format('d M Y') }}
                                    -
                                    {{ \Carbon\Carbon::parse($invoice->billing_end)->format('d M Y') }}
                                </strong>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-gray-600">Amount</span>
                                <strong class="text-gray-900">{{ number_format($invoice->invoice_amount) }} BDT</strong>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-gray-600">Status</span>
                                <strong class="text-gray-900">
                                    @if ($invoice->status == 1)
                                        Paid
                                    @else
                                        Unpaid
                                    @endif
                                </strong>
                            </div>

                            @if ($invoice->package_description)
                                <div class="text-gray-700 mt-4 text-sm">
                                    {{ $invoice->package_description }}
                                </div>
                            @endif

                        </div>
                    </div>

                    <!-- PAYMENT SECTION -->
                    @if ($invoice->status != 1)
                        <form action="{{ route('client.payments.process', $invoice->id) }}" method="POST">
                            @csrf

                            <div class="mt-6">
                                <h2 class="text-lg font-bold text-gray-900 mb-4">
                                    Choose Payment Method
                                </h2>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                                    <!-- bKash -->
                                    <label class="cursor-pointer">
                                        <div class="border border-gray-300 hover:border-accent-500 transition rounded-xl p-4 flex items-center gap-4">
                                            <input type="radio" name="payment_method" value="bkash" class="text-accent-600" checked>

                                            <img src="https://www.logo.wine/a/logo/BKash/BKash-Icon-Logo.wine.svg"
                                                 alt="bKash Payment" class="w-10 h-10 object-contain">

                                            <span class="font-medium text-gray-900">bKash</span>
                                        </div>
                                    </label>

                                    <!-- SSLCOMMERZ -->
                                    <label class="cursor-pointer">
                                        <div class="border border-gray-300 hover:border-accent-500 transition rounded-xl p-4 flex items-center gap-4">
                                            <input type="radio" name="payment_method" value="sslcommerz" class="text-accent-600">

                                            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSsP9tEf-ZJ-KQZWOfG4cVNdi5BgVi3WbFDNg&s"
                                                 alt="SSLCOMMERZ Payment" class="w-10 h-10 object-contain">

                                            <span class="font-medium text-gray-900">SSLCOMMERZ</span>
                                        </div>
                                    </label>

                                </div>
                            </div>

                            <!-- BUTTON -->
                            <div class="mt-10">
                                <button
                                    class="w-full py-4 bg-accent-600 text-white rounded-2xl font-semibold text-lg hover:bg-accent-700 transition flex items-center justify-center gap-2">
                                    <i class="fas fa-lock"></i>
                                    Pay {{ number_format($invoice->invoice_amount) }} BDT Now
                                </button>
                            </div>

                        </form>
                    @else
                        <div class="mt-6 text-center text-green-600 font-semibold">
                            This invoice has already been paid.
                        </div>
                    @endif

                </div>

            </div>

        </div>
    </section>

</x-client.layout.app>
