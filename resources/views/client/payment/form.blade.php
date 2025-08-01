@php
    use App\Enums\PaymentMethod;
    use App\Enums\PaymentStatus;
    $editing = isset($payment); // define before any Blade logic
@endphp

<x-client.layout.app>
    <div class="">
        <div class="bg-white rounded-2xl shadow-sm p-6 w-full mx-auto">
            <h2 class="text-xl font-semibold text-primary-900 mb-6">
                {{ $editing ? 'Edit Payment' : 'Add New Payment' }}
            </h2>

            <form method="POST"
                action="{{ $editing ? route('client.payments.update', $payment->id) : route('client.payments.store') }}"
                class="space-y-6">
                @csrf
                @if ($editing)
                    @method('PUT')
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-form.input name="payment_id" label="Payment ID" :value="old('payment_id', $payment->payment_id ?? '')" required
                        placeholder="Enter Payment ID" :disabled="true" />

                    <div>
                        <label for="member_id" class="block text-sm font-medium text-primary-700 mb-2">Member</label>
                        <select name="member_id" id="member_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-accent-500"
                            @if ($editing) disabled @endif required>
                            <option value="">-- Select Member --</option>
                            @foreach ($members as $member)
                                <option value="{{ $member->id }}"
                                    {{ old('member_id', $payment->member_id ?? '') == $member->id ? 'selected' : '' }}>
                                    {{ $member->name }} ({{ $member->member_id }})
                                </option>
                            @endforeach
                        </select>
                        @error('member_id')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-form.input name="amount" label="Amount" type="number" step="0.01" :value="old('amount', $payment->amount ?? '')" required
                        placeholder="Enter amount" :disabled="$editing" />

                    <x-form.input name="date" label="Payment Date" type="date" :value="old('date', isset($payment->date) ? $payment->date->format('Y-m-d') : '')" required
                        :disabled="$editing" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="payment_method" class="block text-sm font-medium text-primary-700 mb-2">Payment
                            Method</label>
                        <select name="payment_method" id="payment_method"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-accent-500"
                            required @if ($editing) disabled @endif>
                            @foreach (PaymentMethod::cases() as $method)
                                <option value="{{ $method->value }}"
                                    {{ old('payment_method', $payment->payment_method->value ?? 1) == $method->value ? 'selected' : '' }}>
                                    {{ $method->label() }}
                                </option>
                            @endforeach
                        </select>
                        @error('payment_method')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-primary-700 mb-2">Payment
                            Status</label>
                        <select name="status" id="status"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-accent-500"
                            required>
                            @foreach (PaymentStatus::cases() as $status)
                                <option value="{{ $status->value }}"
                                    {{ old('status', $payment->status->value ?? 1) == $status->value ? 'selected' : '' }}>
                                    {{ $status->label() }}
                                </option>
                            @endforeach
                        </select>
                        @error('status')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end space-x-4 pt-4">
                    <a href="{{ route('client.payments.index') }}"
                        class="px-4 py-2 border border-gray-300 text-primary-700 rounded-lg hover:bg-gray-50 text-sm transition duration-300">
                        Cancel
                    </a>

                    <button type="submit"
                        class="px-4 py-2 bg-accent-500 text-white text-sm rounded-lg hover:bg-accent-600 transition duration-300">
                        {{ $editing ? 'Update Payment' : 'Add Payment' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-client.layout.app>
