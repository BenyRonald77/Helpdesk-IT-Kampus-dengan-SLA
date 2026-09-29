<?php

namespace App\Http\Controllers;

use App\Enums\TicketStatus;
use App\Models\Team;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EscalationController extends Controller
{
    /**
     * Dashboard eskalasi: daftar tiket berstatus escalated milik tim yang diawasi
     * (supervisor) atau seluruh tim (admin), lengkap dengan daftar teknisi tim
     * tersebut untuk aksi reassign.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        if ($user->isSupervisor()) {
            $team = Team::query()->where('supervisor_id', $user->id)->first();
            $teams = $team ? collect([$team]) : collect();
        } else {
            $teams = Team::query()->orderBy('name')->get();
        }

        $teamIds = $teams->pluck('id');

        $tickets = Ticket::query()
            ->where('status', TicketStatus::Escalated->value)
            ->whereIn('team_id', $teamIds)
            ->with(['requester', 'category', 'team', 'assignee', 'escalations'])
            ->latest('updated_at')
            ->get();

        $techniciansByTeam = User::query()
            ->whereIn('team_id', $teamIds)
            ->where('role', \App\Enums\UserRole::Teknisi->value)
            ->orderBy('name')
            ->get()
            ->groupBy('team_id');

        return view('escalations.index', [
            'tickets' => $tickets,
            'techniciansByTeam' => $techniciansByTeam,
            'hasAnyTeam' => $teams->isNotEmpty(),
        ]);
    }
}
