<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Clients\Models\Client;
use App\Domain\Packages\Models\Package;
use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Admin;
use App\Domain\Billing\Models\Invoice;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard with statistics and recent activity.
     */
    public function __invoke(Request $request)
    {
        // -----------------------------
        // 1. Get authenticated admin user
        // -----------------------------
        $admin = auth()->guard('admin')->user();

        // -----------------------------
        // 2. Count total parent clients
        // -----------------------------
        $totalClients = Client::whereNull('parent_id')->count();

        // -----------------------------
        // 3. Calculate total invoice balance
        // -----------------------------
        $totalBalance = Invoice::sum('invoice_amount');

        // -----------------------------
        // 4. Prepare monthly income data for the current year
        // -----------------------------
        $monthlyIncome = Invoice::selectRaw('MONTH(created_at) as month, SUM(invoice_amount) as total')
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        // -----------------------------
        // 5. Ensure all 12 months are represented
        // -----------------------------
        $incomeData = collect(range(1, 12))
            ->map(fn ($month) => $monthlyIncome[$month] ?? 0)
            ->toArray();

        // -----------------------------
        // 6. Build Chart.js data structure
        // -----------------------------
        $chartData = [
            'labels' => ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
            'datasets' => [
                [
                    'label' => 'Income',
                    'data' => $incomeData,
                    'borderColor' => '#10b981', // Tailwind green-500
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'tension' => 0.3,
                    'fill' => true,
                ],
            ],
        ];

        // -----------------------------
        // 7. Fetch recent payments (last 7 days)
        // -----------------------------
        $recentPayments = Invoice::with('client')
            ->where('created_at', '>=', now()->subDays(7))
            ->latest()
            ->take(4)
            ->get();

        // -----------------------------
        // 8. Fetch recent admin activities (last 7 days)
        // -----------------------------
        $recentActivities = Activity::with('causer')
            ->where('causer_type', Admin::class)
            ->where('created_at', '>=', now()->subDays(7))
            ->latest()
            ->take(3)
            ->get();

        // -----------------------------
        // 9. Count total admins
        // -----------------------------
        $totalUsers = Admin::count();

        // -----------------------------
        // 10. Count total packages
        // -----------------------------
        $totalPackages = Package::count();

        // -----------------------------
        // 11. Return view with all collected data
        // -----------------------------
        return view('admin.dashboard', compact(
            'admin',
            'totalClients',
            'totalBalance',
            'totalUsers',
            'totalPackages',
            'chartData',
            'recentPayments',
            'recentActivities'
        ));
    }
}
