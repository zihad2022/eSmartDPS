<?php

namespace Database\Factories;

use App\Enums\ProjectStatus;
use App\Models\Client;
use App\Models\Project;
use App\Models\ProjectCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'client_id' => Client::factory(),
            'project_category_id' => ProjectCategory::factory(),
            'name' => $name,
            'slug' => str($name)->slug()->value(),
            'investment_amount' => 50000,
            'expected_return' => 15,
            'expected_return_type' => 'percent',
            'start_date' => now()->subMonths(1),
            'end_date' => now()->addMonths(6),
            'description' => fake()->paragraph(),
            'status' => ProjectStatus::ACTIVE->value,
        ];
    }
}
