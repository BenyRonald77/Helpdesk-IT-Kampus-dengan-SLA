# Helpdesk IT Kampus dengan SLA

Aplikasi pelaporan gangguan IT internal kampus dengan target waktu penyelesaian (SLA) otomatis berdasarkan prioritas, eskalasi otomatis ke atasan tim saat tiket terlambat, dan laporan performa bulanan per teknisi/tim.

Lihat `PRD.md` untuk latar belakang, aktor, dan kriteria penerimaan lengkap. Lihat `DESIGN.md` untuk arah desain dan alasan pemilihan warna.

## Fitur

- **Tiket berbasis prioritas dengan timer SLA otomatis**: target penyelesaian (`sla_due_at`) dihitung dan disimpan otomatis dari prioritas saat tiket dibuat, dan dihitung ulang bila prioritas diubah. Halaman detail tiket menghitung sisa waktu/keterlambatan langsung dari server setiap kali dimuat, dengan indikator warna hijau/kuning/merah sesuai ambang waktu nyata.
- **Eskalasi otomatis ke supervisor**: perintah terjadwal `escalate:overdue-tickets` menemukan tiket yang lewat SLA dan belum ditangani, lalu mengeskalasinya ke supervisor tim terkait (fallback ke admin bila tim belum punya supervisor). Supervisor punya dashboard eskalasi untuk menugaskan ulang teknisi atau turun tangan langsung.
- **Laporan performa bulanan**: rekap per teknisi dan per tim untuk bulan terpilih: jumlah tiket ditangani, rata-rata waktu resolusi, jumlah breach SLA, dan persentase tepat waktu, dihitung langsung dari data tiket (tidak ada angka rekaan).
- **Alur tiket lengkap**: pelapor membuat & memantau tiket, teknisi mengambil dari antrian tim atau menangani tiket yang ditugaskan, komentar/riwayat pada setiap tiket, admin mengelola kategori, aturan SLA, tim, dan pengguna.

## Peran pengguna

| Peran | Bisa apa |
|---|---|
| **Pelapor** | Membuat tiket, melihat & mengomentari tiketnya sendiri |
| **Teknisi** | Melihat tiket yang ditugaskan + antrian tim yang belum diambil, self-assign, mengubah status/prioritas, menandai selesai |
| **Supervisor** | Melihat tiket timnya, dashboard tiket yang dieskalasi ke timnya, menugaskan ulang teknisi, melihat laporan performa timnya |
| **Admin** | Semua di atas + mengelola kategori, aturan SLA, tim, pengguna, dan laporan seluruh tim |

## Kebutuhan

- PHP 8.4, Composer 2.8
- Node.js & npm
- SQLite (bawaan untuk pengembangan/sandbox ini)

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate

touch database/database.sqlite
php artisan migrate --seed

npm install
npm run build   # atau: npm run dev, untuk pengembangan dengan hot reload

php artisan serve
```

Buka `http://127.0.0.1:8000`.

### Menjalankan eskalasi otomatis

Fitur eskalasi otomatis (`escalate:overdue-tickets`) sudah terdaftar di scheduler (`routes/console.php`, tiap 5 menit). Ini **tidak berjalan sendiri** kecuali salah satu dari berikut aktif:

- **Pengembangan lokal**: jalankan `php artisan schedule:work` di terminal terpisah selama aplikasi dipakai.
- **Produksi**: tambahkan satu entri cron yang memanggil `php artisan schedule:run` setiap menit:
  ```
  * * * * * cd /path/ke/aplikasi && php artisan schedule:run >> /dev/null 2>&1
  ```

Untuk menjalankan eskalasi sekali secara manual (misalnya untuk memverifikasi): `php artisan escalate:overdue-tickets`.

### Database produksi (MySQL)

Sandbox ini memakai SQLite. Untuk deployment sebenarnya, isi `.env` dengan blok MySQL yang sudah disediakan (dikomentari) di `.env.example`: ganti `DB_CONNECTION=sqlite` menjadi `DB_CONNECTION=mysql` dan isi `DB_HOST`/`DB_DATABASE`/`DB_USERNAME`/`DB_PASSWORD`, lalu jalankan `php artisan migrate --seed` seperti biasa.

## Kredensial demo

Semua pengguna demo memakai password: **`password`**

| Peran | Email |
|---|---|
| Admin | `admin@helpdesk.test` |
| Supervisor (Tim Jaringan & Infrastruktur) | `supervisor.jaringan@helpdesk.test` |
| Supervisor (Tim Aplikasi & Software) | `supervisor.aplikasi@helpdesk.test` |
| Teknisi (Tim Jaringan) | `teknisi.jaringan1@helpdesk.test`, `teknisi.jaringan2@helpdesk.test` |
| Teknisi (Tim Aplikasi) | `teknisi.aplikasi1@helpdesk.test`, `teknisi.aplikasi2@helpdesk.test` |
| Pelapor | `mahasiswa1@helpdesk.test`, `staf1@helpdesk.test`, `dosen1@helpdesk.test` |

Data demo (`database/seeders/TicketSeeder.php`) sudah mencakup: tiket selesai tepat waktu, tiket selesai terlambat (breach), tiket masih terbuka dan sudah lewat SLA (belum dieskalasi, untuk mencoba perintah eskalasi manual), dan satu tiket yang sudah pernah dieskalasi.

## Menjalankan pengujian

```bash
php artisan test
```

Cakupan pengujian meliputi: perhitungan `sla_due_at` dari prioritas saat tiket dibuat, perhitungan ulang saat prioritas diubah, perintah eskalasi (hanya mengeskalasi tiket yang benar-benar lewat SLA, dengan fallback ke admin bila tim belum punya supervisor), akurasi laporan performa bulanan terhadap data uji yang diketahui, tiket yang selesai terlambat tetap terhitung breach di laporan meski flag eskalasi tidak pernah diset, serta alur HTTP end-to-end (buat tiket, self-assign, resolve, dashboard eskalasi per tim) dan halaman-halaman utama untuk setiap peran memakai data demo yang sama.

## Struktur teknis singkat

- Laravel 13, autentikasi Breeze (stack Livewire) untuk login/registrasi/profil.
- Peran pengguna lewat kolom `role` pada `users` + tabel `teams` (dengan `supervisor_id`) dan `team_id` pada teknisi.
- Logika SLA: `App\Models\Ticket` (hook `creating`/`updating`), `App\Services\SlaCalculator`.
- Eskalasi otomatis: `App\Console\Commands\EscalateOverdueTickets`, dijadwalkan di `routes/console.php`.
- Laporan performa: `App\Services\TeamPerformanceReport`.
- Otorisasi per tiket: `App\Policies\TicketPolicy`; otorisasi per halaman berbasis peran: middleware `role` (`App\Http\Middleware\EnsureUserHasRole`).
