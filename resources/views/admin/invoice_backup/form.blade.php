<x-admin.layout.app>
    @php
        /**
         * =======================================
         * Page Setup: Editing Flag, Title, Breadcrumbs
         * =======================================
         */

        // 1. Check if we are editing an existing invoice or creating a new one
        $editing = isset($invoice);

        // 2. Set page title based on editing mode
        $pageTitle = $editing ? 'Edit Invoice' : 'Add New Invoice';

        // 3. Base breadcrumb items
        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'Invoices', 'url' => route('admin.invoices.index')],
            ['label' => $pageTitle],
        ];
    @endphp

    {{-- ===========================
         Set HTML Page Title
    ============================ --}}
    <x-slot:title>{{ $pageTitle }}</x-slot:title>

    {{-- ===========================
         Breadcrumb Navigation
    ============================ --}}
    <x-breadcrumb :items="$breadcrumbItems" />

    {{-- ===========================
         Main Content Wrapper
    ============================ --}}
    <div>
        <div class="bg-white rounded-2xl shadow-sm p-6 w-full mx-auto">

            {{-- ===========================
                 Page Heading
            ============================ --}}
            <h2 class="text-xl font-semibold text-primary-900 mb-6">
                {{ $pageTitle }}
            </h2>

            {{-- ===========================
                 Invoice Form (Create / Update)
            ============================ --}}
            <form method="POST"
                action="{{ $editing ? route('admin.invoices.update', $invoice->id) : route('admin.invoices.store') }}"
                class="space-y-8">

                @csrf
                {{-- 1. If editing, use PUT method --}}
                @if ($editing)
                    @method('PUT')
                @endif

                {{-- ================================
                     Invoice Basic Info Section
                ================================= --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- 2. Invoice Number (readonly, auto-generated) --}}
                    <x-form.input name="invoice_number" label="Invoice Number" :value="old('invoice_number', $editing ? $invoice->invoice_number : $invoice_number)" required
                        placeholder="Auto-generated or custom number" readonly />

                    {{-- 3. Invoice Amount --}}
                    <x-form.input name="invoice_amount" label="Invoice Amount" type="text" step="0.01"
                        :value="old('invoice_amount', $editing ? $invoice->invoice_amount : '')" required placeholder="Enter invoice amount" />
                </div>

                {{-- ================================
                     Client Selection Section
                ================================= --}}
                <x-form.select name="client_id" label="Client" :options="$clients->mapWithKeys(
                    fn($client) => [
                        $client->id => $client->first_name . ' ' . $client->last_name,
                    ],
                )" :selected="old('client_id', $editing ? $invoice->client_id : null)"
                    optionLabel="Select Client" required />

                {{-- ================================
                     Payment Reference Section
                ================================= --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Internal Payment ID --}}
                    <x-form.input name="payment_id" label="Internal Payment ID" :value="old('payment_id', $editing ? $invoice->payment_id : '')"
                        placeholder="Optional" />

                    {{-- External Transaction ID --}}
                    <x-form.input name="trx_id" label="External Transaction ID" :value="old('trx_id', $editing ? $invoice->trx_id : '')"
                        placeholder="Optional" />
                </div>

                {{-- Wallet / Account Address --}}
                <x-form.input name="wallet_address" label="Wallet / Account Address" :value="old('wallet_address', $editing ? $invoice->wallet_address : '')"
                    placeholder="Optional" />

                {{-- ================================
                     Payment Method & Status Section
                ================================= --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Payment Method --}}
                    <x-form.select name="payment_method" label="Payment Method" :options="collect(\App\Enums\PaymentMethod::cases())
                        ->mapWithKeys(fn($type) => [$type->value => $type->label()])
                        ->toArray()" :selected="old('payment_method', $invoice->payment_method?->value ?? '')" />

                    {{-- Invoice Status --}}
                    <x-form.select name="status" label="Status" :options="collect(\App\Enums\InvoiceStatus::cases())
                        ->mapWithKeys(fn($type) => [$type->value => $type->label()])
                        ->toArray()" :selected="old('status', $invoice->status?->value ?? '')" />
                </div>

                {{-- ================================
                     Form Actions (Submit / Cancel)
                ================================= --}}
                <div class="flex justify-end space-x-4 pt-4">
                    {{-- Cancel Button --}}
                    <a href="{{ route('admin.invoices.index') }}"
                        class="px-4 py-2 border border-gray-300 text-primary-700 rounded-lg hover:bg-gray-50 text-sm transition duration-300">
                        Cancel
                    </a>

                    {{-- Submit Button --}}
                    <button type="submit"
                        class="px-4 py-2 bg-accent-500 text-white text-sm rounded-lg hover:bg-accent-600 transition duration-300">
                        {{ $editing ? 'Update Invoice' : 'Add Invoice' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin.layout.app>
