<?php

namespace App\Http\Controllers;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Category;
use App\Models\Team;
use App\Models\Ticket;
use App\Models\TicketComment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        if ($user->isPelapor()) {
            $groups = [[
                'title' => 'Tiket saya',
                'empty_hint' => 'Belum ada tiket. Buat tiket baru kalau ada gangguan yang perlu ditangani IT.',
                'tickets' => Ticket::query()->where('requester_id', $user->id)->latest()->get(),
            ]];
        } elseif ($user->isTeknisi()) {
            $groups = [
                [
                    'title' => 'Tiket saya',
                    'empty_hint' => 'Belum ada tiket yang ditugaskan ke Anda.',
                    'tickets' => Ticket::query()->where('assigned_to', $user->id)->latest()->get(),
                ],
                [
                    'title' => 'Antrian tim (belum ditugaskan)',
                    'empty_hint' => 'Tidak ada tiket yang menunggu diambil saat ini.',
                    'tickets' => Ticket::query()
                        ->whereNull('assigned_to')
                        ->whereNotIn('status', [TicketStatus::Resolved->value, TicketStatus::Closed->value])
                        ->latest()
                        ->get(),
                    'show_self_assign' => true,
                ],
            ];
        } elseif ($user->isSupervisor()) {
            $team = Team::query()->where('supervisor_id', $user->id)->first();
            $groups = [[
                'title' => $team ? "Tiket tim: {$team->name}" : 'Tiket tim',
                'empty_hint' => 'Belum ada tiket yang ditangani tim Anda.',
                'tickets' => $team
                    ? Ticket::query()->where('team_id', $team->id)->latest()->get()
                    : collect(),
            ]];
        } else {
            $groups = [[
                'title' => 'Semua tiket',
                'empty_hint' => 'Belum ada tiket sama sekali di sistem.',
                'tickets' => Ticket::query()->latest()->get(),
            ]];
        }

        return view('tickets.index', ['groups' => $groups]);
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Ticket::class);

        $categories = Category::query()->orderBy('name')->get();
        $priorities = TicketPriority::options();

        return view('tickets.create', compact('categories', 'priorities'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Ticket::class);

        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'priority' => ['required', 'in:'.implode(',', array_map(fn ($p) => $p->value, TicketPriority::options()))],
            'title' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string', 'max:5000'],
        ]);

        $ticket = Ticket::create([
            'requester_id' => $request->user()->id,
            'category_id' => $data['category_id'],
            'title' => $data['title'],
            'description' => $data['description'],
            'priority' => TicketPriority::from($data['priority']),
            'status' => TicketStatus::Open,
        ]);

        return redirect()->route('tickets.show', $ticket)
            ->with('status', 'Tiket berhasil dibuat. Target penyelesaian sudah ditetapkan otomatis sesuai prioritas.');
    }

    public function show(Request $request, Ticket $ticket): View
    {
        $this->authorize('view', $ticket);

        $ticket->load(['requester', 'category', 'team', 'assignee', 'comments.user']);

        $canReassign = $request->user()->can('reassign', $ticket);

        $reassignTechnicians = $canReassign && $ticket->team_id
            ? \App\Models\User::query()
                ->where('team_id', $ticket->team_id)
                ->where('role', \App\Enums\UserRole::Teknisi->value)
                ->orderBy('name')
                ->get()
            : collect();

        return view('tickets.show', [
            'ticket' => $ticket,
            'canComment' => $request->user()->can('comment', $ticket),
            'canSelfAssign' => $request->user()->can('selfAssign', $ticket),
            'canManage' => $request->user()->can('manage', $ticket),
            'canReassign' => $canReassign,
            'reassignTechnicians' => $reassignTechnicians,
            'statuses' => TicketStatus::options(),
            'priorities' => TicketPriority::options(),
        ]);
    }

    public function selfAssign(Request $request, Ticket $ticket): RedirectResponse
    {
        $this->authorize('selfAssign', $ticket);

        $user = $request->user();

        $ticket->assigned_to = $user->id;
        $ticket->team_id = $user->team_id;

        if (! $ticket->first_responded_at) {
            $ticket->first_responded_at = now();
        }

        if ($ticket->status === TicketStatus::Open) {
            $ticket->status = TicketStatus::InProgress;
        }

        $ticket->save();

        TicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'body' => 'Tiket diambil dan mulai ditangani.',
        ]);

        return redirect()->route('tickets.show', $ticket)->with('status', 'Tiket berhasil diambil.');
    }

    public function updateStatus(Request $request, Ticket $ticket): RedirectResponse
    {
        $this->authorize('manage', $ticket);

        $data = $request->validate([
            'status' => ['required', 'in:'.implode(',', array_map(fn ($s) => $s->value, TicketStatus::options()))],
        ]);

        $newStatus = TicketStatus::from($data['status']);
        $ticket->status = $newStatus;

        if ($newStatus === TicketStatus::Resolved && ! $ticket->resolved_at) {
            $ticket->resolved_at = now();
        }

        if ($newStatus !== TicketStatus::Resolved) {
            $ticket->resolved_at = null;
        }

        $ticket->save();

        return redirect()->route('tickets.show', $ticket)->with('status', 'Status tiket diperbarui.');
    }

    public function updatePriority(Request $request, Ticket $ticket): RedirectResponse
    {
        $this->authorize('manage', $ticket);

        $data = $request->validate([
            'priority' => ['required', 'in:'.implode(',', array_map(fn ($p) => $p->value, TicketPriority::options()))],
        ]);

        // Mengubah prioritas menghitung ulang sla_due_at dari created_at + resolution_minutes
        // aturan prioritas baru (lihat hook di App\Models\Ticket::booted()).
        $ticket->priority = TicketPriority::from($data['priority']);
        $ticket->save();

        return redirect()->route('tickets.show', $ticket)
            ->with('status', 'Prioritas diperbarui. Target SLA dihitung ulang sesuai prioritas baru.');
    }

    public function reassign(Request $request, Ticket $ticket): RedirectResponse
    {
        $this->authorize('reassign', $ticket);

        $data = $request->validate([
            'assigned_to' => ['required', 'exists:users,id'],
        ]);

        $ticket->assigned_to = $data['assigned_to'];

        if (! $ticket->first_responded_at) {
            $ticket->first_responded_at = now();
        }

        if ($ticket->status === TicketStatus::Escalated) {
            $ticket->status = TicketStatus::InProgress;
        }

        $ticket->save();

        TicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id' => $request->user()->id,
            'body' => 'Tiket ditugaskan ulang oleh supervisor.',
        ]);

        return redirect()->route('tickets.show', $ticket)->with('status', 'Tiket berhasil ditugaskan ulang.');
    }
}
