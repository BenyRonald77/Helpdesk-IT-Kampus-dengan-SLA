<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database dengan data master dan data demo.
     *
     * Catatan: tidak memakai WithoutModelEvents, karena TicketSeeder sengaja
     * mengandalkan hook penghitungan sla_due_at pada model Ticket untuk sebagian
     * kasus, dan mem-bypassnya secara eksplisit untuk data historis lainnya.
     */
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            SlaRuleSeeder::class,
            TeamSeeder::class,
            TicketSeeder::class,
        ]);
    }
}
