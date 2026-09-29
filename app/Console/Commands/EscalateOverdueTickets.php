<?php

namespace App\Console\Commands;

use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Models\Escalation;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Menemukan tiket yang belum resolved/closed/escalated dan sudah lewat sla_due_at,
 * lalu mengeskalasinya ke supervisor tim tiket tersebut. Jalankan berkala lewat
 * scheduler (`php artisan schedule:work` atau cron) supaya eskalasi terjadi otomatis
 * tanpa perlu ada yang memantau manual.
 */
class EscalateOverdueTickets extends Command
{
    protected $signature = 'escalate:overdue-tickets';

    protected $description = 'Eskalasi tiket yang sudah lewat target SLA dan belum ditangani ke supervisor tim terkait';

    public function handle(): int
    {
        $overdueTickets = Ticket::query()
            ->whereNotIn('status', [
                TicketStatus::Resolved->value,
                TicketStatus::Closed->value,
                TicketStatus::Escalated->value,
            ])
            ->whereNotNull('sla_due_at')
            ->where('sla_due_at', '<', now())
            ->with('team')
            ->get();

        if ($overdueTickets->isEmpty()) {
            $this->info('Tidak ada tiket yang lewat SLA saat ini.');

            return self::SUCCESS;
        }

        $escalatedCount = 0;

        foreach ($overdueTickets as $ticket) {
            DB::transaction(function () use ($ticket, &$escalatedCount) {
                $escalatedTo = $ticket->team?->supervisor_id;

                // Fallback: jika tim tiket tidak ada atau tim belum punya supervisor,
                // eskalasi dialihkan ke admin pertama yang ditemukan, supaya tiket yang
                // lewat SLA tidak hilang begitu saja tanpa penerima.
                if (! $escalatedTo) {
                    $escalatedTo = User::query()->where('role', UserRole::Admin->value)->value('id');
                }

                if (! $escalatedTo) {
                    // Tidak ada supervisor maupun admin sama sekali di sistem: lewati,
                    // daripada membuat baris escalations dengan escalated_to kosong.
                    return;
                }

                Escalation::query()->create([
                    'ticket_id' => $ticket->id,
                    'escalated_at' => now(),
                    'escalated_to' => $escalatedTo,
                    'reason' => 'Lewat target resolusi',
                ]);

                $ticket->status = TicketStatus::Escalated;
                $ticket->sla_breached = true;
                $ticket->save();

                $escalatedCount++;
            });
        }

        $this->info("{$escalatedCount} tiket dieskalasi ke supervisor/admin terkait.");

        return self::SUCCESS;
    }
}
