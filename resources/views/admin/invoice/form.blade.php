<x-admin.layout.app>
    @php
        // Check if we are editing an existing invoice or creating a new one
        $editing = isset($invoice);
    @endphp

    {{-- Breadcrumb navigation --}}
    <x-breadcrumb :items="[
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Invoices', 'url' => route('admin.invoices.index')],
        ['label' => $editing ? 'Edit Invoice' : 'Add New Invoice'],
    ]" />

    {{-- Page Title --}}
    <x-slot:title>{{ $editing ? 'Edit Invoice' : 'Add New Invoice' }}</x-slot:title>

    <div>
        <div class="bg-white rounded-2xl shadow-sm p-6 w-full mx-auto">
            
            {{-- Page Heading --}}
            <h2 class="text-xl font-semibold text-primary-900 mb-6">
                {{ $editing ? 'Edit Invoice' : 'Add New Invoice' }}
            </h2>

            {{-- Invoice Form (Create / Update) --}}
            <form method="POST"
                action="{{ $editing ? route('admin.invoices.update', $invoice->id) : route('admin.invoices.store') }}"
                class="space-y-8">
                
                @csrf
                {{-- If editing, use PUT method --}}
                @if ($editing)
                    @method('PUT')
                @endif

                {{-- ================================
                    Invoice Basic Info
                ================================= --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Invoice Number (auto-generated or custom, readonly) --}}
                    <x-form.input 
                        name="invoice_number" 
                        label="Invoice Number" 
                        :value="old('invoice_number', $editing ? $invoice->invoice_number : $invoice_number)" 
                        required 
                        placeholder="Auto-generated or custom number" 
                        readonly 
                    />

                    {{-- Invoice Amount --}}
                    <x-form.input 
                        name="invoice_amount" 
                        label="Invoice Amount" 
                        type="text" 
                        step="0.01"
                        :value="old('invoice_amount', $editing ? $invoice->invoice_amount : '')" 
                        required 
                        placeholder="Enter invoice amount" 
                    />
                </div>

                {{-- ================================
                    Client Selection
                ================================= --}}
                <x-form.select 
                    name="client_id" 
                    label="Client" 
                    :options="$clients->mapWithKeys(fn($client) => [
                        $client->id => $client->first_name . ' ' . $client->last_name
                    ])" 
                    :selected="old('client_id', $editing ? $invoice->client_id : null)" 
                    optionLabel="Select Client"
                    required 
                />
            
                {{-- ================================
                    Payment Reference Info
                ================================= --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Internal Payment ID --}}
                    <x-form.input 
                        name="payment_id" 
                        label="Internal Payment ID" 
                        :value="old('payment_id', $editing ? $invoice->payment_id : '')" 
                        placeholder="Optional" 
                    />

                    {{-- External Transaction ID --}}
                    <x-form.input 
                        name="trx_id" 
                        label="External Transaction ID" 
                        :value="old('trx_id', $editing ? $invoice->trx_id : '')" 
                        placeholder="Optional" 
                    />
                </div>

                {{-- Wallet / Account Address --}}
                <x-form.input 
                    name="wallet_address" 
                    label="Wallet / Account Address" 
                    :value="old('wallet_address', $editing ? $invoice->wallet_address : '')" 
                    placeholder="Optional" 
                />

                {{-- ================================
                    Payment Method & Status
                ================================= --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              
                    {{-- Payment Method (Enum options) --}}
                    <x-form.select 
                        name="payment_method" 
                        label="Payment Method" 
                        :options="collect(\App\Enums\PaymentMethod::cases())
                            ->mapWithKeys(fn($type) => [$type->value => $type->label()])
                            ->toArray()" 
                        :selected="old('payment_method', $invoice->payment_method?->value ?? '')" 
                    />

                    {{-- Invoice Status (Enum options) --}}
                    <x-form.select 
                        name="status" 
                        label="Status" 
                        :options="collect(\App\Enums\InvoiceStatus::cases())
                            ->mapWithKeys(fn($type) => [$type->value => $type->label()])
                            ->toArray()" 
                        :selected="old('status', $invoice->status?->value ?? '')" 
                    />
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
