<?php

namespace Database\Seeders;

use App\Models\LedgerCategory;
use Illuminate\Database\Seeder;

class LedgerCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clientId = 1;

        $categories = [
            [
                'client_id' => $clientId,
                'name' => 'Sales',
                'description' => 'All income from product or service sales',
            ],
            [
                'client_id' => $clientId,
                'name' => 'Purchases',
                'description' => 'Expenses related to purchasing goods',
            ],
            [
                'client_id' => $clientId,
                'name' => 'Operating Expenses',
                'description' => 'General business expenses such as rent, utilities, salaries',
            ],
            [
                'client_id' => $clientId,
                'name' => 'Miscellaneous',
                'description' => 'Other income or expenses not categorized',
            ],
        ];

        foreach ($categories as $category) {
            LedgerCategory::create($category);
        }
    }
}
