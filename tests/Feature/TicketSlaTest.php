<?php

namespace Tests\Feature;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Category;
use App\Models\SlaRule;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class TicketSlaTest extends TestCase
{
    use RefreshDatabase;

    private Category $category;

    private User $requester;

    protected function setUp(): void
    {
        parent::setUp();

        SlaRule::create(['priority' => TicketPriority::Low, 'response_minutes' => 240, 'resolution_minutes' => 4320]);
        SlaRule::create(['priority' => TicketPriority::Medium, 'response_minutes' => 60, 'resolution_minutes' => 1440]);
        SlaRule::create(['priority' => TicketPriority::High, 'response_minutes' => 30, 'resolution_minutes' => 480]);
        SlaRule::create(['priority' => TicketPriority::Critical, 'response_minutes' => 15, 'resolution_minutes' => 240]);

        $this->category = Category::create(['name' => 'Jaringan']);
        $this->requester = User::factory()->pelapor()->create();
    }

    public function test_sla_due_at_is_computed_from_priority_on_creation(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-01-01 08:00:00'));

        $ticket = Ticket::create([
            'requester_id' => $this->requester->id,
            'category_id' => $this->category->id,
            'title' => 'Wifi mati',
            'description' => 'Tidak ada koneksi wifi di gedung A.',
            'priority' => TicketPriority::High,
            'status' => TicketStatus::Open,
        ]);

        // high: resolution_minutes = 480 (8 jam)
        $this->assertNotNull($ticket->sla_due_at);
        $this->assertTrue($ticket->created_at->copy()->addMinutes(480)->equalTo($ticket->sla_due_at));
        $this->assertSame('2026-01-01 16:00:00', $ticket->sla_due_at->format('Y-m-d H:i:s'));

        Carbon::setTestNow();
    }

    public function test_changing_priority_recomputes_sla_due_at_from_created_at(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-01-01 08:00:00'));

        $ticket = Ticket::create([
            'requester_id' => $this->requester->id,
            'category_id' => $this->category->id,
            'title' => 'Printer rusak',
            'description' => 'Printer tidak bisa mencetak.',
            'priority' => TicketPriority::Low,
            'status' => TicketStatus::Open,
        ]);

        // low: 4320 menit -> due 2026-01-04 08:00:00
        $this->assertSame('2026-01-04 08:00:00', $ticket->sla_due_at->format('Y-m-d H:i:s'));

        Carbon::setTestNow(Carbon::parse('2026-01-01 09:30:00'));

        $ticket->priority = TicketPriority::Critical;
        $ticket->save();
        $ticket->refresh();

        // critical: 240 menit dari created_at (bukan dari waktu update) -> 2026-01-01 12:00:00
        $this->assertSame('2026-01-01 12:00:00', $ticket->sla_due_at->format('Y-m-d H:i:s'));

        Carbon::setTestNow();
    }

    public function test_ticket_resolved_after_due_date_is_flagged_as_actually_breached(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-01-01 08:00:00'));

        $ticket = Ticket::create([
            'requester_id' => $this->requester->id,
            'category_id' => $this->category->id,
            'title' => 'Server lambat',
            'description' => 'Akses aplikasi akademik lambat sekali.',
            'priority' => TicketPriority::High,
            'status' => TicketStatus::Open,
        ]);

        // Resolve 9 jam kemudian, padahal target resolusi 8 jam (480 menit) -> breach,
        // walau sla_breached (flag dari job eskalasi) tidak pernah diset.
        Carbon::setTestNow(Carbon::parse('2026-01-01 17:00:00'));
        $ticket->status = TicketStatus::Resolved;
        $ticket->resolved_at = now();
        $ticket->save();
        $ticket->refresh();

        $this->assertFalse($ticket->sla_breached);
        $this->assertTrue($ticket->isActuallyBreached());

        Carbon::setTestNow();
    }
}
