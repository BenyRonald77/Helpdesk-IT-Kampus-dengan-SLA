<?php

namespace Database\Seeders;

use App\Enums\TicketPriority;
use App\Models\SlaRule;
use Illuminate\Database\Seeder;

class SlaRuleSeeder extends Seeder
{
    /**
     * Target waktu default. Bisa diedit admin lewat halaman pengaturan SLA.
     */
    public function run(): void
    {
        $defaults = [
            ['priority' => TicketPriority::Critical, 'response_minutes' => 15, 'resolution_minutes' => 240],
            ['priority' => TicketPriority::High, 'response_minutes' => 30, 'resolution_minutes' => 480],
            ['priority' => TicketPriority::Medium, 'response_minutes' => 60, 'resolution_minutes' => 1440],
            ['priority' => TicketPriority::Low, 'response_minutes' => 240, 'resolution_minutes' => 4320],
        ];

        foreach ($defaults as $rule) {
            SlaRule::query()->updateOrCreate(
                ['priority' => $rule['priority']->value],
                [
                    'response_minutes' => $rule['response_minutes'],
                    'resolution_minutes' => $rule['resolution_minutes'],
                ]
            );
        }
    }
}
