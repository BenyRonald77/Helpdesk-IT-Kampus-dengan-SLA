<?php

namespace App\Services;

use App\Enums\TicketPriority;
use App\Models\SlaRule;
use Carbon\CarbonInterface;

/**
 * Menghitung target waktu penyelesaian (sla_due_at) sebuah tiket dari prioritasnya.
 *
 * Aturan: sla_due_at = waktu_acuan + resolution_minutes (dari sla_rules sesuai priority).
 * Dipakai baik saat tiket dibuat (waktu_acuan = created_at) maupun saat prioritas
 * diubah (waktu_acuan tetap created_at semula, hanya jendela resolusinya yang berubah
 * sesuai aturan prioritas baru).
 */
class SlaCalculator
{
    public static function dueAt(TicketPriority $priority, CarbonInterface $from): ?CarbonInterface
    {
        $rule = SlaRule::forPriority($priority);

        if (! $rule) {
            return null;
        }

        return $from->copy()->addMinutes($rule->resolution_minutes);
    }
}
