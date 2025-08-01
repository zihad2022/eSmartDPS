<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Share;
use Illuminate\Database\Seeder;

class ShareSeeder extends Seeder
{
    public function run(): void
    {
        $clients = Client::whereNull('parent_id')->get();
        $shares = [
            [
                'name' => 'Basic',
                'price' => 2000,
                'description' => 'Basic monthly share plan for regular members.',
                'is_active' => true,
            ],
            [
                'name' => 'Advanced',
                'price' => 5000,
                'description' => 'Advanced share plan with higher contribution.',
                'is_active' => true,
            ],
            [
                'name' => 'Premium',
                'price' => 10000,
                'description' => 'Premium plan for high-value members.',
                'is_active' => true,
            ],
        ];

        foreach ($shares as $share) {
            Share::updateOrCreate(
                ['name' => $share['name'], 'client_id' => $clients->random()->id],
                $share
            );
        }
    }
}
