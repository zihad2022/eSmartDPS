<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Ledger;
use App\Models\LedgerCategory;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class LedgerSeeder extends Seeder
{
    public function run(): void
    {

        $clients = Client::where('parent_id', null)->get();
        // ✅ Create Ledger Categories for this client
        $categories = [
            ['name' => 'Sales', 'type' => 'income', 'description' => 'Income from product sales'],
            ['name' => 'Service Revenue', 'type' => 'income', 'description' => 'Income from services'],
            ['name' => 'Rent', 'type' => 'expense', 'description' => 'Office/shop rent'],
            ['name' => 'Utilities', 'type' => 'expense', 'description' => 'Electricity, water, gas bills'],
            ['name' => 'Salary', 'type' => 'expense', 'description' => 'Employee salaries'],
        ];

        $categoryIds = [];

        foreach ($categories as $data) {
            $category = LedgerCategory::create([
                'client_id' => $clients->random()->id,
                'name' => $data['name'],
                'type' => $data['type'],
                'description' => $data['description'],
                'is_active' => true,
            ]);

            $categoryIds[] = $category->id;
        }

        foreach (LedgerCategory::where('client_id', $clients->random()->id)->get() as $category) {
            for ($i = 1; $i <= 3; $i++) {
                Ledger::create([
                    'client_id' => $clients->random()->id,
                    'category_id' => $category->id,
                    'title' => $category->name.' Entry '.$i,
                    'description' => 'This is a sample entry for '.$category->name,
                    'amount' => rand(1000, 5000),
                    'type' => $category->type,
                    'entry_date' => Carbon::now()->subDays(rand(1, 30)),
                    'reference_no' => 'REF-'.strtoupper(uniqid()),
                    'payment_method' => ['cash', 'bank', 'card'][array_rand(['cash', 'bank', 'card'])],
                ]);
            }
        }
    }
}
