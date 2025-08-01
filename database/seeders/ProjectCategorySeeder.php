<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\ProjectCategory;
use Illuminate\Database\Seeder;

class ProjectCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Real Estate',
            'Agriculture',
            'Stock Investment',
            'Small Business',
            'Startup Funding',
            'E-commerce',
        ];

        // Loop through only top-level clients (parent_id is null)
        Client::whereNull('parent_id')->each(function ($client) use ($categories) {
            foreach ($categories as $name) {
                $exists = ProjectCategory::where('client_id', $client->id)
                    ->where('name', $name)
                    ->exists();

                if (! $exists) {
                    ProjectCategory::create([
                        'client_id' => $client->id,
                        'name' => $name,
                    ]);
                }
            }
        });
    }
}
