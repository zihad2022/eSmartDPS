<?php

namespace Database\Seeders;

use App\Enums\Ledger\LedgerType;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LedgerSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $transactions = [];

        // Generate last 12 months of income and expense data
        for ($i = 0; $i < 12; $i++) {
            $monthDate = $now->copy()->subMonths($i);

            // Income
            $transactions[] = [
                'ledger_category_id' => rand(1, 4),
                'client_id' => 1,
                'type' => LedgerType::INCOME,
                'description' => 'Monthly Income - '.$monthDate->format('F'),
                'amount' => rand(2000, 8000),
                'entry_date' => $monthDate->copy()->day(rand(1, 28))->toDateString(),
                'notes' => 'Generated income for '.$monthDate->format('F'),
                'created_at' => $now,
                'updated_at' => $now,
            ];

            // Expense
            $transactions[] = [
                'ledger_category_id' => rand(1, 4),
                'client_id' => 1,
                'type' => LedgerType::EXPENSE,
                'description' => 'Monthly Expense - '.$monthDate->format('F'),
                'amount' => rand(1000, 5000),
                'entry_date' => $monthDate->copy()->day(rand(1, 28))->toDateString(),
                'notes' => 'Generated expense for '.$monthDate->format('F'),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Add some yearly data for the past 2 years
        for ($year = 1; $year <= 2; $year++) {
            $yearDate = $now->copy()->subYears($year);

            $transactions[] = [
                'ledger_category_id' => rand(1, 4),
                'client_id' => 1,
                'type' => LedgerType::INCOME,
                'description' => 'Yearly Bonus Income '.$yearDate->year,
                'amount' => rand(10000, 20000),
                'entry_date' => $yearDate->copy()->month(rand(1, 12))->day(rand(1, 28))->toDateString(),
                'notes' => 'Special yearly income for '.$yearDate->year,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            $transactions[] = [
                'ledger_category_id' => rand(1, 4),
                'client_id' => 1,
                'type' => LedgerType::EXPENSE,
                'description' => 'Yearly Expense '.$yearDate->year,
                'amount' => rand(5000, 15000),
                'entry_date' => $yearDate->copy()->month(rand(1, 12))->day(rand(1, 28))->toDateString(),
                'notes' => 'Special yearly expense for '.$yearDate->year,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('ledgers')->insert($transactions);
    }
}
