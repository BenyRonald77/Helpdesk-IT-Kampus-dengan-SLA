<?php

namespace Database\Factories;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    public function definition(): array
    {
        return [
            'requester_id' => User::factory()->pelapor(),
            'category_id' => Category::factory(),
            'team_id' => null,
            'assigned_to' => null,
            'title' => fake()->sentence(6),
            'description' => fake()->paragraph(),
            'priority' => TicketPriority::Medium,
            'status' => TicketStatus::Open,
        ];
    }

    public function priority(TicketPriority $priority): static
    {
        return $this->state(['priority' => $priority]);
    }

    public function resolved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TicketStatus::Resolved,
        ]);
    }
}
