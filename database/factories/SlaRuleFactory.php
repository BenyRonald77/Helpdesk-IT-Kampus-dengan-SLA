<?php

namespace Database\Factories;

use App\Enums\TicketPriority;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\SlaRule>
 */
class SlaRuleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'priority' => TicketPriority::Medium,
            'response_minutes' => 60,
            'resolution_minutes' => 1440,
        ];
    }
}
