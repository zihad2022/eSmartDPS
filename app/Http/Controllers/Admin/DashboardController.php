<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Client;
use App\Models\Invoice;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request for the admin dashboard.
     *
     * This method retrieves:
     * - Authenticated admin user
     * - Total number of parent clients
     * - Total invoice balance
     * - Monthly income data for the current year
     * - Recent payments within the last 7 days
     * - Recent admin activities within the last 7 days
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function __invoke(Request $request)
    {
        // Get authenticated admin user
        $admin = auth()->guard('admin')->user();

        // Count total clients (excluding sub-clients)
        $totalClients = Client::whereNull('parent_id')->count();

        // Calculate total invoice balance
        $totalBalance = Invoice::sum('invoice_amount');

        // Get monthly income for the current year (grouped by month)
        $monthlyIncome = Invoice::selectRaw('MONTH(created_at) as month, SUM(invoice_amount) as total')
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        // Ensure all 12 months are represented (fill missing months with 0)
        $incomeData = collect(range(1, 12))
            ->map(fn ($month) => $monthlyIncome[$month] ?? 0)
            ->toArray();

        // Chart.js data structure
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
                // Additional datasets (e.g., Expenses, Loans) can be added here
            ],
        ];

        // Fetch recent payments (last 7 days) with related client
        $recentPayments = Invoice::with('client')
            ->where('created_at', '>=', now()->subDays(7))
            ->latest()
            ->take(10)
            ->get();

        // Fetch recent admin activities (last 7 days) with related causer
        $recentActivities = Activity::with('causer')
            ->where('causer_type', \App\Models\Admin::class)
            ->where('created_at', '>=', now()->subDays(7))
            ->latest()
            ->take(10)
            ->get();

        // Render dashboard view with all collected data
        return view('admin.dashboard', compact(
            'admin',
            'totalClients',
            'totalBalance',
            'chartData',
            'recentPayments',
            'recentActivities'
        ));
    }
}
