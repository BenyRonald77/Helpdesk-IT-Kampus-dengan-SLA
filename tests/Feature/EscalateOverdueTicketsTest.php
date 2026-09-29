<?php

namespace Tests\Feature;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Category;
use App\Models\Escalation;
use App\Models\SlaRule;
use App\Models\Team;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class EscalateOverdueTicketsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        SlaRule::create(['priority' => TicketPriority::High, 'response_minutes' => 30, 'resolution_minutes' => 480]);
        SlaRule::create(['priority' => TicketPriority::Low, 'response_minutes' => 240, 'resolution_minutes' => 4320]);
    }

    public function test_it_escalates_an_overdue_ticket_to_the_team_supervisor(): void
    {
        $category = Category::create(['name' => 'Jaringan']);
        $requester = User::factory()->pelapor()->create();
        $supervisor = User::factory()->supervisor()->create();
        $team = Team::create(['name' => 'Tim Jaringan', 'supervisor_id' => $supervisor->id]);
        $technician = User::factory()->teknisi()->create(['team_id' => $team->id]);

        Carbon::setTestNow(Carbon::parse('2026-01-01 08:00:00'));

        $overdueTicket = Ticket::create([
            'requester_id' => $requester->id,
            'category_id' => $category->id,
            'team_id' => $team->id,
            'assigned_to' => $technician->id,
            'title' => 'Overdue',
            'description' => 'Sudah lewat target.',
            'priority' => TicketPriority::High,
            'status' => TicketStatus::InProgress,
        ]);

        $notYetDueTicket = Ticket::create([
            'requester_id' => $requester->id,
            'category_id' => $category->id,
            'team_id' => $team->id,
            'assigned_to' => $technician->id,
            'title' => 'Belum lewat target',
            'description' => 'Masih dalam jendela SLA.',
            'priority' => TicketPriority::Low,
            'status' => TicketStatus::InProgress,
        ]);

        // Lompat ke 9 jam kemudian: tiket high (target 8 jam) lewat, tiket low (target 72 jam) belum.
        Carbon::setTestNow(Carbon::parse('2026-01-01 17:00:00'));

        $this->artisan('escalate:overdue-tickets')->assertSuccessful();

        $overdueTicket->refresh();
        $notYetDueTicket->refresh();

        $this->assertSame(TicketStatus::Escalated, $overdueTicket->status);
        $this->assertTrue($overdueTicket->sla_breached);
        $this->assertDatabaseHas('escalations', [
            'ticket_id' => $overdueTicket->id,
            'escalated_to' => $supervisor->id,
            'reason' => 'Lewat target resolusi',
        ]);

        // Hanya tiket yang benar-benar overdue yang dieskalasi.
        $this->assertSame(TicketStatus::InProgress, $notYetDueTicket->status);
        $this->assertFalse($notYetDueTicket->sla_breached);
        $this->assertSame(0, Escalation::query()->where('ticket_id', $notYetDueTicket->id)->count());

        Carbon::setTestNow();
    }

    public function test_it_falls_back_to_an_admin_when_the_team_has_no_supervisor(): void
    {
        $category = Category::create(['name' => 'Hardware']);
        $requester = User::factory()->pelapor()->create();
        $admin = User::factory()->admin()->create();
        $team = Team::create(['name' => 'Tim Tanpa Atasan', 'supervisor_id' => null]);
        $technician = User::factory()->teknisi()->create(['team_id' => $team->id]);

        Carbon::setTestNow(Carbon::parse('2026-01-01 08:00:00'));

        $ticket = Ticket::create([
            'requester_id' => $requester->id,
            'category_id' => $category->id,
            'team_id' => $team->id,
            'assigned_to' => $technician->id,
            'title' => 'Tim belum punya atasan',
            'description' => 'Perlu fallback ke admin.',
            'priority' => TicketPriority::High,
            'status' => TicketStatus::Open,
        ]);

        Carbon::setTestNow(Carbon::parse('2026-01-01 17:00:00'));

        $this->artisan('escalate:overdue-tickets')->assertSuccessful();

        $this->assertDatabaseHas('escalations', [
            'ticket_id' => $ticket->id,
            'escalated_to' => $admin->id,
        ]);

        Carbon::setTestNow();
    }

    public function test_it_does_not_re_escalate_a_ticket_that_is_already_escalated(): void
    {
        $category = Category::create(['name' => 'Software']);
        $requester = User::factory()->pelapor()->create();
        $supervisor = User::factory()->supervisor()->create();
        $team = Team::create(['name' => 'Tim Aplikasi', 'supervisor_id' => $supervisor->id]);

        Carbon::setTestNow(Carbon::parse('2026-01-01 08:00:00'));

        $ticket = Ticket::create([
            'requester_id' => $requester->id,
            'category_id' => $category->id,
            'team_id' => $team->id,
            'title' => 'Sudah dieskalasi',
            'description' => 'Sudah pernah dieskalasi sebelumnya.',
            'priority' => TicketPriority::High,
            'status' => TicketStatus::Escalated,
            'sla_breached' => true,
        ]);

        Carbon::setTestNow(Carbon::parse('2026-01-01 17:00:00'));

        $this->artisan('escalate:overdue-tickets')->assertSuccessful();

        $this->assertSame(0, Escalation::query()->where('ticket_id', $ticket->id)->count());

        Carbon::setTestNow();
    }
}
