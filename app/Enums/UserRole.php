<?php

namespace App\Enums;

enum UserRole: string
{
    case Pelapor = 'pelapor';
    case Teknisi = 'teknisi';
    case Supervisor = 'supervisor';
    case Admin = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::Pelapor => 'Pelapor',
            self::Teknisi => 'Teknisi',
            self::Supervisor => 'Supervisor',
            self::Admin => 'Admin',
        };
    }
}
