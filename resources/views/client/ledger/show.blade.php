<x-client.layout.app>
    @php
        // Page title for showing ledger details
        $pageTitle = 'Ledger Details';

        // Breadcrumb items: Dashboard > All Ledgers > Ledger Details
        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('client.dashboard')],
            ['label' => 'All Ledgers', 'url' => route('client.ledgers.index')],
            ['label' => $pageTitle, 'url' => route('client.ledgers.show', $ledger)],
        ];
    @endphp

    {{-- Set HTML page title --}}
    <x-slot:title>{{ $pageTitle }}</x-slot:title>

    {{-- Render breadcrumb navigation --}}
    <x-breadcrumb :items="$breadcrumbItems" />

    <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-sm p-6">
        {{-- Ledger Heading --}}
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-semibold text-primary-900">{{ $ledger->description }}</h2>
            <a href="{{ route('client.ledgers.edit', $ledger) }}"
               class="px-4 py-2 bg-accent-500 text-white text-sm rounded-lg hover:bg-accent-600 transition">
                Edit Ledger
            </a>
        </div>

        {{-- Ledger Details Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Category --}}
            <div>
                <p class="text-sm font-medium text-primary-500">Category</p>
                <p class="text-sm text-primary-900">{{ $ledger->ledgerCategory->name ?? 'N/A' }}</p>
            </div>

            {{-- Type --}}
            <div>
                <p class="text-sm font-medium text-primary-500">Type</p>
                <p class="text-sm text-primary-900">
                    @if ($ledger->type == \App\Enums\Ledger\LedgerType::INCOME)
                        Income
                    @else
                        Expense
                    @endif
                </p>
            </div>

            {{-- Amount --}}
            <div>
                <p class="text-sm font-medium text-primary-500">Amount</p>
                <p class="text-sm {{ $ledger->type == \App\Enums\Ledger\LedgerType::INCOME ? 'text-green-600' : 'text-red-600' }} font-semibold">
                    {{ $ledger->type == \App\Enums\Ledger\LedgerType::INCOME ? '+' : '-' }}${{ number_format($ledger->amount, 2) }}
                </p>
            </div>

            {{-- Entry Date --}}
            <div>
                <p class="text-sm font-medium text-primary-500">Entry Date</p>
                <p class="text-sm text-primary-900">{{ \Carbon\Carbon::parse($ledger->entry_date)->format('M d, Y') }}</p>
            </div>

            {{-- Notes --}}
            <div class="md:col-span-2">
                <p class="text-sm font-medium text-primary-500">Notes</p>
                <p class="text-sm text-primary-900">{{ $ledger->notes ?? 'N/A' }}</p>
            </div>
        </div>
    </div>
</x-client.layout.app>
