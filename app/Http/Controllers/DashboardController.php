<?php

namespace App\Http\Controllers;

use App\Enums\TicketStatus;
use App\Models\Team;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        if ($user->isPelapor()) {
            $openCount = Ticket::query()
                ->where('requester_id', $user->id)
                ->whereNotIn('status', [TicketStatus::Resolved->value, TicketStatus::Closed->value])
                ->count();
            $totalCount = Ticket::query()->where('requester_id', $user->id)->count();

            $stats = [
                ['label' => 'Tiket masih berjalan', 'value' => $openCount],
                ['label' => 'Total tiket dibuat', 'value' => $totalCount],
            ];
        } elseif ($user->isTeknisi()) {
            $mineCount = Ticket::query()
                ->where('assigned_to', $user->id)
                ->whereNotIn('status', [TicketStatus::Resolved->value, TicketStatus::Closed->value])
                ->count();
            $queueCount = Ticket::query()
                ->whereNull('assigned_to')
                ->whereNotIn('status', [TicketStatus::Resolved->value, TicketStatus::Closed->value])
                ->count();
            $breachedMine = Ticket::query()
                ->where('assigned_to', $user->id)
                ->whereNotIn('status', [TicketStatus::Resolved->value, TicketStatus::Closed->value])
                ->where('sla_due_at', '<', now())
                ->count();

            $stats = [
                ['label' => 'Tiket saya yang berjalan', 'value' => $mineCount],
                ['label' => 'Tiket saya lewat SLA', 'value' => $breachedMine],
                ['label' => 'Antrian tim belum diambil', 'value' => $queueCount],
            ];
        } elseif ($user->isSupervisor()) {
            $team = Team::query()->where('supervisor_id', $user->id)->first();
            $teamId = $team?->id;

            $escalatedCount = Ticket::query()
                ->where('team_id', $teamId)
                ->where('status', TicketStatus::Escalated->value)
                ->count();
            $activeCount = Ticket::query()
                ->where('team_id', $teamId)
                ->whereNotIn('status', [TicketStatus::Resolved->value, TicketStatus::Closed->value])
                ->count();

            $stats = [
                ['label' => 'Tiket dieskalasi ke saya', 'value' => $escalatedCount],
                ['label' => 'Tiket tim yang masih berjalan', 'value' => $activeCount],
            ];
        } else {
            $totalOpen = Ticket::query()
                ->whereNotIn('status', [TicketStatus::Resolved->value, TicketStatus::Closed->value])
                ->count();
            $totalEscalated = Ticket::query()->where('status', TicketStatus::Escalated->value)->count();
            $totalBreached = Ticket::query()
                ->whereNotIn('status', [TicketStatus::Resolved->value, TicketStatus::Closed->value])
                ->where('sla_due_at', '<', now())
                ->count();

            $stats = [
                ['label' => 'Tiket berjalan (semua tim)', 'value' => $totalOpen],
                ['label' => 'Tiket dieskalasi', 'value' => $totalEscalated],
                ['label' => 'Lewat SLA & belum ditangani', 'value' => $totalBreached],
            ];
        }

        return view('dashboard', ['stats' => $stats]);
    }
}
