<x-client.settings.layout>
    {{-- Display success or error flash message --}}
    @if (session('success') || session('error'))
        <x-flash-message 
            :type="session('success') ? 'success' : 'error'" 
            :title="session('success') ? 'Success' : 'Error'" 
            :message="session('success') ?? session('error')" 
        />
    @endif

    <x-slot name="title">Settings</x-slot>

    <div id="settings" class="settings-content bg-white rounded-xl shadow-sm p-6 space-y-10">
     
        {{-- ===============================
            Payment Settings Section
        =============================== --}}
        <div>
            <h3 class="text-lg font-semibold text-primary-900 mb-6">Payment Settings</h3>

            <form class="space-y-6" action="{{ route('client.settings.payment.update') }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Payment Due Date --}}
                <x-form.input 
                    name="payment_due_date" 
                    label="Payment Due Date (e.g., 1, 15, 30)" 
                    :value="old('payment_due_date', $settings->payment_due_date ?? '')" 
                    placeholder="Enter due date" 
                />

                {{-- Late Payment Fee --}}
                <x-form.input 
                    name="late_payment_fee" 
                    label="Late Payment Fee" 
                    type="number" step="0.01" 
                    :value="old('late_payment_fee', $settings->late_payment_fee ?? '')" 
                    placeholder="Enter late fee" 
                />

                {{-- Grace Period Days --}}
                <x-form.input 
                    name="grace_period_days" 
                    label="Grace Period (days)" 
                    type="number" 
                    :value="old('grace_period_days', $settings->grace_period_days ?? '')" 
                    placeholder="Enter grace period days" 
                />

              {{-- Payment Methods (multi-select checkboxes) --}}
              <div>
                <x-form.checkbox 
                name="payment_methods" 
                label="Allowed Payment Methods"
                :options="collect(\App\Enums\PaymentMethod::cases())->mapWithKeys(fn($m) => [$m->value => $m->label()])->toArray()"
                :selected="old('payment_methods', $settings->payment_methods ?? [])"
            />
            
            </div>
            
                <button type="submit"
                    class="bg-accent-500 hover:bg-accent-600 text-white px-6 py-2 rounded-lg transition duration-300">
                    Save Changes
                </button>
            </form>
        </div>
    </div>
</x-client.settings.layout>
