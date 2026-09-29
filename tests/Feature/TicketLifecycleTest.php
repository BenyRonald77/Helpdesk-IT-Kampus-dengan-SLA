<?php

namespace Tests\Feature;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Category;
use App\Models\SlaRule;
use App\Models\Team;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketLifecycleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        SlaRule::create(['priority' => TicketPriority::Medium, 'response_minutes' => 60, 'resolution_minutes' => 1440]);
    }

    public function test_pelapor_can_create_a_ticket_and_see_it_in_their_own_list(): void
    {
        $category = Category::create(['name' => 'Jaringan']);
        $pelapor = User::factory()->pelapor()->create();

        $response = $this->actingAs($pelapor)->post(route('tickets.store'), [
            'category_id' => $category->id,
            'priority' => 'medium',
            'title' => 'Wifi lab 3 mati total',
            'description' => 'Sejak pagi wifi lab 3 tidak menyala sama sekali.',
        ]);

        $ticket = Ticket::first();
        $response->assertRedirect(route('tickets.show', $ticket));
        $this->assertNotNull($ticket->sla_due_at);

        $this->actingAs($pelapor)->get(route('tickets.index'))->assertOk()->assertSee('Wifi lab 3 mati total');

        // Pelapor lain tidak boleh melihat tiket ini.
        $otherPelapor = User::factory()->pelapor()->create();
        $this->actingAs($otherPelapor)->get(route('tickets.show', $ticket))->assertForbidden();
    }

    public function test_technician_can_self_assign_and_resolve_a_ticket(): void
    {
        $category = Category::create(['name' => 'Hardware']);
        $supervisor = User::factory()->supervisor()->create();
        $team = Team::create(['name' => 'Tim Hardware', 'supervisor_id' => $supervisor->id]);
        $technician = User::factory()->teknisi()->create(['team_id' => $team->id]);
        $pelapor = User::factory()->pelapor()->create();

        $ticket = Ticket::create([
            'requester_id' => $pelapor->id,
            'category_id' => $category->id,
            'title' => 'Proyektor mati',
            'description' => 'Tidak menyala.',
            'priority' => TicketPriority::Medium,
            'status' => TicketStatus::Open,
        ]);

        $this->actingAs($technician)
            ->post(route('tickets.self-assign', $ticket))
            ->assertRedirect(route('tickets.show', $ticket));

        $ticket->refresh();
        $this->assertSame($technician->id, $ticket->assigned_to);
        $this->assertSame($team->id, $ticket->team_id);
        $this->assertSame(TicketStatus::InProgress, $ticket->status);
        $this->assertNotNull($ticket->first_responded_at);

        $this->actingAs($technician)
            ->patch(route('tickets.update-status', $ticket), ['status' => 'resolved'])
            ->assertRedirect(route('tickets.show', $ticket));

        $ticket->refresh();
        $this->assertSame(TicketStatus::Resolved, $ticket->status);
        $this->assertNotNull($ticket->resolved_at);
    }

    public function test_supervisor_sees_only_their_teams_escalated_tickets(): void
    {
        $category = Category::create(['name' => 'Jaringan']);
        $pelapor = User::factory()->pelapor()->create();

        $supervisorA = User::factory()->supervisor()->create();
        $teamA = Team::create(['name' => 'Tim A', 'supervisor_id' => $supervisorA->id]);

        $supervisorB = User::factory()->supervisor()->create();
        $teamB = Team::create(['name' => 'Tim B', 'supervisor_id' => $supervisorB->id]);

        $escalatedInA = Ticket::create([
            'requester_id' => $pelapor->id,
            'category_id' => $category->id,
            'team_id' => $teamA->id,
            'title' => 'Eskalasi tim A',
            'description' => 'x',
            'priority' => TicketPriority::Medium,
            'status' => TicketStatus::Escalated,
            'sla_breached' => true,
        ]);

        Ticket::create([
            'requester_id' => $pelapor->id,
            'category_id' => $category->id,
            'team_id' => $teamB->id,
            'title' => 'Eskalasi tim B',
            'description' => 'x',
            'priority' => TicketPriority::Medium,
            'status' => TicketStatus::Escalated,
            'sla_breached' => true,
        ]);

        $response = $this->actingAs($supervisorA)->get(route('escalations.index'));

        $response->assertOk();
        $response->assertSee('Eskalasi tim A');
        $response->assertDontSee('Eskalasi tim B');

        // Supervisor tidak boleh melihat rute admin.
        $this->actingAs($supervisorA)->get(route('admin.categories.index'))->assertForbidden();
    }
}
