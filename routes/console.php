<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Eskalasi otomatis tiket yang lewat SLA. Perlu `php artisan schedule:work`
// (pengembangan) atau cron yang memanggil `php artisan schedule:run` setiap
// menit (produksi) agar ini benar-benar berjalan. Lihat README.md.
Schedule::command('escalate:overdue-tickets')->everyFiveMinutes();
