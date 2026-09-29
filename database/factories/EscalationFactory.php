<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Escalation>
 */
class EscalationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'ticket_id' => Ticket::factory(),
            'escalated_at' => now(),
            'escalated_to' => User::factory()->admin(),
            'reason' => 'Lewat target resolusi',
        ];
    }
}
