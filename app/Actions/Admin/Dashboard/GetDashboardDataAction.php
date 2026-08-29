<?php

namespace App\Actions\Admin\Dashboard;

use App\Domain\Clients\Models\Client;
use App\Domain\Invoices\Models\Invoice;
use App\Domain\Packages\Models\Package;
use App\Models\Activity;
use App\Models\Admin;

use Illuminate\Support\Facades\DB;

class GetDashboardDataAction
{
    public function execute(Admin $admin): array
    {
        $monthSelect = DB::getDriverName() === 'sqlite'
            ? "CAST(strftime('%m', paid_at) AS INTEGER) as month, SUM(invoice_amount) as total"
            : 'MONTH(paid_at) as month, SUM(invoice_amount) as total';

        $monthlyIncome = Invoice::query()
            ->paid()
            ->selectRaw($monthSelect)
            ->whereYear('paid_at', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $incomeData = collect(range(1, 12))
            ->map(fn (int $month): int => (int) ($monthlyIncome[$month] ?? 0))
            ->all();

        return [
            'admin' => $admin,
            'totalClients' => Client::query()->parents()->count(),
            'totalBalance' => (int) Invoice::query()->paid()->sum('invoice_amount'),
            'totalUsers' => Admin::query()->count(),
            'totalPackages' => Package::query()->count(),
            'chartData' => [
                'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                'datasets' => [[
                    'label' => 'Income',
                    'data' => $incomeData,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'tension' => 0.3,
                    'fill' => true,
                ]],
            ],
            'recentPayments' => Invoice::query()
                ->with('client')
                ->paid()
                ->where('paid_at', '>=', now()->subDays(7))
                ->latest('paid_at')
                ->limit(4)
                ->get(),
            'recentActivities' => Activity::query()
                ->with('causer')
                ->where('causer_type', Admin::class)
                ->where('created_at', '>=', now()->subDays(7))
                ->latest()
                ->limit(3)
                ->get(),
        ];
    }
}
