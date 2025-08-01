<?php

namespace Database\Seeders;

use App\Enums\ProjectStatus;
use App\Models\Project;
use App\Models\ProjectCategory;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $clientId = 1;

        $categories = ProjectCategory::where('client_id', $clientId)->pluck('id', 'name');

        $projects = [
            [
                'name' => 'Real Estate Investment',
                'category' => 'Real Estate',
                'investment_amount' => 15000,
                'expected_return' => 15.0,
                'start_date' => '2025-01-15',
                'end_date' => '2025-12-15',
                'description' => 'Development of a residential complex with high ROI due to urban demand.',
                'status' => ProjectStatus::CANCELLED->value,
            ],
            [
                'name' => 'Stock Market Portfolio',
                'category' => 'Stock Investment',
                'investment_amount' => 8500,
                'expected_return' => 12.0,
                'start_date' => '2025-02-20',
                'end_date' => '2025-08-20',
                'description' => 'Diversified equity investment focusing on tech and healthcare stocks.',
                'status' => ProjectStatus::COMPLETED->value,
            ],
            [
                'name' => 'Agricultural Expansion',
                'category' => 'Agriculture',
                'investment_amount' => 9700,
                'expected_return' => 18.0,
                'start_date' => '2024-06-01',
                'end_date' => '2025-06-01',
                'description' => 'Organic farming initiative with export opportunities in EU markets.',
                'status' => ProjectStatus::COMPLETED->value,
            ],
            [
                'name' => 'Small Business Loan',
                'category' => 'Small Business',
                'investment_amount' => 12000,
                'expected_return' => 8.0,
                'start_date' => '2025-03-10',
                'end_date' => '2026-09-10',
                'description' => 'Microloan project for a local handmade crafts business.',
                'status' => ProjectStatus::ACTIVE->value,
            ],
            [
                'name' => 'Community Grocery Chain',
                'category' => 'Small Business',
                'investment_amount' => 10000,
                'expected_return' => 10.0,
                'start_date' => '2024-10-01',
                'end_date' => '2025-10-01',
                'description' => 'Opening a chain of local grocery shops in rural areas.',
                'status' => ProjectStatus::CANCELLED->value,
            ],
        ];

        foreach ($projects as $data) {
            $categoryId = $categories[$data['category']] ?? null;

            if (! $categoryId) {
                continue;
            }

            // Calculate duration in months
            $startDate = Carbon::parse($data['start_date']);
            $endDate = Carbon::parse($data['end_date']);
            $duration = $startDate->diffInMonths($endDate);

            Project::create([
                'client_id' => $clientId,
                'project_category_id' => $categoryId,
                'name' => $data['name'],
                'slug' => Str::slug($data['name']),
                'investment_amount' => $data['investment_amount'],
                'expected_return' => $data['expected_return'],
                'start_date' => $startDate,
                'end_date' => $endDate,
                'duration' => $duration,
                'description' => $data['description'],
                'status' => $data['status'],
            ]);
        }
    }
}
