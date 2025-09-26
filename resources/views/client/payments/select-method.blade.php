<x-client.layout.app>
    <x-slot:title>Pay Invoice #{{ $invoice->invoice_number }}</x-slot:title>

    <div class="max-w-2xl mx-auto my-12 p-6 bg-white rounded-2xl shadow-lg border border-accent-200">
        <h2 class="text-2xl font-bold text-accent-900 mb-4">Choose Payment Method</h2>
        <p class="text-gray-700 mb-6">Invoice Amount: {{ $invoice->invoice_amount }} {{ $settings->currency }}</p>

        <form action="{{ route('client.payments.process', $invoice->id) }}" method="POST">
            @csrf
            <div class="space-y-4">
                @foreach($paymentMethods as $key => $label)
                    <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="payment_method" value="{{ $key }}" class="mr-3" required>
                        <span class="text-gray-800 font-medium">{{ $label }}</span>
                    </label>
                @endforeach
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit"
                    class="bg-accent-500 hover:bg-accent-600 transition duration-300 text-white px-6 py-2 rounded-lg font-semibold shadow-sm">
                    Continue to Pay
                </button>
            </div>
        </form>
    </div>
</x-client.layout.app>
