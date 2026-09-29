<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\Team;
use App\Models\Ticket;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Menghitung laporan performa bulanan dari data tiket riil (tidak ada angka rekaan).
 *
 * Definisi "tiket ditangani" pada bulan terpilih: tiket yang assigned_to teknisi
 * tersebut DAN resolved_at berada di bulan itu. Artinya jumlah ini selalu sama
 * dengan jumlah tiket yang dipakai untuk menghitung rata-rata waktu resolusi dan
 * persentase tepat waktu (semuanya sudah pasti resolved). Teknisi tanpa tiket
 * resolved pada bulan tersebut tetap muncul dengan angka 0, bukan disembunyikan.
 */
class TeamPerformanceReport
{
    /**
     * @return Collection<int, array{
     *     team: Team,
     *     tickets_handled: int,
     *     avg_resolution_hours: float,
     *     sla_breaches: int,
     *     on_time_percentage: float,
     *     technicians: array<int, array{
     *         technician: \App\Models\User,
     *         tickets_handled: int,
     *         avg_resolution_hours: float,
     *         sla_breaches: int,
     *         on_time_percentage: float,
     *     }>,
     * }>
     */
    public function generate(int $year, int $month, ?int $teamId = null): Collection
    {
        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $teamsQuery = Team::query()
            ->with(['technicians' => function ($query) {
                $query->where('role', UserRole::Teknisi->value)->orderBy('name');
            }])
            ->orderBy('name');

        if ($teamId) {
            $teamsQuery->where('id', $teamId);
        }

        return $teamsQuery->get()->map(function (Team $team) use ($start, $end) {
            $technicianStats = $team->technicians->map(
                fn ($technician) => $this->statsFor($technician->id, $start, $end, $technician)
            )->values()->all();

            $handled = array_sum(array_column($technicianStats, 'tickets_handled'));
            $totalMinutes = array_sum(array_column($technicianStats, '_total_minutes'));
            $breaches = array_sum(array_column($technicianStats, 'sla_breaches'));

            return [
                'team' => $team,
                'tickets_handled' => $handled,
                'avg_resolution_hours' => $this->averageHours($totalMinutes, $handled),
                'sla_breaches' => $breaches,
                'on_time_percentage' => $this->onTimePercentage($handled, $breaches),
                'technicians' => array_map(function ($stat) {
                    unset($stat['_total_minutes']);

                    return $stat;
                }, $technicianStats),
            ];
        });
    }

    private function statsFor(int $technicianId, Carbon $start, Carbon $end, $technician): array
    {
        $resolvedTickets = Ticket::query()
            ->where('assigned_to', $technicianId)
            ->whereBetween('resolved_at', [$start, $end])
            ->get();

        $handled = $resolvedTickets->count();

        $totalMinutes = $resolvedTickets->sum(
            fn (Ticket $ticket) => $ticket->created_at->diffInMinutes($ticket->resolved_at)
        );

        $breaches = $resolvedTickets->filter(fn (Ticket $ticket) => $ticket->isActuallyBreached())->count();

        return [
            'technician' => $technician,
            'tickets_handled' => $handled,
            'avg_resolution_hours' => $this->averageHours($totalMinutes, $handled),
            'sla_breaches' => $breaches,
            'on_time_percentage' => $this->onTimePercentage($handled, $breaches),
            '_total_minutes' => $totalMinutes,
        ];
    }

    private function averageHours(int $totalMinutes, int $handled): float
    {
        if ($handled === 0) {
            return 0.0;
        }

        return round(($totalMinutes / 60) / $handled, 2);
    }

    private function onTimePercentage(int $handled, int $breaches): float
    {
        if ($handled === 0) {
            return 0.0;
        }

        return round((($handled - $breaches) / $handled) * 100, 1);
    }
}
