<?php

namespace Database\Seeders;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Category;
use App\Models\Escalation;
use App\Models\SlaRule;
use App\Models\Team;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Data tiket demo: campuran tiket selesai tepat waktu, selesai terlambat (breach),
 * masih terbuka dan sudah lewat SLA (untuk diverifikasi manual dengan perintah
 * eskalasi), dan satu yang sudah pernah dieskalasi. Semua timestamp diisi manual
 * (bypass hook otomatis) supaya data historis stabil dan bisa diverifikasi di
 * laporan bulanan.
 */
class TicketSeeder extends Seeder
{
    public function run(): void
    {
        $jaringan = Team::query()->where('name', 'Tim Jaringan & Infrastruktur')->first();
        $aplikasi = Team::query()->where('name', 'Tim Aplikasi & Software')->first();

        $andi = User::query()->where('email', 'teknisi.jaringan1@helpdesk.test')->first();
        $citra = User::query()->where('email', 'teknisi.jaringan2@helpdesk.test')->first();
        $dedi = User::query()->where('email', 'teknisi.aplikasi1@helpdesk.test')->first();

        $sari = User::query()->where('email', 'mahasiswa1@helpdesk.test')->first();
        $hendra = User::query()->where('email', 'staf1@helpdesk.test')->first();
        $wulan = User::query()->where('email', 'dosen1@helpdesk.test')->first();

        $jaringanCat = Category::query()->where('name', 'Jaringan')->first();
        $hardwareCat = Category::query()->where('name', 'Hardware')->first();
        $softwareCat = Category::query()->where('name', 'Software')->first();
        $akunCat = Category::query()->where('name', 'Akun/Akses')->first();

        $now = now();

        // 1. Selesai tepat waktu (medium, 24 jam) - Andi, bulan ini.
        $this->makeTicket(
            requester: $hendra,
            category: $jaringanCat,
            team: $jaringan,
            assignee: $andi,
            title: 'Wifi lab komputer 2 sering putus',
            description: 'Koneksi wifi di lab komputer 2 putus-nyambung sejak pagi, mengganggu praktikum.',
            priority: TicketPriority::Medium,
            createdAt: $now->copy()->subDays(10),
            firstRespondedAfterMinutes: 30,
            resolvedAfterMinutes: 20 * 60,
            status: TicketStatus::Resolved,
        );

        // 2. Selesai TERLAMBAT (high, target 8 jam, selesai di jam ke-30) - Andi, bulan ini.
        // Sengaja sla_breached TIDAK diset lewat job eskalasi (job belum pernah menjangkau
        // tiket ini karena sudah keburu resolve), untuk membuktikan laporan bulanan tetap
        // menghitungnya sebagai breach dari resolved_at vs sla_due_at.
        $this->makeTicket(
            requester: $sari,
            category: $hardwareCat,
            team: $jaringan,
            assignee: $andi,
            title: 'Proyektor ruang B203 tidak menyala',
            description: 'Proyektor tidak merespons remote maupun tombol power di panel dinding.',
            priority: TicketPriority::High,
            createdAt: $now->copy()->subDays(8),
            firstRespondedAfterMinutes: 45,
            resolvedAfterMinutes: 30 * 60,
            status: TicketStatus::Resolved,
        );

        // 3. Masih TERBUKA dan sudah lewat SLA, BELUM dieskalasi (critical, target 4 jam,
        // dibuat 5 hari lalu). Ini target verifikasi manual: jalankan
        // `php artisan escalate:overdue-tickets` dan pastikan tiket ini berpindah ke
        // status escalated serta muncul di dashboard supervisor.
        $this->makeTicket(
            requester: $wulan,
            category: $jaringanCat,
            team: $jaringan,
            assignee: $citra,
            title: 'Server presensi tidak bisa diakses seluruh gedung',
            description: 'Sejak pukul 07:00 seluruh gedung tidak bisa mengakses server presensi, kemungkinan jaringan inti bermasalah.',
            priority: TicketPriority::Critical,
            createdAt: $now->copy()->subDays(5),
            firstRespondedAfterMinutes: 20,
            resolvedAfterMinutes: null,
            status: TicketStatus::InProgress,
        );

        // 4. SUDAH dieskalasi (high, target 8 jam, dibuat 6 hari lalu, belum selesai).
        $escalatedTicket = $this->makeTicket(
            requester: $hendra,
            category: $softwareCat,
            team: $jaringan,
            assignee: $citra,
            title: 'Aplikasi akademik error 500 saat login',
            description: 'Mahasiswa tidak bisa login ke portal akademik, muncul error 500 sejak kemarin sore.',
            priority: TicketPriority::High,
            createdAt: $now->copy()->subDays(6),
            firstRespondedAfterMinutes: 60,
            resolvedAfterMinutes: null,
            status: TicketStatus::Escalated,
            slaBreached: true,
        );

        $supervisorJaringan = User::query()->where('email', 'supervisor.jaringan@helpdesk.test')->first();

        Escalation::query()->create([
            'ticket_id' => $escalatedTicket->id,
            'escalated_at' => $escalatedTicket->sla_due_at->copy()->addMinutes(5),
            'escalated_to' => $supervisorJaringan->id,
            'reason' => 'Lewat target resolusi',
        ]);

        // 5. Selesai tepat waktu - Citra, bulan ini.
        $this->makeTicket(
            requester: $wulan,
            category: $akunCat,
            team: $jaringan,
            assignee: $citra,
            title: 'Lupa password akun email kampus',
            description: 'Dosen tidak bisa login ke email kampus setelah lupa password, butuh reset.',
            priority: TicketPriority::Low,
            createdAt: $now->copy()->subDays(4),
            firstRespondedAfterMinutes: 90,
            resolvedAfterMinutes: 2 * 24 * 60,
            status: TicketStatus::Resolved,
        );

        // 6. Tiket belum ditugaskan ke teknisi manapun, hanya ke tim (untuk uji alur self-assign).
        $this->makeTicket(
            requester: $sari,
            category: $hardwareCat,
            team: $jaringan,
            assignee: null,
            title: 'Printer perpustakaan lantai 1 macet',
            description: 'Printer sering macet saat mencetak lebih dari 5 halaman.',
            priority: TicketPriority::Low,
            createdAt: $now->copy()->subHours(6),
            firstRespondedAfterMinutes: null,
            resolvedAfterMinutes: null,
            status: TicketStatus::Open,
        );

        // 7. Tim Aplikasi: Dedi selesai tepat waktu bulan ini. Fajar Ramadhan sengaja
        // tidak diberi tiket sama sekali bulan ini, untuk menguji laporan menampilkan 0
        // secara jujur, bukan angka rekaan atau baris yang disembunyikan.
        $this->makeTicket(
            requester: $hendra,
            category: $softwareCat,
            team: $aplikasi,
            assignee: $dedi,
            title: 'Aplikasi keuangan tidak bisa export laporan',
            description: 'Tombol export ke Excel di aplikasi keuangan tidak merespons.',
            priority: TicketPriority::Medium,
            createdAt: $now->copy()->subDays(3),
            firstRespondedAfterMinutes: 40,
            resolvedAfterMinutes: 10 * 60,
            status: TicketStatus::Resolved,
        );

        // 8. Tim Aplikasi: satu tiket breach bulan lalu, untuk memastikan filter bulan pada
        // laporan benar-benar memisahkan data antar bulan (tidak tercampur).
        $this->makeTicket(
            requester: $wulan,
            category: $akunCat,
            team: $aplikasi,
            assignee: $dedi,
            title: 'Akun SSO tidak bisa dipakai login SIAKAD',
            description: 'Akun SSO staf terkunci otomatis dan tidak bisa dipakai login ke SIAKAD.',
            priority: TicketPriority::High,
            createdAt: $now->copy()->subMonthsNoOverflow(1)->startOfMonth()->addDays(5),
            firstRespondedAfterMinutes: 50,
            resolvedAfterMinutes: 20 * 60,
            status: TicketStatus::Resolved,
        );
    }

