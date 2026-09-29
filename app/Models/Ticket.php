<?php

namespace App\Models;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Services\SlaCalculator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'requester_id',
        'category_id',
        'team_id',
        'assigned_to',
        'title',
        'description',
        'priority',
        'status',
        'first_responded_at',
        'resolved_at',
        'sla_due_at',
        'sla_breached',
    ];

    protected function casts(): array
    {
        return [
            'priority' => TicketPriority::class,
            'status' => TicketStatus::class,
            'first_responded_at' => 'datetime',
            'resolved_at' => 'datetime',
            'sla_due_at' => 'datetime',
            'sla_breached' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Ticket $ticket) {
            if (! $ticket->created_at) {
                $ticket->created_at = now();
            }

            if (! $ticket->updated_at) {
                $ticket->updated_at = $ticket->created_at;
            }

            if ($ticket->priority instanceof TicketPriority && ! $ticket->getAttribute('sla_due_at')) {
                $ticket->sla_due_at = SlaCalculator::dueAt($ticket->priority, $ticket->created_at);
            }
        });

        static::updating(function (Ticket $ticket) {
            // Aturan: mengubah prioritas menghitung ulang sla_due_at dari created_at (waktu tiket
            // dibuat, tidak berubah) + resolution_minutes aturan prioritas yang baru. Riwayat lama
            // tidak disentuh, hanya target ke depan yang bergeser.
            if ($ticket->isDirty('priority') && $ticket->priority instanceof TicketPriority) {
                $ticket->sla_due_at = SlaCalculator::dueAt($ticket->priority, $ticket->created_at);
            }
        });
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TicketComment::class)->orderBy('created_at');
    }

    public function escalations(): HasMany
    {
        return $this->hasMany(Escalation::class);
    }

    /**
     * Sisa waktu (menit) sampai sla_due_at, dihitung dari waktu server saat ini.
     * Negatif berarti sudah terlambat sejumlah menit tersebut.
     * Ini dipanggil setiap halaman detail tiket di-render, langsung dari DB, bukan
     * countdown sisi klien, supaya selalu akurat.
     */
    public function minutesUntilDue(?Carbon $now = null): ?int
    {
        if (! $this->sla_due_at) {
            return null;
        }

        $now ??= now();

        return (int) round($now->diffInSeconds($this->sla_due_at, false) / 60);
    }

    public function isOverdue(?Carbon $now = null): bool
    {
        $minutes = $this->minutesUntilDue($now);

        return $minutes !== null && $minutes < 0;
    }

    /**
     * Sisa fraksi jendela resolusi (0..1+) yang masih tersisa, dihitung dari
     * created_at ke sla_due_at. Dipakai untuk kode warna urgensi SLA.
     */
    public function remainingWindowFraction(?Carbon $now = null): ?float
    {
        if (! $this->sla_due_at || ! $this->created_at) {
            return null;
        }

        $now ??= now();

        $totalWindow = $this->created_at->diffInSeconds($this->sla_due_at, false);

        if ($totalWindow <= 0) {
            return null;
        }

        $remaining = $now->diffInSeconds($this->sla_due_at, false);

        return $remaining / $totalWindow;
    }

    /**
     * Status warna urgensi SLA: on_time (hijau, sisa >50% atau selesai sebelum jatuh tempo),
     * approaching (kuning, sisa <=50% dan masih berjalan), breached (merah, sudah lewat
     * sla_due_at, baik masih terbuka maupun baru selesai setelah lewat tempo). Warna di sini
     * menandai urgensi SLA yang nyata (ambang waktu yang jelas), bukan dekorasi
     * (antislop R-01 / R-31).
     */
    public function slaColorStatus(?Carbon $now = null): ?string
    {
        if (! $this->sla_due_at) {
            return null;
        }

        // Tiket yang sudah selesai dinilai dari waktu penyelesaiannya, bukan dari waktu
        // server sekarang (yang bisa jauh setelah tiket lama diselesaikan).
        if ($this->resolved_at) {
            return $this->resolved_at->greaterThan($this->sla_due_at) ? 'breached' : 'on_time';
        }

        if ($this->isOverdue($now)) {
            return 'breached';
        }

        $fraction = $this->remainingWindowFraction($now);

        if ($fraction === null) {
            return null;
        }

        return $fraction > 0.5 ? 'on_time' : 'approaching';
    }

    /**
     * Apakah tiket ini benar-benar melewati SLA, dihitung dari data riil (bukan hanya
     * mengandalkan flag sla_breached yang diisi oleh job eskalasi). Tiket yang resolve
     * setelah sla_due_at tetap terhitung breach walau job eskalasi tidak pernah menjangkaunya.
     */
    public function isActuallyBreached(): bool
    {
        if ($this->sla_breached) {
            return true;
        }

        if (! $this->sla_due_at) {
            return false;
        }

        if ($this->resolved_at) {
            return $this->resolved_at->greaterThan($this->sla_due_at);
        }

        return now()->greaterThan($this->sla_due_at);
    }
}
