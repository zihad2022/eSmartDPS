<?php

namespace App\Http\Controllers\Client;

use App\Enums\Ledger\LedgerType;
use App\Http\Controllers\Controller;
use App\Models\ClientSetting;
use App\Models\Ledger;
use App\Models\LedgerCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LedgerController extends Controller
{
    /**
     * Display a listing of the ledgers.
     * Supports filtering by type, date range, and search query.
     */
    public function index(Request $request)
    {
        // -----------------------------
        // 1. Get main client ID which is parent client ID
        // -----------------------------
        $clientId = owner_client_id();

        // -----------------------------
        // 2. Handle search query
        // -----------------------------
        $search = $request->get('search');

        // -----------------------------
        // 3. Build filtered ledger list
        // -----------------------------
        $ledgers = Ledger::with('ledgerCategory')
            ->where('client_id', $clientId)
            ->when($request->type && in_array($request->type, [LedgerType::INCOME->value, LedgerType::EXPENSE->value]), 
                fn($q) => $q->where('type', $request->type)
            )
            ->when($request->date_from, fn($q) => $q->whereDate('entry_date', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('entry_date', '<=', $request->date_to))
            ->when($search, fn($q) => $q->where(function($query) use ($search) {
                $query->where('description', 'like', "%{$search}%")
                      ->orWhere('notes', 'like', "%{$search}%")
                      ->orWhere('amount', 'like', "%{$search}%");
            }))
            ->latest('entry_date')
            ->paginate(10)
            ->appends($request->query());

        // -----------------------------
        // 4. Calculate total income and expense
        // -----------------------------
        $totalIncome = Ledger::where('client_id', $clientId)->where('type', LedgerType::INCOME)->sum('amount');
        $totalExpense = Ledger::where('client_id', $clientId)->where('type', LedgerType::EXPENSE)->sum('amount');

        // -----------------------------
        // 5. Prepare monthly chart data
        // -----------------------------
        $monthlyIncome = Ledger::selectRaw('MONTH(entry_date) as month, SUM(amount) as total')
            ->where('client_id', $clientId)
            ->where('type', LedgerType::INCOME)
            ->whereYear('entry_date', now()->year)
            ->groupBy('month')
            ->pluck('total', 'month');

        $monthlyExpense = Ledger::selectRaw('MONTH(entry_date) as month, SUM(amount) as total')
            ->where('client_id', $clientId)
            ->where('type', LedgerType::EXPENSE)
            ->whereYear('entry_date', now()->year)
            ->groupBy('month')
            ->pluck('total', 'month');

        $incomeData = collect(range(1,12))->map(fn($month) => $monthlyIncome[$month] ?? 0)->toArray();
        $expenseData = collect(range(1,12))->map(fn($month) => $monthlyExpense[$month] ?? 0)->toArray();

        $monthlyChartData = [
            'labels' => ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
            'datasets' => [
                [
                    'label' => 'Income',
                    'data' => $incomeData,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'tension' => 0.3,
                    'fill' => true,
                ],
                [
                    'label' => 'Expense',
                    'data' => $expenseData,
                    'borderColor' => '#ef4444',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'tension' => 0.3,
                    'fill' => true,
                ],
            ],
        ];

        // -----------------------------
        // 5. Prepare yearly chart data
        // -----------------------------
        $yearlyIncome = Ledger::selectRaw('YEAR(entry_date) as year, SUM(amount) as total')
            ->where('client_id', $clientId)
            ->where('type', LedgerType::INCOME)
            ->groupBy('year')
            ->pluck('total', 'year');

        $yearlyExpense = Ledger::selectRaw('YEAR(entry_date) as year, SUM(amount) as total')
            ->where('client_id', $clientId)
            ->where('type', LedgerType::EXPENSE)
            ->groupBy('year')
            ->pluck('total', 'year');

        $years = $yearlyIncome->keys()->merge($yearlyExpense->keys())->unique()->sort()->values();
        $yearlyIncomeData = $years->map(fn($year) => $yearlyIncome[$year] ?? 0)->toArray();
        $yearlyExpenseData = $years->map(fn($year) => $yearlyExpense[$year] ?? 0)->toArray();

        $yearlyChartData = [
            'labels' => $years->toArray(),
            'datasets' => [
                [
                    'label' => 'Income',
                    'data' => $yearlyIncomeData,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'tension' => 0.3,
                    'fill' => true,
                ],
                [
                    'label' => 'Expense',
                    'data' => $yearlyExpenseData,
                    'borderColor' => '#ef4444',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'tension' => 0.3,
                    'fill' => true,
                ],
            ],
        ];

        // -----------------------------
        // 6. Fetch categories and client settings
        // -----------------------------
        $ledgerCategories = LedgerCategory::where('client_id', $clientId)->take(8)->get();
        $settings = ClientSetting::where('client_id', $clientId)->first();

        // -----------------------------
        // 7. Return view with all data
        // -----------------------------
        return view('client.ledger.index', compact(
            'ledgers',
            'totalIncome',
            'totalExpense',
            'incomeData',
            'expenseData',
            'monthlyChartData',
            'yearlyChartData',
            'ledgerCategories',
            'settings',
            'search'
        ));
    }

    /**
     * Show the form to create a new ledger entry.
     */
    public function create()
    {
        // -----------------------------
        // 1. Fetch ledger categories
        // -----------------------------
        $ledgerCategories = LedgerCategory::where('client_id', owner_client_id())->pluck('name','id');

        // -----------------------------
        // 2. Return create form view
        // -----------------------------
        return view('client.ledger.form', compact('ledgerCategories'));
    }

    /**
     * Store a newly created ledger entry.
     */
    public function store(Request $request)
    {
        // -----------------------------
        // 1. Validate request data
        // -----------------------------
        $data = $request->validate([
            'ledger_category_id' => [
                'required',
                'integer',
                Rule::exists('ledger_categories', 'id')
                    ->where(fn ($query) => $query->where('client_id', owner_client_id())),
            ],
            'type' => 'required|in:' . LedgerType::INCOME->value . ',' . LedgerType::EXPENSE->value,
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'entry_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        // -----------------------------
        // 2. Assign client ID
        // -----------------------------
        $data['client_id'] = owner_client_id();

        // -----------------------------
        // 3. Create ledger entry
        // -----------------------------
        Ledger::create($data);

        // -----------------------------
        // 4. Redirect back with success message
        // -----------------------------
        return redirect()->route('client.ledgers.index')
            ->with('success', 'Ledger entry created successfully.');
    }

    /**
     * Display a specific ledger entry.
     */
    public function show(Ledger $ledger)
    {
        $this->authorizeLedger($ledger);
        return view('client.ledger.show', compact('ledger'));
    }

    /**
     * Show the form for editing a ledger entry.
     */
    public function edit(Ledger $ledger)
    {
        $this->authorizeLedger($ledger);

        $ledgerCategories = LedgerCategory::where('client_id', owner_client_id())->pluck('name','id');

        return view('client.ledger.form', compact('ledger','ledgerCategories'));
    }

    /**
     * Update an existing ledger entry.
     */
    public function update(Request $request, Ledger $ledger)
    {
        $this->authorizeLedger($ledger);

        // -----------------------------
        // 1. Validate request data
        // -----------------------------
        $data = $request->validate([
            'ledger_category_id' => [
                'required',
                'integer',
                Rule::exists('ledger_categories', 'id')
                    ->where(fn ($query) => $query->where('client_id', owner_client_id())),
            ],
            'type' => 'required|in:' . LedgerType::INCOME->value . ',' . LedgerType::EXPENSE->value,
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'entry_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        // -----------------------------
        // 2. Update ledger entry
        // -----------------------------
        $ledger->update($data);

        return redirect()->route('client.ledgers.index')
            ->with('success', 'Ledger entry updated successfully.');
    }

    /**
     * Delete a ledger entry.
     */
    public function destroy(Ledger $ledger)
    {
        $this->authorizeLedger($ledger);

        $ledger->delete();

        return redirect()->route('client.ledgers.index')
            ->with('success', 'Ledger entry deleted successfully.');
    }

    /**
     * Ensure the ledger belongs to the authenticated client.
     */
    protected function authorizeLedger(Ledger $ledger)
    {
        if ($ledger->client_id !== owner_client_id()) {
            abort(403, 'Unauthorized action.');
        }
    }
}
