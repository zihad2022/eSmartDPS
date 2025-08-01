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
            PakageSeeder::class,
            ClientSeeder::class,
            ShareSeeder::class,
            LedgerSeeder::class,
            // InvoiceSeeder::class,
            // TicketSeeder::class,
            MemberSeeder::class,
            // ProjectCategorySeeder::class,
            // ProjectSeeder::class,
        ]);
    }
}
