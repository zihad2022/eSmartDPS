@php
    use App\Enums\PaymentMethod;
    use App\Enums\PaymentStatus;

    // Determine if this is an edit form (existing payment) or a create form (new payment)
    $editing = isset($payment);
@endphp

<x-client.layout.app>
    {{-- Page title changes depending on edit or create --}}
    <x-slot:title>{{ $editing ? 'Edit Payment' : 'Add New Payment' }}</x-slot:title>

    {{-- Breadcrumb navigation for better UX --}}
    <x-breadcrumb :items="[
        ['label' => 'Dashboard', 'url' => route('client.dashboard')],
        ['label' => 'All Payments', 'url' => route('client.payments.index')],
        ['label' => $editing ? 'Edit Payment' : 'Add New Payment'],
    ]" />

    <div class="">
        <div class="bg-white rounded-2xl shadow-sm p-6 w-full mx-auto">
            {{-- Section header --}}
            <h2 class="text-xl font-semibold text-primary-900 mb-6">
                {{ $editing ? 'Edit Payment' : 'Add New Payment' }}
            </h2>

            {{-- Payment form start --}}
            <form method="POST"
                  action="{{ $editing ? route('client.payments.update', $payment->id) : route('client.payments.store') }}"
                  class="space-y-6">
                @csrf
                {{-- Use PUT for update --}}
                @if ($editing)
                    @method('PUT')
                @endif

                {{-- ---------------- Row 1: Payment ID & Member ---------------- --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Payment ID (always disabled, system generated) --}}
                    <x-form.input name="payment_id" label="Payment ID"
                        :value="old('payment_id', $payment->payment_id ?? generate_payment_id())"
                        required placeholder="Enter Payment ID" :disabled="true" />

                    {{-- Member selection (only set on create, disabled on edit) --}}
                    <div>
                        <label for="member_id" class="block text-sm font-medium text-primary-700 mb-2">Member</label>
                        <select name="member_id" id="member_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm"
                                disabled>
                            <option value="">-- Select Member --</option>
                            @foreach ($members as $member)
                                <option value="{{ $member->id }}"
                                    {{ old('member_id', $payment->member_id ?? '') == $member->id ? 'selected' : '' }}>
                                    {{ $member->name }} ({{ $member->member_id }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- ---------------- Row 2: Amount & Currency ---------------- --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Amount (fixed at creation, cannot be changed later) --}}
                    <x-form.input name="amount" label="Amount" type="number" step="0.01"
                        :value="old('amount', $payment->amount ?? '')"
                        placeholder="Enter amount" :disabled="true" />

                    {{-- Currency (fixed when payment is created) --}}
                    <x-form.input name="currency" label="Currency" type="text"
                        :value="old('currency', $payment->currency ?? 'USD')"
                        placeholder="Enter currency code" :disabled="true" />
                </div>

                {{-- ---------------- Row 3: Payment Method & Status ---------------- --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Payment method (set once when payment is made, disabled later) --}}
                    <x-form.select 
                        name="payment_method" 
                        label="Payment Method" 
                        :options="collect(PaymentMethod::cases())
                            ->mapWithKeys(fn($type) => [$type->value => $type->label()])
                            ->toArray()" 
                        :selected="old('payment_method', $payment->payment_method?->value ?? '')" 
                        disabled
                    />

                    {{-- Payment status (editable, admin can change e.g. pending → paid) --}}
                    <x-form.select 
                        name="status" 
                        label="Status" 
                        :options="collect(PaymentStatus::cases())
                            ->mapWithKeys(fn($type) => [$type->value => $type->label()])
                            ->toArray()" 
                        :selected="old('status', $payment->status?->value ?? '')" 
                    />
                </div>

                {{-- ---------------- Row 4: Transaction ID & Reference ---------------- --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Transaction ID (gateway-provided, cannot be changed later) --}}
                    <x-form.input name="transaction_id" label="Transaction ID"
                        :value="old('transaction_id', $payment->transaction_id ?? '')"
                        placeholder="Payment gateway transaction ID" :disabled="true" />

                    {{-- Reference (internal/external reference, set once only) --}}
                    <x-form.input name="reference" label="Reference"
                        :value="old('reference', $payment->reference ?? '')"
                        placeholder="Internal/external reference" :disabled="true" />
                </div>

                {{-- ---------------- Row 5: Paid At & Due Date ---------------- --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Paid date (system-controlled, cannot edit manually) --}}
                    <x-form.input name="paid_at" label="Paid Date" type="datetime-local"
                        :value="old('paid_at', isset($payment->paid_at) ? $payment->paid_at->format('Y-m-d\TH:i') : '')"
                        placeholder="When payment was made" :disabled="true" />

                    {{-- Due date (system-controlled, disabled for security) --}}
                    <x-form.input name="due_date" label="Due Date" type="date"
                        :value="old('due_date', isset($payment->due_date) ? $payment->due_date->format('Y-m-d') : '')"
                        placeholder="Payment due date" :disabled="true" />
                </div>

                {{-- ---------------- Notes / Meta ---------------- --}}
                <div>
                    {{-- Optional notes or metadata in JSON format --}}
                    <x-form.textarea name="meta" label="Notes / Meta JSON" rows="3"
                        placeholder='Optional: {"note": "example"}'>{{ old('meta', $payment->meta ?? '') }}</x-form.textarea>
                </div>

                {{-- ---------------- Form Actions ---------------- --}}
                <div class="flex justify-end space-x-4 pt-4">
                    {{-- Cancel → go back to payments list --}}
                    <a href="{{ route('client.payments.index') }}"
                        class="px-4 py-2 border border-gray-300 text-primary-700 rounded-lg hover:bg-gray-50 text-sm transition">
                        Cancel
                    </a>

                    {{-- Submit button (label changes for edit/create) --}}
                    <button type="submit"
                        class="px-4 py-2 bg-accent-500 text-white text-sm rounded-lg hover:bg-accent-600 transition">
                        {{ $editing ? 'Update Payment' : 'Add Payment' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-client.layout.app>
