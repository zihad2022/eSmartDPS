<x-admin.layout.app>
    @php
        $editing = isset($invoice);
    @endphp
    <x-breadcrumb :items="[
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Invoices', 'url' => route('admin.invoices.index')],
        ['label' => $editing ? 'Edit Invoice' : 'Add New Invoice'],
    ]" />
    <x-slot:title>{{ $editing ? 'Edit Invoice' : 'Add New Invoice' }}</x-slot:title>
    <div>
        <div class="bg-white rounded-2xl shadow-sm p-6 w-full mx-auto">
            <h2 class="text-xl font-semibold text-primary-900 mb-6">
                {{ $editing ? 'Edit Invoice' : 'Add New Invoice' }}
            </h2>

            <form method="POST"
                action="{{ $editing ? route('admin.invoices.update', $invoice->id) : route('admin.invoices.store') }}"
                class="space-y-8">
                @csrf
                @if ($editing)
                    @method('PUT')
                @endif

                {{-- 📋 Invoice Info --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-form.input name="invoice_number" label="Invoice Number" :value="isset($invoice) ? $invoice->invoice_number : $invoice_number" required
                        placeholder="Auto-generated or custom number" :disabled="true" />

                    <x-form.input name="invoice_amount" label="Invoice Amount" type="number" step="0.01"
                        :value="old('invoice_amount', $invoice->invoice_amount ?? '')" required placeholder="Enter invoice amount" />
                </div>

                {{-- 🔗 Client --}}
                <div>
                    <label for="client_id" class="block text-sm font-medium text-primary-700 mb-2">Client</label>
                    <select name="client_id" id="client_id"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-accent-500"
                        required>
                        <option value="">-- Select Client --</option>
                        @foreach ($clients as $client)
                            <option value="{{ $client->id }}"
                                {{ old('client_id', $invoice->client_id ?? '') == $client->id ? 'selected' : '' }}>
                                {{ $client->first_name }} {{ $client->last_name }} ({{ $client->user_id }})
                            </option>
                        @endforeach
                    </select>
                    @error('client_id')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- 💳 Payment Info --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-form.input name="payment_id" label="Internal Payment ID" :value="old('payment_id', $invoice->payment_id ?? '')"
                        placeholder="Optional" />

                    <x-form.input name="trx_id" label="External Transaction ID" :value="old('trx_id', $invoice->trx_id ?? '')"
                        placeholder="Optional" />
                </div>

                <x-form.input name="wallet_address" label="Wallet / Account Address" :value="old('wallet_address', $invoice->wallet_address ?? '')"
                    placeholder="Optional" />

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="payment_method" class="block text-sm font-medium text-primary-700 mb-2">Payment
                            Method</label>
                        <select name="payment_method" id="payment_method"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-accent-500">
                            <option value="">-- Select Method --</option>
                            <option value="bkash"
                                {{ old('payment_method', $invoice->payment_method ?? '') == 'bkash' ? 'selected' : '' }}>
                                bKash</option>
                            <option value="nagad"
                                {{ old('payment_method', $invoice->payment_method ?? '') == 'nagad' ? 'selected' : '' }}>
                                Nagad</option>
                            <option value="paypal"
                                {{ old('payment_method', $invoice->payment_method ?? '') == 'paypal' ? 'selected' : '' }}>
                                PayPal</option>
                            <option value="manual"
                                {{ old('payment_method', $invoice->payment_method ?? '') == 'manual' ? 'selected' : '' }}>
                                Manual</option>
                        </select>
                        @error('payment_method')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-primary-700 mb-2">Status</label>
                        <select name="status" id="status"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-accent-500">
                            @foreach (App\Enums\InvoiceStatus::cases() as $status)
                                <option value="{{ $status->value }}"
                                    {{ old('status', $invoice->status ?? '') == $status->value ? 'selected' : '' }}>
                                    {{ $status->label() }}
                                </option>
                            @endforeach
                        </select>
                        @error('status')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- 🔘 Submit --}}
                <div class="flex justify-end space-x-4 pt-4">
                    <a href="{{ route('admin.invoices.index') }}"
                        class="px-4 py-2 border border-gray-300 text-primary-700 rounded-lg hover:bg-gray-50 text-sm transition duration-300">
                        Cancel
                    </a>
                    <button type="submit"
                        class="px-4 py-2 bg-accent-500 text-white text-sm rounded-lg hover:bg-accent-600 transition duration-300">
                        {{ $editing ? 'Update Invoice' : 'Add Invoice' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin.layout.app>
