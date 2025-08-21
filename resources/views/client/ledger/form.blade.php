<x-client.layout.app>
    <div class="">
        @if (session('success') || session('error'))
            <x-flash-message :type="session('success') ? 'success' : 'error'" :title="session('success') ? 'Success' : 'Error'" :message="session('success') ?? session('error')" />
        @endif

        <div class="bg-white rounded-2xl shadow-sm p-6 w-full mx-auto">
            <h2 class="text-xl font-semibold text-primary-900 mb-6">
                {{ $editing = isset($ledger) ? 'Edit Ledger Entry' : 'Add New Ledger Entry' }}
            </h2>

            @php
                $editing = isset($ledger);
            @endphp

            <form method="POST"
                action="{{ $editing ? route('client.ledgers.update', $ledger->id) : route('client.ledgers.store') }}"
                class="space-y-8">
                @csrf
                @if ($editing)
                    @method('PUT')
                @endif

                <!-- 🟢 LEDGER INFORMATION -->
                <div class="bg-gray-50 p-4 rounded-lg border">
                    <h3 class="text-lg font-semibold text-primary-900 mb-4">Ledger Information</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Ledger Category -->
                        <x-form.select name="ledger_category_id" label="Category" :options="$ledgerCategories" :selected="old('ledger_category_id', $ledger->ledger_category_id ?? '')"
                            required />

                        <!-- Type -->
                        <x-form.select name="type" label="Type" :options="collect(\App\Enums\Ledger\LedgerType::cases())
                            ->mapWithKeys(fn($case) => [$case->value => $case->label()])
                            ->toArray()" :selected="old('type', $ledger->type ?? '')" required />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                        <!-- Amount -->
                        <x-form.input name="amount" label="Amount" type="number" step="0.01" :value="old('amount', $ledger->amount ?? '')"
                            placeholder="Enter amount" required />

                        <!-- Entry Date -->
                        <x-form.input name="entry_date" label="Entry Date" type="date" :value="old(
                            'entry_date',
                            isset($ledger->entry_date) ? $ledger->entry_date->format('Y-m-d') : '',
                        )" required />
                    </div>

                    <div class="grid grid-cols-1 gap-6 mt-4">
                        <!-- Description -->
                        <x-form.input name="description" label="Description" :value="old('description', $ledger->description ?? '')"
                            placeholder="Enter short description" required />

                        <!-- Notes -->
                        <x-form.textarea name="notes" label="Notes" :value="old('notes', $ledger->notes ?? '')"
                            placeholder="Optional additional details" rows="4" />
                    </div>
                </div>

                <!-- 🔵 ACTION BUTTONS -->
                <div class="flex justify-end space-x-4 pt-4">
                    <a href="{{ route('client.ledgers.index') }}"
                        class="px-4 py-2 border border-gray-300 text-primary-700 rounded-lg hover:bg-gray-50 text-sm transition duration-300">
                        Cancel
                    </a>

                    <button type="submit"
                        class="px-4 py-2 bg-accent-500 text-white text-sm rounded-lg hover:bg-accent-600 transition duration-300">
                        {{ $editing ? 'Update Ledger' : 'Add Ledger' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-client.layout.app>
