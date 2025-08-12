<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            AdminRolePermissionSeeder::class,
            AdminSeeder::class,
            PackageSeeder::class,
            ClientSeeder::class,
            LedgerSeeder::class,
            InvoiceSeeder::class,
            TicketSeeder::class,
            // ProjectCategorySeeder::class,
            // ProjectSeeder::class,
        ]);
    }
}
