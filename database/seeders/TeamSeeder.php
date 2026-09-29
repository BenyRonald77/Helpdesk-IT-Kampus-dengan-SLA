<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Membuat admin, dua tim teknisi dengan atasannya masing-masing, dan beberapa
 * pelapor (civitas kampus) sebagai data demo. Kredensial dicatat di README.md.
 */
class TeamSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password');

        User::query()->updateOrCreate(
            ['email' => 'admin@helpdesk.test'],
            [
                'name' => 'Admin IT',
                'password' => $password,
                'email_verified_at' => now(),
                'role' => UserRole::Admin,
            ]
        );

        $jaringan = Team::query()->firstOrCreate(['name' => 'Tim Jaringan & Infrastruktur']);
        $supervisorJaringan = User::query()->updateOrCreate(
            ['email' => 'supervisor.jaringan@helpdesk.test'],
            [
                'name' => 'Budi Santoso',
                'password' => $password,
                'email_verified_at' => now(),
                'role' => UserRole::Supervisor,
                'team_id' => $jaringan->id,
            ]
        );
        $jaringan->update(['supervisor_id' => $supervisorJaringan->id]);

        foreach ([
            ['name' => 'Andi Nugraha', 'email' => 'teknisi.jaringan1@helpdesk.test'],
            ['name' => 'Citra Ayu Lestari', 'email' => 'teknisi.jaringan2@helpdesk.test'],
        ] as $teknisi) {
            User::query()->updateOrCreate(
                ['email' => $teknisi['email']],
                [
                    'name' => $teknisi['name'],
                    'password' => $password,
                    'email_verified_at' => now(),
                    'role' => UserRole::Teknisi,
                    'team_id' => $jaringan->id,
                ]
            );
        }

        $aplikasi = Team::query()->firstOrCreate(['name' => 'Tim Aplikasi & Software']);
        $supervisorAplikasi = User::query()->updateOrCreate(
            ['email' => 'supervisor.aplikasi@helpdesk.test'],
            [
                'name' => 'Rina Wijayanti',
                'password' => $password,
                'email_verified_at' => now(),
                'role' => UserRole::Supervisor,
                'team_id' => $aplikasi->id,
            ]
        );
        $aplikasi->update(['supervisor_id' => $supervisorAplikasi->id]);

        foreach ([
            ['name' => 'Dedi Kurniawan', 'email' => 'teknisi.aplikasi1@helpdesk.test'],
            ['name' => 'Fajar Ramadhan', 'email' => 'teknisi.aplikasi2@helpdesk.test'],
        ] as $teknisi) {
            User::query()->updateOrCreate(
                ['email' => $teknisi['email']],
                [
                    'name' => $teknisi['name'],
                    'password' => $password,
                    'email_verified_at' => now(),
                    'role' => UserRole::Teknisi,
                    'team_id' => $aplikasi->id,
                ]
            );
        }

        foreach ([
            ['name' => 'Sari Dewi', 'email' => 'mahasiswa1@helpdesk.test'],
            ['name' => 'Hendra Gunawan', 'email' => 'staf1@helpdesk.test'],
            ['name' => 'Wulan Permatasari', 'email' => 'dosen1@helpdesk.test'],
        ] as $pelapor) {
            User::query()->updateOrCreate(
                ['email' => $pelapor['email']],
                [
                    'name' => $pelapor['name'],
                    'password' => $password,
                    'email_verified_at' => now(),
                    'role' => UserRole::Pelapor,
                ]
            );
        }
    }
}
