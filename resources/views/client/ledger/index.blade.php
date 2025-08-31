<x-client.layout.app>
    <div class="">
        {{-- Display flash messages (success or error) if any --}}
        @if (session('success') || session('error'))
            <x-flash-message :type="session('success') ? 'success' : 'error'" :title="session('success') ? 'Success' : 'Error'" :message="session('success') ?? session('error')" />
        @endif

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6 mb-6">
            <x-card.stat-card :label="'Total Income'" :value="$totalIncome" :bgColor="'bg-green-100'" :textColor="'text-green-600'" :icon="'fas fa-arrow-up'"
                :iconBgColor="'bg-green-100'" :iconTextColor="'text-green-600'" :valueTextColor="'text-green-600'" />

            <x-card.stat-card :label="'Total Expenses'" :value="$totalExpense" :bgColor="'bg-red-100'" :textColor="'text-red-600'"
                :icon="'fas fa-arrow-down'" :iconBgColor="'bg-red-100'" :iconTextColor="'text-red-600'" :valueTextColor="'text-red-600'" />

            <x-card.stat-card :label="'Net Balance'" :value="$totalIncome - $totalExpense" :bgColor="'bg-primary-100'" :textColor="'text-primary-600'"
                :icon="'fas fa-wallet'" :iconBgColor="'bg-primary-100'" :iconTextColor="'text-primary-600'" :valueTextColor="'text-primary-600'" />
        </div>

        <!-- Chart and Recent Transactions -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Income vs Expenses Chart -->
            <div class="bg-white rounded-xl shadow-sm p-6 lg:col-span-2">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold text-primary-900">Income vs Expenses</h3>
                    <div class="flex space-x-2">
                        <button
                            class="px-3 py-1 text-xs font-medium bg-accent-100 text-accent-600 rounded-lg">Monthly</button>
                    </div>
                </div>
                <div class="h-64">
                    <canvas id="ledgerChart"></canvas>
                </div>
            </div>

            <!-- Categories Breakdown -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-primary-900 mb-6">Expense Categories</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-red-500 rounded-full mr-3"></div>
                            <span class="text-sm text-primary-700">Office Rent</span>
                        </div>
                        <span class="text-sm font-medium text-primary-900">$5,200</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
                            <span class="text-sm text-primary-700">Utilities</span>
                        </div>
                        <span class="text-sm font-medium text-primary-900">$1,850</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                            <span class="text-sm text-primary-700">Marketing</span>
                        </div>
                        <span class="text-sm font-medium text-primary-900">$3,200</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-yellow-500 rounded-full mr-3"></div>
                            <span class="text-sm text-primary-700">Supplies</span>
                        </div>
                        <span class="text-sm font-medium text-primary-900">$950</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-purple-500 rounded-full mr-3"></div>
                            <span class="text-sm text-primary-700">Other</span>
                        </div>
                        <span class="text-sm font-medium text-primary-900">$1,200</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs for Different Views -->
        <div class="bg-white rounded-xl shadow-sm">
            <div class="border-b border-gray-200">
                <div class="flex space-x-8 px-6">
                    <a href="{{ route('client.ledgers.index') }}"
                        class="tab-button active py-4 px-2 border-b-2 border-accent-500 font-medium text-sm"
                        data-tab="all">
                        All Transactions
                    </a>
                    <a href="{{ route('client.ledgers.index', ['type' => 'income']) }}"
                        class="tab-button py-4 active px-2 border-b-2 border-transparent font-medium text-sm text-primary-600 hover:text-primary-900"
                        data-tab="income">
                        Income
                    </a>
                    <a href="{{ route('client.ledgers.index', ['type' => 'expense']) }}"
                        class="tab-button py-4 px-2 border-b-2 border-transparent font-medium text-sm text-primary-600 hover:text-primary-900"
                        data-tab="expenses">
                        Expenses
                    </a>
                </div>
            </div>

            <div class="p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
                    <h3 class="text-lg font-semibold text-primary-900 mb-4 md:mb-0">Recent Transactions</h3>
                    <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-4">
                        <!-- Date Filter -->
                        <input type="date"
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-500">

                        <!-- Category Filter -->
                        <select
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-500">
                            <option>All Categories</option>
                            <option>Office Rent</option>
                            <option>Utilities</option>
                            <option>Marketing</option>
                            <option>Supplies</option>
                            <option>Member Payments</option>
                            <option>Investment Returns</option>
                        </select>

                        <!-- Export Button -->
                        <button
                            class="bg-gray-100 hover:bg-gray-200 text-primary-700 px-4 py-2 rounded-lg text-sm font-medium transition duration-300">
                            <i class="fas fa-download mr-2"></i>Export
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                    Date</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                    Description</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                    Category</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                    Type</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                    Amount</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($ledgers as $ledger)
                                <tr class="hover:bg-gray-50">
                                    {{-- Date --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-primary-600">
                                        {{ \Carbon\Carbon::parse($ledger->entry_date)->format('M d, Y') }}
                                    </td>

                                    {{-- Description + Notes --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-primary-900">{{ $ledger->description }}
                                        </div>
                                        @if ($ledger->notes)
                                            <div class="text-sm text-primary-500">{{ $ledger->notes }}</div>
                                        @endif
                                    </td>

                                    {{-- Category --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-primary-600">
                                        {{ $ledger->ledgerCategory->name ?? '—' }}
                                    </td>

                                    {{-- Type --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($ledger->type == App\Enums\Ledger\LedgerType::INCOME)
                                            <span
                                                class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Income</span>
                                        @else
                                            <span
                                                class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">Expense</span>
                                        @endif
                                    </td>

                                    {{-- Amount --}}
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ $ledger->type == App\Enums\Ledger\LedgerType::INCOME ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $ledger->type == App\Enums\Ledger\LedgerType::INCOME ? '+' : '-' }}${{ number_format($ledger->amount) }}
                                    </td>

                                    {{-- Actions --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('client.ledgers.show', $ledger) }}"
                                                class="text-accent-600 hover:text-accent-900" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('client.ledgers.edit', $ledger) }}"
                                                class="text-secondary-600 hover:text-secondary-900" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('client.ledgers.destroy', $ledger) }}"
                                                method="POST" onsubmit="return confirm('Are you sure?');"
                                                class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900"
                                                    title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                        No ledger entries found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- Pagination Controls and Showing Results Info -->
                <div class="px-6 py-4 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        {{-- Showing items range and total count --}}
                        <div class="text-sm text-primary-600">
                            @if ($ledgers->total() > 0)
                                Showing {{ $ledgers->firstItem() }} to {{ $ledgers->lastItem() }} of
                                {{ $ledgers->total() }} results
                            @else
                                No results found.
                            @endif
                        </div>

                        {{-- Pagination links --}}
                        <div class="flex space-x-2">
                            <x-pagination :paginator="$ledgers" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const ledgerCanvas = document.getElementById('ledgerChart');
    
        if (ledgerCanvas) { // only run if element exists
            const ctx = ledgerCanvas.getContext('2d');
            const chartData = @json($chartData);
            const ledgerChart = new Chart(ctx, {
                type: 'line',
                data: chartData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '₹' + (value / 1000) + 'K';
                                }
                            }
                        }
                    }
                }
            });
        }
    </script>
</x-client.layout.app>
