<?php

namespace App\Enums;

enum TicketStatus: string
{
    case Open = 'open';
    case InProgress = 'in_progress';
    case Escalated = 'escalated';
    case Resolved = 'resolved';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::Open => 'Terbuka',
            self::InProgress => 'Sedang Ditangani',
            self::Escalated => 'Dieskalasi',
            self::Resolved => 'Selesai',
            self::Closed => 'Ditutup',
        };
    }

    public function isFinal(): bool
    {
        return in_array($this, [self::Resolved, self::Closed], true);
    }

    /** @return array<int, self> */
    public static function options(): array
    {
        return [self::Open, self::InProgress, self::Escalated, self::Resolved, self::Closed];
    }
}
