<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    /**
     * Siapa saja yang bisa membuat tiket baru: hanya pelapor. Staf (teknisi, supervisor,
     * admin) menangani tiket, bukan membuatnya sebagai pelapor.
     */
    public function create(User $user): bool
    {
        return $user->isPelapor();
    }

    /**
     * Melihat detail satu tiket:
     * - pelapor hanya tiketnya sendiri
     * - teknisi: tiket yang ditugaskan padanya, atau tiket tim yang belum ditugaskan (untuk self-assign)
     * - supervisor: tiket milik tim yang ia awasi
     * - admin: semua tiket
     */
    public function view(User $user, Ticket $ticket): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isPelapor()) {
            return $ticket->requester_id === $user->id;
        }

        if ($user->isTeknisi()) {
            return $ticket->assigned_to === $user->id
                || ($ticket->assigned_to === null && $ticket->team_id === $user->team_id);
        }

        if ($user->isSupervisor()) {
            return $ticket->team && $ticket->team->supervisor_id === $user->id;
        }

        return false;
    }

    /**
     * Menambah komentar: siapa pun yang boleh melihat tiket tersebut boleh berkomentar,
     * kecuali teknisi yang belum mengambil tiket unassigned (harus self-assign dulu).
     */
    public function comment(User $user, Ticket $ticket): bool
    {
        if ($user->isTeknisi() && $ticket->assigned_to === null) {
            return false;
        }

        return $this->view($user, $ticket);
    }

    /**
     * Mengambil tiket tim yang belum ditugaskan (self-assign).
     */
    public function selfAssign(User $user, Ticket $ticket): bool
    {
        return $user->isTeknisi() && $ticket->assigned_to === null;
    }

    /**
     * Mengubah status, prioritas, atau menandai selesai: teknisi yang menangani, atau admin.
     */
    public function manage(User $user, Ticket $ticket): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isTeknisi() && $ticket->assigned_to === $user->id;
    }

    /**
     * Menugaskan ulang (reassign) ke teknisi lain: supervisor tim tiket tersebut, atau admin.
     */
    public function reassign(User $user, Ticket $ticket): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isSupervisor() && $ticket->team && $ticket->team->supervisor_id === $user->id;
    }
}
