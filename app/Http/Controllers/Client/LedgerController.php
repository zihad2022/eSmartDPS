<?php

namespace App\Http\Controllers\Client;

use App\Enums\Ledger\LedgerType;
use App\Http\Controllers\Controller;
use App\Models\Ledger;
use App\Models\LedgerCategory;
use Illuminate\Http\Request;

class LedgerController extends Controller
{
    /**
     * Display a listing of the resource (all ledgers).
     * Supports filtering by type (income/expense) and date range.
     */
    public function index(Request $request)
    {
        $clientId = owner_client_id();

        // Filter by type: income, expense, or all
        if ($request->type === 'income') {
            $ledgers = Ledger::with('ledgerCategory')
                ->where('client_id', $clientId)
                ->where('type', LedgerType::INCOME->value)
                ->when($request->date_from, fn($q) => $q->whereDate('entry_date', '>=', $request->date_from))
                ->when($request->date_to, fn($q) => $q->whereDate('entry_date', '<=', $request->date_to))
                ->latest('entry_date')
                ->paginate(10);
        } elseif ($request->type === 'expense') {
            $ledgers = Ledger::with('ledgerCategory')
                ->where('client_id', $clientId)
                ->where('type', LedgerType::EXPENSE->value)
                ->when($request->date_from, fn($q) => $q->whereDate('entry_date', '>=', $request->date_from))
                ->when($request->date_to, fn($q) => $q->whereDate('entry_date', '<=', $request->date_to))
                ->latest('entry_date')
                ->paginate(10);
        } else {
            $ledgers = Ledger::with('ledgerCategory')
                ->where('client_id', $clientId)
                ->when($request->type, fn($q) => $q->where('type', $request->type))
                ->when($request->date_from, fn($q) => $q->whereDate('entry_date', '>=', $request->date_from))
                ->when($request->date_to, fn($q) => $q->whereDate('entry_date', '<=', $request->date_to))
                ->latest('entry_date')
                ->paginate(10);
        }

        // Summary totals
        $totalIncome = Ledger::where('client_id', $clientId)
            ->where('type', LedgerType::INCOME)
            ->sum('amount');

        $totalExpense = Ledger::where('client_id', $clientId)
            ->where('type', LedgerType::EXPENSE)
            ->sum('amount');

        // Monthly income summary for current year
        $monthlyIncome = Ledger::selectRaw('MONTH(entry_date) as month, SUM(amount) as total')
            ->where('client_id', $clientId)
            ->where('type', LedgerType::INCOME)
            ->whereYear('entry_date', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        // Monthly expense summary for current year
        $monthlyExpense = Ledger::selectRaw('MONTH(entry_date) as month, SUM(amount) as total')
            ->where('client_id', $clientId)
            ->where('type', LedgerType::EXPENSE)
            ->whereYear('entry_date', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        // Normalize data for charts (fill empty months with 0)
        $incomeData = collect(range(1, 12))
            ->map(fn($month) => $monthlyIncome[$month] ?? 0)
            ->toArray();

        $expenseData = collect(range(1, 12))
            ->map(fn($month) => $monthlyExpense[$month] ?? 0)
            ->toArray();

        // Fetch limited categories for dashboard overview
        $ledgerCategories = LedgerCategory::where('client_id', $clientId)
            ->take(8)
            ->get();

        // Chart.js dataset
        $chartData = [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            'datasets' => [
                [
                    'label' => 'Income',
                    'data' => $incomeData,
                    'borderColor' => '#10b981', // Tailwind green-500
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'tension' => 0.3,
                    'fill' => true,
                ],
                [
                    'label' => 'Expense',
                    'data' => $expenseData,
                    'borderColor' => '#ef4444', // Tailwind red-500
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'tension' => 0.3,
                    'fill' => true,
                ],
            ],
        ];

        return view('client.ledger.index', compact(
            'ledgers',
            'totalIncome',
            'totalExpense',
            'monthlyIncome',
            'monthlyExpense',
            'incomeData',
            'expenseData',
            'chartData',
            'ledgerCategories'
        ));
    }

    /**
     * Show the form for creating a new ledger entry.
     */
    public function create()
    {
        // Fetch categories for dropdown
        $ledgerCategories = LedgerCategory::where('client_id', owner_client_id())
            ->pluck('name', 'id');

        return view('client.ledger.form', compact('ledgerCategories'));
    }

    /**
     * Store a newly created ledger entry.
     */
    public function store(Request $request)
    {
        // Validate input
        $data = $request->validate([
            'ledger_category_id' => 'required|exists:ledger_categories,id',
            'type' => 'required|in:' . LedgerType::INCOME->value . ',' . LedgerType::EXPENSE->value,
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'entry_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        // Attach ledger to the client
        $data['client_id'] = owner_client_id();

        Ledger::create($data);

        return redirect()->route('client.ledgers.index')
            ->with('success', 'Ledger entry created successfully.');
    }

    /**
     * Display the specified ledger entry.
     */
    public function show(Ledger $ledger)
    {
        // Ensure the ledger belongs to this client
        $this->authorizeLedger($ledger);

        return view('client.ledger.show', compact('ledger'));
    }

    /**
     * Show the form for editing the specified ledger entry.
     */
    public function edit(Ledger $ledger)
    {
        $this->authorizeLedger($ledger);

        $ledgerCategories = LedgerCategory::where('client_id', owner_client_id())
            ->pluck('name', 'id');

        return view('client.ledger.form', compact('ledger', 'ledgerCategories'));
    }

    /**
     * Update the specified ledger entry.
     */
    public function update(Request $request, Ledger $ledger)
    {
        $this->authorizeLedger($ledger);

        // Validate input
        $data = $request->validate([
            'ledger_category_id' => 'required|exists:ledger_categories,id',
            'type' => 'required|in:' . LedgerType::INCOME->value . ',' . LedgerType::EXPENSE->value,
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'entry_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $ledger->update($data);

        return redirect()->route('client.ledgers.index')
            ->with('success', 'Ledger entry updated successfully.');
    }

    /**
     * Remove the specified ledger entry.
     */
    public function destroy(Ledger $ledger)
    {
        $this->authorizeLedger($ledger);

        $ledger->delete();

        return redirect()->route('client.ledgers.index')
            ->with('success', 'Ledger entry deleted successfully.');
    }

    /**
     * Ensure the given ledger belongs to the authenticated client.
     */
    protected function authorizeLedger(Ledger $ledger)
    {
        if ($ledger->client_id !== owner_client_id()) {
            abort(403, 'Unauthorized action.');
        }
    }
}
