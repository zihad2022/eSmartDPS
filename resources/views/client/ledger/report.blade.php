<x-client.layout.app>
    <x-slot:title>Ledger Report</x-slot:title>

    <x-breadcrumb :items="[
        ['label' => 'Dashboard', 'url' => route('client.dashboard')],
        ['label' => 'Ledger Report'],
    ]" />

    <div>
        <div class="bg-white rounded-2xl shadow-sm p-6 w-full mx-auto">
            <h2 class="text-xl font-semibold text-primary-900 mb-6">
                Ledger Report
            </h2>

            {{-- ===========================
                 Filter Section
            ============================ --}}
            <form method="GET" action="{{ route('client.ledgers.report') }}"
                class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8 items-end">
                
                {{-- From Date --}}
                <x-form.input name="from_date" label="From Date" type="date"
                    :value="request('from_date')" />

                {{-- To Date --}}
                <x-form.input name="to_date" label="To Date" type="date"
                    :value="request('to_date')" />

                {{-- Category --}}
                <x-form.select name="ledger_category_id" label="Category"
                    :options="$categories->toArray()"
                    :selected="request('ledger_category_id')" />

                {{-- Filter Button --}}
                <div class="flex">
                    <button type="submit"
                        class="w-full px-4 py-2 bg-accent-500 text-white text-sm rounded-lg hover:bg-accent-600 transition duration-300">
                        Apply Filter
                    </button>
                </div>
            </form>

            {{-- ===========================
                 Report Table
            ============================ --}}
            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-200 rounded-lg">
                    <thead class="bg-gray-50 text-left text-sm font-medium text-gray-700">
                        <tr>
                            <th class="px-4 py-3 border-b">Date</th>
                            <th class="px-4 py-3 border-b">Category</th>
                            <th class="px-4 py-3 border-b">Description</th>
                            <th class="px-4 py-3 border-b text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm text-gray-600">
                        @forelse($ledgers as $ledger)
                            <tr>
                                <td class="px-4 py-3">{{ $ledger->entry_date->format('Y-m-d') }}</td>
                                <td class="px-4 py-3">{{ $ledger->category->name ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $ledger->description }}</td>
                                <td class="px-4 py-3 text-right font-medium
                                    {{ $ledger->type === 1 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $ledger->type === 1 ? '+' : '-' }}
                                    {{ number_format($ledger->amount, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-3 text-center text-gray-500">
                                    No records found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-gray-50 text-sm font-semibold text-gray-700">
                        <tr>
                            <td colspan="3" class="px-4 py-3 text-right">Total</td>
                            <td class="px-4 py-3 text-right text-primary-900">
                                {{ number_format($total, 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- ===========================
                 Actions (Export Buttons)
            ============================ --}}
            <div class="flex justify-end space-x-4 pt-6">
                {{-- {{ route('client.ledgers.export', ['type' => 'pdf'] + request()->all()) }} --}}
                <a href=""
                   class="px-4 py-2 border border-gray-300 text-primary-700 rounded-lg hover:bg-gray-50 text-sm transition duration-300">
                    Export PDF
                </a>
                {{-- {{ route('client.ledgers.export', ['type' => 'excel'] + request()->all()) }} --}}
                <a href=""
                   class="px-4 py-2 bg-accent-500 text-white text-sm rounded-lg hover:bg-accent-600 transition duration-300">
                    Export Excel
                </a>
            </div>
        </div>
    </div>
</x-client.layout.app>
