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

        $transactions = [
            [
                'ledger_category_id' => rand(1, 4), // e.g., "Opening Balance"
                'client_id' => 1, // Link to a client if applicable
                'type' => LedgerType::INCOME,
                'description' => 'Opening Balance',
                'amount' => 5000.00,
                'entry_date' => $now->copy()->subDays(10)->toDateString(),
                'notes' => 'Initial balance when account was created.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'ledger_category_id' => rand(1, 4), // e.g., "Office Supplies"
                'client_id' => 1,
                'type' => LedgerType::EXPENSE,
                'description' => 'Office Supplies Purchase',
                'amount' => 1200.00,
                'entry_date' => $now->copy()->subDays(9)->toDateString(),
                'notes' => 'Bought printer ink, paper, and stationery.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'ledger_category_id' => rand(1, 4), // e.g., "Client Payments"
                'client_id' => 1,
                'type' => LedgerType::INCOME,
                'description' => 'Client Payment - Invoice #1023',
                'amount' => 2500.00,
                'entry_date' => $now->copy()->subDays(7)->toDateString(),
                'notes' => 'Payment received via bank transfer.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'ledger_category_id' => rand(1, 4), // e.g., "Utilities"
                'client_id' => 1,
                'type' => LedgerType::EXPENSE,
                'description' => 'Utility Bill Payment',
                'amount' => 900.00,
                'entry_date' => $now->copy()->subDays(5)->toDateString(),
                'notes' => 'Electricity and water bill for the month.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'ledger_category_id' => rand(1, 4), // e.g., "Service Income"
                'client_id' => 1,
                'type' => LedgerType::INCOME,
                'description' => 'Service Income - Project X',
                'amount' => 4000.00,
                'entry_date' => $now->copy()->subDays(2)->toDateString(),
                'notes' => 'Full payment for Project X development.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('ledgers')->insert($transactions);
    }
}
