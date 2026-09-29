<?php

namespace Tests\Feature;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Category;
use App\Models\SlaRule;
use App\Models\Team;
use App\Models\Ticket;
use App\Models\User;
use App\Services\TeamPerformanceReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class TeamPerformanceReportTest extends TestCase
{
    use RefreshDatabase;

    private function makeTicket(User $requester, Category $category, Team $team, User $technician, TicketPriority $priority, string $createdAt, ?string $resolvedAt): Ticket
    {
        $ticket = new Ticket([
            'requester_id' => $requester->id,
            'category_id' => $category->id,
            'team_id' => $team->id,
            'assigned_to' => $technician->id,
            'title' => 'Tiket fixture',
            'description' => 'Data uji laporan performa.',
            'priority' => $priority,
            'status' => $resolvedAt ? TicketStatus::Resolved : TicketStatus::InProgress,
        ]);
        $ticket->created_at = Carbon::parse($createdAt);
        $ticket->save();

        if ($resolvedAt) {
            $ticket->resolved_at = Carbon::parse($resolvedAt);
            $ticket->save();
        }

        return $ticket->refresh();
    }

    public function test_it_computes_exact_average_resolution_time_and_on_time_percentage_from_fixture(): void
    {
        SlaRule::create(['priority' => TicketPriority::Medium, 'response_minutes' => 60, 'resolution_minutes' => 1440]); // 24 jam
        SlaRule::create(['priority' => TicketPriority::High, 'response_minutes' => 30, 'resolution_minutes' => 480]); // 8 jam
        SlaRule::create(['priority' => TicketPriority::Low, 'response_minutes' => 240, 'resolution_minutes' => 4320]); // 72 jam

        $supervisor = User::factory()->supervisor()->create();
        $team = Team::create(['name' => 'Tim A', 'supervisor_id' => $supervisor->id]);

        $t1 = User::factory()->teknisi()->create(['team_id' => $team->id, 'name' => 'Teknisi Satu']);
        $t2 = User::factory()->teknisi()->create(['team_id' => $team->id, 'name' => 'Teknisi Dua']);
        $t3 = User::factory()->teknisi()->create(['team_id' => $team->id, 'name' => 'Teknisi Tiga (nihil)']);

        $requester = User::factory()->pelapor()->create();
        $category = Category::create(['name' => 'Jaringan']);

        // Teknisi Satu, Februari 2026: satu on-time (12 jam dari target 24 jam), satu breach
        // (24 jam dari target 8 jam).
        $this->makeTicket($requester, $category, $team, $t1, TicketPriority::Medium, '2026-02-01 00:00:00', '2026-02-01 12:00:00');
        $this->makeTicket($requester, $category, $team, $t1, TicketPriority::High, '2026-02-05 00:00:00', '2026-02-06 00:00:00');

        // Teknisi Dua, Februari 2026: satu on-time (24 jam dari target 72 jam).
        $this->makeTicket($requester, $category, $team, $t2, TicketPriority::Low, '2026-02-10 00:00:00', '2026-02-11 00:00:00');

        // Teknisi Dua juga punya tiket yang resolve di BULAN LAIN (Maret) dan satu yang
        // belum resolve sama sekali: keduanya tidak boleh masuk hitungan Februari.
        $this->makeTicket($requester, $category, $team, $t2, TicketPriority::Medium, '2026-03-01 00:00:00', '2026-03-02 00:00:00');
        $this->makeTicket($requester, $category, $team, $t2, TicketPriority::Medium, '2026-02-20 00:00:00', null);

        // Teknisi Tiga sengaja tidak punya tiket resolved sama sekali bulan ini.

        $report = (new TeamPerformanceReport())->generate(2026, 2)->keyBy(fn ($row) => $row['team']->id);
        $teamRow = $report->get($team->id);

        $this->assertNotNull($teamRow);

        $technicianStats = collect($teamRow['technicians'])->keyBy(fn ($row) => $row['technician']->id);

        $satu = $technicianStats->get($t1->id);
        $this->assertSame(2, $satu['tickets_handled']);
        $this->assertSame(18.0, $satu['avg_resolution_hours']); // (12 + 24) / 2
        $this->assertSame(1, $satu['sla_breaches']);
        $this->assertSame(50.0, $satu['on_time_percentage']);

        $dua = $technicianStats->get($t2->id);
        $this->assertSame(1, $dua['tickets_handled']);
        $this->assertSame(24.0, $dua['avg_resolution_hours']);
        $this->assertSame(0, $dua['sla_breaches']);
        $this->assertSame(100.0, $dua['on_time_percentage']);

        $tiga = $technicianStats->get($t3->id);
        $this->assertSame(0, $tiga['tickets_handled']);
        $this->assertSame(0.0, $tiga['avg_resolution_hours']);
        $this->assertSame(0, $tiga['sla_breaches']);
        $this->assertSame(0.0, $tiga['on_time_percentage']);

        // Agregat tim: 3 tiket ditangani (2 + 1 + 0), total 60 jam (12+24+24), rata-rata 20 jam,
        // 1 breach dari 3 -> 66.7% tepat waktu.
        $this->assertSame(3, $teamRow['tickets_handled']);
        $this->assertSame(20.0, $teamRow['avg_resolution_hours']);
        $this->assertSame(1, $teamRow['sla_breaches']);
        $this->assertSame(66.7, $teamRow['on_time_percentage']);
    }

    public function test_a_ticket_resolved_late_counts_as_a_breach_even_when_the_flag_was_never_set(): void
    {
        SlaRule::create(['priority' => TicketPriority::High, 'response_minutes' => 30, 'resolution_minutes' => 480]);

        $supervisor = User::factory()->supervisor()->create();
        $team = Team::create(['name' => 'Tim B', 'supervisor_id' => $supervisor->id]);
        $technician = User::factory()->teknisi()->create(['team_id' => $team->id]);
        $requester = User::factory()->pelapor()->create();
        $category = Category::create(['name' => 'Software']);

        $ticket = $this->makeTicket($requester, $category, $team, $technician, TicketPriority::High, '2026-05-01 00:00:00', '2026-05-02 00:00:00');

        // Job eskalasi tidak pernah menjangkau tiket ini (langsung resolve), jadi flag tetap false.
        $this->assertFalse($ticket->sla_breached);

        $report = (new TeamPerformanceReport())->generate(2026, 5)->first();
        $stat = collect($report['technicians'])->first();

        $this->assertSame(1, $stat['tickets_handled']);
        $this->assertSame(1, $stat['sla_breaches']);
        $this->assertSame(0.0, $stat['on_time_percentage']);
    }
}
