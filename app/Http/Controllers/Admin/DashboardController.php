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

        $chartData = [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            'datasets' => [
                [
                    'label' => 'Income',
                    'data' => [200000, 250000, 220000, 280000, 320000, 350000, 200000, 250000, 220000, 280000, 320000, 350000],
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'tension' => 0.3,
                    'fill' => true,
                ],
                [
                    'label' => 'Expenses',
                    'data' => [80000, 95000, 70000, 110000, 130000, 120000, 80000, 95000, 70000, 110000, 130000, 120000],
                    'borderColor' => '#ef4444',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'tension' => 0.3,
                    'fill' => true,
                ],
                [
                    'label' => 'Loans',
                    'data' => [50000, 75000, 60000, 85000, 90000, 82000, 50000, 75000, 60000, 85000, 90000, 82000],
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                    'tension' => 0.3,
                    'fill' => true,
                ],
            ],
        ];

        return view('admin.dashboard', compact('admin', 'totalClients', 'totalBalance', 'chartData'));
    }
}
