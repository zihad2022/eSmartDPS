<x-client.layout.app>
    @php
        // Get the status query parameter from the request (e.g., 'income', 'expense')
        $type = request()->type;

        // Map type values to human-readable titles for the page
        $titleMap = [
            'income' => 'Ledger Income',
            'expense' => 'Ledger Expenses',
        ];

        // Determine page title based on the type filter, default to 'All Ledgers'
        $pageTitle = $titleMap[$type] ?? 'All Ledgers';

        // Base breadcrumb items: Dashboard > All Ledgers
        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('client.dashboard')],
            ['label' => 'All Ledgers', 'url' => route('client.ledgers.index')],
        ];

        // Append specific type breadcrumb if a valid type is set
        if (isset($titleMap[$type])) {
            $breadcrumbItems[] = [
                'label' => $pageTitle,
                'url' => route('client.ledgers.index', ['type' => $type]),
            ];
        }
    @endphp

    {{-- Set the HTML page title dynamically --}}
    <x-slot:title>{{ $pageTitle }}</x-slot:title>

    {{-- Render breadcrumb navigation based on current page location --}}
    <x-breadcrumb :items="$breadcrumbItems" />

    <div class="">
        {{-- ===========================================================
            FLASH MESSAGES (SUCCESS / ERROR)
            This section displays feedback messages after actions
        ============================================================ --}}
        @if (session('success') || session('error'))
            <x-flash-message 
                :type="session('success') ? 'success' : 'error'" 
                :title="session('success') ? 'Success' : 'Error'" 
                :message="session('success') ?? session('error')" />
        @endif

        {{-- ===========================================================
            SUMMARY CARDS (Total Income, Expenses, Net Balance)
        ============================================================ --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6 mb-6">
            <x-card.stat-card 
                :label="'Total Income'" 
                :value="number_format($totalIncome)" 
                :bgColor="'bg-green-100'" 
                :textColor="'text-green-600'" 
                :icon="'fas fa-arrow-up'"
                :iconBgColor="'bg-green-100'" 
                :iconTextColor="'text-green-600'" 
                :valueTextColor="'text-green-600'" />

            <x-card.stat-card 
                :label="'Total Expenses'" 
                :value="number_format($totalExpense)" 
                :bgColor="'bg-red-100'" 
                :textColor="'text-red-600'"
                :icon="'fas fa-arrow-down'" 
                :iconBgColor="'bg-red-100'" 
                :iconTextColor="'text-red-600'" 
                :valueTextColor="'text-red-600'" />

            <x-card.stat-card 
                :label="'Net Balance'" 
                :value="number_format($totalIncome - $totalExpense)" 
                :bgColor="'bg-primary-100'" 
                :textColor="'text-primary-600'"
                :icon="'fas fa-wallet'" 
                :iconBgColor="'bg-primary-100'" 
                :iconTextColor="'text-primary-600'" 
                :valueTextColor="'text-primary-600'" />
        </div>

        {{-- ===========================================================
            CHART + CATEGORIES
            Left: Income vs Expenses Chart
            Right: Breakdown of Expense Categories
        ============================================================ --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            {{-- =======================
                INCOME VS EXPENSES CHART
            ======================== --}}
            <div class="bg-white rounded-xl shadow-sm p-6 lg:col-span-2">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold text-primary-900">Income vs Expenses</h3>
                    <div class="flex space-x-2">
                        <button class="px-3 py-1 text-xs font-medium bg-accent-100 text-accent-600 rounded-lg">
                            Monthly
                        </button>
                    </div>
                </div>
                <div class="h-64">
                    <canvas id="ledgerChart"></canvas>
                </div>
            </div>

            {{-- =======================
                All Categories
            ======================== --}}
            {{-- <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-primary-900 mb-6">All Categories</h3>
                
                <div class="space-y-4">
                    @foreach ($ledgerCategories as $ledgerCategory)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-red-500 rounded-full mr-3"></div>
                            <span class="text-sm text-primary-700">{{ $ledgerCategory->name }}</span>
                        </div>
                        <span class="text-sm font-medium text-primary-900">$5,200</span>
                    </div>
                    @endforeach
                </div>
            
                <div class="mt-6 text-right">
                    <a href="{{ route('client.ledger-categories.index') }}"
                       class="inline-block px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg shadow hover:bg-primary-700 transition">
                        View All Categories
                    </a>
                </div>
            </div> --}}
            
        </div>

        {{-- ===========================================================
            Ledgers SECTION
            Tabs (All, Income, Expenses) + Recent Ledgers Table
        ============================================================ --}}
        <div class="bg-white rounded-xl shadow-sm">
            {{-- =======================
                TAB NAVIGATION
            ======================== --}}
            <div class="border-b border-gray-200">
                <div class="flex space-x-8 px-6">
                    {{-- All Ledgers Tab --}}
                    <a href="{{ route('client.ledgers.index') }}"
                       class="tab-button py-4 px-2 font-medium text-sm
                       {{ request()->query('type') === null ? 'border-b-2 border-accent-500' : '' }}">
                        All Ledgers
                    </a>
                
                    {{-- Income Tab --}}
                    <a href="{{ route('client.ledgers.index', ['type' => 'income']) }}"
                       class="tab-button py-4 px-2 font-medium text-sm
                       {{ request()->query('type') === 'income' ? 'border-b-2 border-accent-500' : 'text-primary-600 hover:text-primary-900' }}">
                        Income
                    </a>
                
                    {{-- Expenses Tab --}}
                    <a href="{{ route('client.ledgers.index', ['type' => 'expense']) }}"
                       class="tab-button py-4 px-2 font-medium text-sm
                       {{ request()->query('type') === 'expense' ? 'border-b-2 border-accent-500' : 'text-primary-600 hover:text-primary-900' }}">
                        Expenses
                    </a>
                </div>
            </div>

            {{-- =======================
                RECENT Ledgers
            ======================== --}}
            <div class="p-6">
                {{-- Filters + Export --}}
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
                    <h3 class="text-lg font-semibold text-primary-900 mb-4 md:mb-0">Recent Ledgers</h3>
                    <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-4">
                        {{-- Date Filter --}}
                        <input type="date"
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-500">

                        {{-- Category Filter --}}
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

                        {{-- Export Button --}}
                        <button
                            class="bg-gray-100 hover:bg-gray-200 text-primary-700 px-4 py-2 rounded-lg text-sm font-medium transition duration-300">
                            <i class="fas fa-download mr-2"></i>Export
                        </button>
                    </div>
                </div>

                {{-- =======================
                    Ledgers TABLE
                ======================== --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">Description</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">Category</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($ledgers as $ledger)
                                <tr class="hover:bg-gray-50">
                                    {{-- Transaction Date --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-primary-600">
                                        {{ \Carbon\Carbon::parse($ledger->entry_date)->format('M d, Y') }}
                                    </td>

                                    {{-- Description + Notes --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-primary-900">{{ $ledger->description }}</div>
                                        @if ($ledger->notes)
                                            <div class="text-sm text-primary-500">{{ $ledger->notes }}</div>
                                        @endif
                                    </td>

                                    {{-- Category --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-primary-600">
                                        {{ $ledger->ledgerCategory->name ?? '—' }}
                                    </td>

                                    {{-- Type (Income/Expense) --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($ledger->type == App\Enums\Ledger\LedgerType::INCOME)
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                                Income
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                                Expense
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Amount --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ $ledger->type == App\Enums\Ledger\LedgerType::INCOME ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $ledger->type == App\Enums\Ledger\LedgerType::INCOME ? '+' : '-' }}${{ number_format($ledger->amount) }}
                                    </td>

                                    {{-- Actions (View, Edit, Delete) --}}
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
                                                <button type="button" class="text-red-600 hover:text-red-900 delete-btn"
                                                    title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                    {{-- Confirm modal component for deletion confirmation --}}
                                    <x-confirm-modal />
                                </tr>
                            @empty
                                {{-- If no ledgers exist --}}
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                        No ledger entries found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- =======================
                    PAGINATION + RESULTS INFO
                ======================== --}}
                <div class="px-6 py-4 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        {{-- Results Count --}}
                        <div class="text-sm text-primary-600">
                            @if ($ledgers->total() > 0)
                                Showing {{ $ledgers->firstItem() }} to {{ $ledgers->lastItem() }} of {{ $ledgers->total() }} results
                            @else
                                No results found.
                            @endif
                        </div>

                        {{-- Pagination Links --}}
                        <div class="flex space-x-2">
                            <x-pagination :paginator="$ledgers" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===========================================================
        CHART INITIALIZATION SCRIPT
        Chart.js for Income vs Expenses line graph
    ============================================================ --}}
    <script>
        const ledgerCanvas = document.getElementById('ledgerChart');
    
        if (ledgerCanvas) {
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