    private function makeTicket(
        User $requester,
        Category $category,
        Team $team,
        ?User $assignee,
        string $title,
        string $description,
        TicketPriority $priority,
        Carbon $createdAt,
        ?int $firstRespondedAfterMinutes,
        ?int $resolvedAfterMinutes,
        TicketStatus $status,
        bool $slaBreached = false,
    ): Ticket {
        $rule = SlaRule::forPriority($priority);
        $slaDueAt = $createdAt->copy()->addMinutes($rule->resolution_minutes);

        $firstRespondedAt = $firstRespondedAfterMinutes !== null
            ? $createdAt->copy()->addMinutes($firstRespondedAfterMinutes)
            : null;

        $resolvedAt = $resolvedAfterMinutes !== null
            ? $createdAt->copy()->addMinutes($resolvedAfterMinutes)
            : null;

        // $slaBreached di sini murni parameter eksplisit (default false): tiket #2 sengaja
        // resolve terlambat tanpa flag ini diset, supaya laporan bulanan dibuktikan menghitung
        // breach dari resolved_at vs sla_due_at, bukan dari flag semata. Lihat komentar di run().
        $ticket = new Ticket([
            'requester_id' => $requester->id,
            'category_id' => $category->id,
            'team_id' => $team->id,
            'assigned_to' => $assignee?->id,
            'title' => $title,
            'description' => $description,
            'priority' => $priority,
            'status' => $status,
            'first_responded_at' => $firstRespondedAt,
            'resolved_at' => $resolvedAt,
            'sla_due_at' => $slaDueAt,
            'sla_breached' => $slaBreached,
        ]);
        $ticket->timestamps = false;
        $ticket->created_at = $createdAt;
        $ticket->updated_at = $resolvedAt ?? $createdAt;
        $ticket->save();

        if ($assignee) {
            TicketComment::query()->create([
                'ticket_id' => $ticket->id,
                'user_id' => $assignee->id,
                'body' => 'Tiket saya ambil, sedang saya cek ke lokasi.',
                'created_at' => $firstRespondedAt ?? $createdAt,
                'updated_at' => $firstRespondedAt ?? $createdAt,
            ]);
        }

        if ($resolvedAt) {
            TicketComment::query()->create([
                'ticket_id' => $ticket->id,
                'user_id' => $assignee->id,
                'body' => 'Sudah ditangani dan dikonfirmasi selesai.',
                'created_at' => $resolvedAt,
                'updated_at' => $resolvedAt,
            ]);
        }

        return $ticket;
    }
}
