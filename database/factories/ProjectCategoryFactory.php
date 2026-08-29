<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\ProjectCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProjectCategory>
 */
class ProjectCategoryFactory extends Factory
{
    protected $model = ProjectCategory::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'client_id' => Client::factory(),
            'name' => $name,
            'slug' => str($name)->slug()->value(),
        ];
    }
}
