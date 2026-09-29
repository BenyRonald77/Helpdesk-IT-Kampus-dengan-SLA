<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Database\Seeders\SlaRuleSeeder;
use Database\Seeders\TeamSeeder;
use Database\Seeders\TicketSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Menjalankan seeder demo yang sama seperti yang dipakai untuk data contoh, lalu
 * membuka setiap halaman utama sebagai setiap peran, memastikan semua benar-benar
 * merender tanpa error server (antislop R-35: aplikasi harus benar-benar dijalankan,
 * bukan hanya dibaca kodenya).
 */
class SmokeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(CategorySeeder::class);
        $this->seed(SlaRuleSeeder::class);
        $this->seed(TeamSeeder::class);
        $this->seed(TicketSeeder::class);
    }

    public function test_pelapor_pages_render(): void
    {
        $pelapor = User::query()->where('email', 'mahasiswa1@helpdesk.test')->first();

        $this->actingAs($pelapor)->get(route('dashboard'))->assertOk();
        $this->actingAs($pelapor)->get(route('tickets.index'))->assertOk();
        $this->actingAs($pelapor)->get(route('tickets.create'))->assertOk();

        $ownTicket = Ticket::query()->where('requester_id', $pelapor->id)->first();
        $this->actingAs($pelapor)->get(route('tickets.show', $ownTicket))->assertOk();
    }

    public function test_teknisi_pages_render(): void
    {
        $teknisi = User::query()->where('email', 'teknisi.jaringan1@helpdesk.test')->first();

        $this->actingAs($teknisi)->get(route('dashboard'))->assertOk();
        $this->actingAs($teknisi)->get(route('tickets.index'))->assertOk();

        $assigned = Ticket::query()->where('assigned_to', $teknisi->id)->first();
        $this->actingAs($teknisi)->get(route('tickets.show', $assigned))->assertOk();
    }

    public function test_supervisor_pages_render(): void
    {
        $supervisor = User::query()->where('email', 'supervisor.jaringan@helpdesk.test')->first();

        $this->actingAs($supervisor)->get(route('dashboard'))->assertOk();
        $this->actingAs($supervisor)->get(route('tickets.index'))->assertOk();
        $this->actingAs($supervisor)->get(route('escalations.index'))->assertOk();
        $this->actingAs($supervisor)->get(route('report.index'))->assertOk();
    }

    public function test_admin_pages_render(): void
    {
        $admin = User::query()->where('email', 'admin@helpdesk.test')->first();

        $this->actingAs($admin)->get(route('dashboard'))->assertOk();
        $this->actingAs($admin)->get(route('tickets.index'))->assertOk();
        $this->actingAs($admin)->get(route('escalations.index'))->assertOk();
        $this->actingAs($admin)->get(route('report.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.categories.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.sla-rules.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.teams.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.users.index'))->assertOk();
    }

    public function test_escalation_command_against_seeded_overdue_ticket_shows_up_for_supervisor(): void
    {
        $overdue = Ticket::query()->where('title', 'Server presensi tidak bisa diakses seluruh gedung')->first();
        $this->assertSame(\App\Enums\TicketStatus::InProgress, $overdue->status);

        $this->artisan('escalate:overdue-tickets')->assertSuccessful();

        $overdue->refresh();
        $this->assertSame(\App\Enums\TicketStatus::Escalated, $overdue->status);
        $this->assertTrue($overdue->sla_breached);

        $supervisor = User::query()->where('email', 'supervisor.jaringan@helpdesk.test')->first();
        $this->actingAs($supervisor)
            ->get(route('escalations.index'))
            ->assertOk()
            ->assertSee('Server presensi tidak bisa diakses seluruh gedung');
    }
}
