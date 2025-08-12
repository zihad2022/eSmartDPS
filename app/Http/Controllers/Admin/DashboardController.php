<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Invoice;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $admin = auth()->guard('admin')->user();
        $totalClients = Client::whereNull('parent_id')->count();
        $totalBalance = Invoice::sum('invoice_amount');

        // Get monthly income for the current year
        $monthlyIncome = Invoice::selectRaw('MONTH(created_at) as month, SUM(invoice_amount) as total')
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        // Fill missing months with 0
        $incomeData = [];
        for ($i = 1; $i <= 12; $i++) {
            $incomeData[] = $monthlyIncome[$i] ?? 0;
        }

        $chartData = [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            'datasets' => [
                [
                    'label' => 'Income',
                    'data' => $incomeData,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'tension' => 0.3,
                    'fill' => true,
                ],
                // Keep your other datasets (Expenses, Loans) if needed
            ],
        ];

        return view('admin.dashboard', compact('admin', 'totalClients', 'totalBalance', 'chartData'));
    }
}
