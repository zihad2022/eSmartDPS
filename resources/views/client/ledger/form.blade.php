<x-client.layout.app>
    {{-- Set the page title dynamically based on whether we are editing or creating a ledger entry --}}
    <x-slot:title>{{ isset($ledger) ? 'Edit Ledger Entry' : 'Add New Ledger Entry' }}</x-slot:title>

    {{-- Breadcrumb navigation for better user context --}}
    <x-breadcrumb :items="[
        ['label' => 'Dashboard', 'url' => route('client.dashboard')],
        ['label' => 'All Ledgers', 'url' => route('client.ledgers.index')],
        ['label' => isset($ledger) ? 'Edit Ledger Entry' : 'Add New Ledger Entry'],
    ]" />

    <div class="">
        {{-- Flash messages for success or error notifications --}}
        @if (session('success') || session('error'))
            <x-flash-message 
                :type="session('success') ? 'success' : 'error'" 
                :title="session('success') ? 'Success' : 'Error'" 
                :message="session('success') ?? session('error')" 
            />
        @endif

        <div class="bg-white rounded-2xl shadow-sm p-6 w-full mx-auto">
            {{-- Header: dynamic title based on editing or creating --}}
            <h2 class="text-xl font-semibold text-primary-900 mb-6">
                {{ $editing = isset($ledger) ? 'Edit Ledger Entry' : 'Add New Ledger Entry' }}
            </h2>

            @php
                // Determine if this is an edit form
                $editing = isset($ledger);
            @endphp

            {{-- Ledger Form --}}
            <form method="POST"
                action="{{ $editing ? route('client.ledgers.update', $ledger->id) : route('client.ledgers.store') }}"
                class="space-y-8">
                @csrf
                @if ($editing)
                    @method('PUT')
                @endif

                {{-- Ledger Information Section --}}
                <div class="bg-gray-50 p-4 rounded-lg border">
                    <h3 class="text-lg font-semibold text-primary-900 mb-4">Ledger Information</h3>

                    {{-- Category & Type --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Ledger Category Dropdown --}}
                        <x-form.select 
                            name="ledger_category_id" 
                            label="Category" 
                            :options="$ledgerCategories" 
                            :selected="old('ledger_category_id', $ledger->ledger_category_id ?? '')"
                            required 
                        />

                        {{-- Ledger Type Dropdown --}}
                        <x-form.select 
                            name="type" 
                            label="Type" 
                            :options="collect(\App\Enums\Ledger\LedgerType::cases())
                                ->mapWithKeys(fn($case) => [$case->value => $case->label()])
                                ->toArray()" 
                            :selected="old('type', $ledger->type ?? '')" 
                            required 
                        />
                    </div>

                    {{-- Amount & Entry Date --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                        {{-- Ledger Amount Input --}}
                        <x-form.input 
                            name="amount" 
                            label="Amount" 
                            type="number" 
                            step="0.01" 
                            :value="old('amount', $ledger->amount ?? '')"
                            placeholder="Enter amount" 
                            required 
                        />

                        {{-- Ledger Entry Date Input --}}
                        <x-form.input 
                            name="entry_date" 
                            label="Entry Date" 
                            type="date" 
                            :value="old(
                                'entry_date',
                                isset($ledger->entry_date) ? $ledger->entry_date->format('Y-m-d') : '',
                            )" 
                            required 
                        />
                    </div>

                    {{-- Description & Notes --}}
                    <div class="grid grid-cols-1 gap-6 mt-4">
                        {{-- Description input --}}
                        <x-form.input 
                            name="description" 
                            label="Description" 
                            :value="old('description', $ledger->description ?? '')"
                            placeholder="Enter short description" 
                            required 
                        />

                        {{-- Notes textarea (optional) --}}
                        <x-form.textarea 
                            name="notes" 
                            label="Notes" 
                            :value="old('notes', $ledger->notes ?? '')"
                            placeholder="Optional additional details" 
                            rows="4" 
                        />
                    </div>
                </div>

                {{-- Action Buttons (Cancel / Submit) --}}
                <div class="flex justify-end space-x-4 pt-4">
                    {{-- Cancel Button: returns to ledger list --}}
                    <a href="{{ route('client.ledgers.index') }}"
                        class="px-4 py-2 border border-gray-300 text-primary-700 rounded-lg hover:bg-gray-50 text-sm transition duration-300">
                        Cancel
                    </a>

                    {{-- Submit Button: dynamically updates label based on editing or creating --}}
                    <button type="submit"
                        class="px-4 py-2 bg-accent-500 text-white text-sm rounded-lg hover:bg-accent-600 transition duration-300">
                        {{ $editing ? 'Update Ledger' : 'Add Ledger' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-client.layout.app>
