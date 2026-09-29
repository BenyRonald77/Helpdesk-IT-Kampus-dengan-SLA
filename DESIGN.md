# DESIGN.md: Helpdesk IT Kampus dengan SLA

## Apa ini

Alat kerja internal untuk unit IT kampus: pelapor membuat tiket, teknisi menanganinya, supervisor mengawasi eskalasi timnya, admin mengatur data master. Ini bukan situs marketing dan tidak butuh meyakinkan siapa pun untuk "mendaftar", setiap pengguna sudah punya akun dan tugas yang jelas begitu masuk. Desainnya melayani orang yang membuka aplikasi ini berkali-kali sehari di sela pekerjaan lain, bukan pengunjung sekali lihat.

**Reading this as:** internal ticketing/admin tool untuk staf IT kampus, gaya minim-dekorasi ala perangkat kerja (bukan produk konsumen), dial `ENERGY 2 / RHYTHM 2 / MOTION 1`.

## Dial

- **ENERGY 2**: setara Stripe/Vercel, bersih dan modern, tapi tidak sedatar GOV.UK juga tidak seramai landing page produk. Cocok untuk alat kerja harian: terasa dirawat, tidak menuntut perhatian.
- **RHYTHM 2**: konsisten dengan beberapa variasi disengaja. Halaman daftar tiket, halaman detail tiket, dan dashboard laporan punya bentuk berbeda karena tugasnya berbeda (memindai banyak baris vs membaca satu tiket vs membaca angka), bukan template yang diulang.
- **MOTION 1**: hanya hover/focus state dan transisi halus bawaan Livewire (loading state saat submit). Tidak ada animasi hias; pengguna butuh kecepatan dan kepastian, bukan pertunjukan.

## Palet warna

- **Netral** (tidak dihitung sebagai warna inti per R-29): skala `slate` Tailwind untuk teks, latar, dan border. Alasan: skala netral bawaan Tailwind sudah punya kontras yang teruji dan konsisten di seluruh komponen Breeze, tidak perlu diracik ulang.
- **Aksen (1 warna): Indigo** (`indigo-600` di light mode, `indigo-400` di dark mode), dipakai untuk tombol aksi utama, link aktif, dan ring fokus. Alasan: indigo netral secara emosional (tidak menyiratkan bahaya atau sukses seperti merah/hijau), sehingga tidak bertentangan secara visual dengan skema warna status SLA di bawah, dan cukup gelap untuk kontras AA di atas putih maupun di atas slate-900.

### Skema warna status SLA (fungsional, bukan dekoratif)

Tiket punya tiga status warna yang **menandai urgensi SLA yang nyata** berdasarkan ambang waktu yang jelas, dihitung di server dari `sla_due_at`, bukan dipasang untuk mempercantik tampilan (antislop R-01 / R-31):

| Warna | Kelas Tailwind (light / dark) | Arti | Ambang |
|---|---|---|---|
| Hijau | `emerald-600` / `emerald-400` | Aman | Sisa waktu > 50% dari jendela resolusi, atau selesai sebelum jatuh tempo |
| Kuning | `amber-600` / `amber-400` | Mendekati | Sisa waktu ≤ 50% dari jendela resolusi, masih berjalan |
| Merah | `red-600` / `red-400` | Lewat SLA | Sudah lewat `sla_due_at`, baik masih terbuka maupun baru selesai setelah lewat tempo |

Ini adalah satu-satunya tempat di aplikasi selain aksen indigo yang memakai warna bermakna, bukan warna acak per elemen. Dihitung oleh `Ticket::slaColorStatus()` (lihat `app/Models/Ticket.php`) setiap halaman dimuat dari data server, konsisten dengan prinsip waktu-otoritatif-server: pengguna tidak bisa memanipulasi tampilan SLA dari sisi klien.

## Tipografi

Font bawaan Breeze (`Instrument Sans` via Tailwind default stack, di-load dari Google Fonts oleh scaffold Laravel). Alasan: ini alat kerja, bukan halaman identitas merek; font default yang jelas dibaca pada tabel dan formulir sudah cukup, mengganti font tanpa alasan brand hanya menambah risiko tanpa manfaat (R-06).

## Layout & komponen

- **Tidak ada ilustrasi.** Tidak ada koneksi nyata antara ilustrasi generik dan sistem tiket IT internal (R-22).
- **Tidak ada statistik rekaan.** Semua angka pada dashboard dan laporan berasal dari query nyata ke tabel `tickets`/`escalations` (lihat `TeamPerformanceReport`); kalau datanya nol, tampilkan nol (R-17, R-38).
- **Tabel adalah komponen utama**, bukan kartu. Daftar tiket, daftar eskalasi, dan baris laporan memakai tabel karena tugas penggunanya adalah memindai banyak baris dan membandingkan kolom, bukan menjelajah galeri kartu (C-3).
- **Badge status** (bukan pil dekoratif): dipakai untuk status tiket (`open`/`in_progress`/`escalated`/`resolved`/`closed`) dan warna SLA di atas, karena keduanya benar-benar menandai state, sesuai kebutuhan (R-09).
- **Radius**: `rounded-md` konsisten untuk kartu/tombol/input mengikuti bawaan Breeze, tidak dibuat pil di semua elemen (R-11).
- **Aksen tunggal**: indigo hanya pada tombol utama dan link aktif di navigasi, tidak diulang di badge, garis, atau latar (satu aksen yang disengaja, lihat antislop Part 3).

## Aksesibilitas

- Semua badge warna status memakai teks + label kata (bukan warna saja) supaya tidak bergantung pada persepsi warna.
- Kontras diuji manual: `emerald-700`/`amber-700`/`red-700` dengan latar `*-50` di light mode, dan `emerald-300`/`amber-300`/`red-300` di atas `slate-900` untuk dark-safe fallback bila diperlukan di masa depan (aplikasi ini sendiri belum menyediakan toggle dark mode karena bukan fokus versi ini; base Breeze tetap dipertahankan agar tidak rusak jika suatu saat diaktifkan).
- Semua kontrol interaktif dapat dicapai dengan Tab dan memakai focus ring bawaan Tailwind (`focus:ring`), tidak dihapus tanpa pengganti (R-32).
