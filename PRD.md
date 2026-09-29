# PRD: Helpdesk IT Kampus dengan SLA

## 1. Latar Belakang & Tujuan

Unit IT kampus menerima laporan gangguan (jaringan, hardware, software, akun/akses) dari mahasiswa dan staf melalui berbagai jalur informal (chat, datang langsung, telepon), sehingga sulit dipantau, sering terlambat ditangani, dan tidak ada data objektif soal kinerja teknisi. Aplikasi ini menyediakan satu pintu pelaporan tiket, aturan target waktu penanganan (SLA) berdasarkan prioritas, eskalasi otomatis ke atasan bila tiket terlambat, dan laporan kinerja bulanan per teknisi/tim.

Tujuan:
- Setiap gangguan tercatat sebagai tiket dengan status dan riwayat yang jelas.
- Setiap tiket punya target waktu penyelesaian yang berjalan otomatis sesuai prioritas.
- Tiket yang melewati target langsung dieskalasi ke atasan tim terkait tanpa perlu dipantau manual.
- Ada laporan bulanan objektif (bukan perkiraan) untuk evaluasi kinerja teknisi dan tim.

## 2. Aktor

- **Pelapor** (civitas kampus: mahasiswa/dosen/staf): membuat tiket, memantau status tiketnya sendiri, menambah komentar.
- **Teknisi**: menangani tiket yang ditugaskan ke dirinya atau ke timnya, mengubah status, menambah komentar, menandai tiket selesai.
- **Supervisor** (atasan tim teknisi): menerima notifikasi/daftar eskalasi tiket timnya yang terlambat, dapat menugaskan ulang (reassign) ke teknisi lain atau turun tangan langsung, melihat laporan kinerja timnya.
- **Admin**: mengelola kategori, aturan SLA, tim, dan pengguna; melihat seluruh laporan lintas tim.

## 3. Lingkup Fitur

**Termasuk lingkup (in scope):**
1. Pembuatan dan pengelolaan tiket dengan prioritas dan kategori.
2. Timer SLA otomatis berdasarkan prioritas, dihitung dan disimpan sejak tiket dibuat/prioritas diubah.
3. Eskalasi otomatis ke supervisor tim saat tiket melewati target SLA.
4. Laporan kinerja bulanan per teknisi dan per tim.
5. Manajemen kategori, aturan SLA, tim, dan pengguna oleh admin.
6. Komentar/log aktivitas pada tiket.

**Di luar lingkup (out of scope) untuk versi ini:**
- Notifikasi email/WhatsApp real-time (cukup tampil di aplikasi; hook eskalasi bisa dikembangkan lebih lanjut).
- Integrasi single sign-on kampus.
- Aplikasi mobile terpisah.
- Live chat antara pelapor dan teknisi.

## 4. Entitas Data Utama

- **User**: nama, email, password, `role` (pelapor/teknisi/supervisor/admin), `team_id` (untuk teknisi, tim tempat ia bertugas).
- **Team**: nama tim, `supervisor_id` (atasan tim).
- **Category**: nama kategori gangguan (Jaringan, Hardware, Software, Akun/Akses).
- **SlaRule**: `priority` (low/medium/high/critical), `response_minutes` (target respons pertama), `resolution_minutes` (target penyelesaian penuh).
- **Ticket**: pelapor, kategori, tim yang menangani, teknisi yang ditugaskan, judul, deskripsi, prioritas, status (open/in_progress/escalated/resolved/closed), `first_responded_at`, `resolved_at`, `sla_due_at` (disimpan, dihitung dari waktu dibuat + `resolution_minutes` sesuai prioritas saat itu), `sla_breached`.
- **TicketComment**: tiket, penulis, isi komentar, waktu.
- **Escalation**: tiket, waktu eskalasi, supervisor tujuan, alasan.

## 5. Alur End-to-End

1. **Pelapor membuat tiket**: pilih kategori, prioritas, isi judul/deskripsi. Sistem otomatis mencari `SlaRule` sesuai prioritas dan mengisi `sla_due_at = created_at + resolution_minutes`.
2. **Timer SLA berjalan otomatis**: setiap kali halaman detail tiket dibuka, sisa waktu/keterlambatan dihitung ulang dari `sla_due_at` dikurangi waktu server saat itu (server-authoritative, bukan hitungan di browser), dan ditandai warna: hijau (on-time, sisa waktu >50% dari jendela resolusi), kuning (mendekati, sisa waktu ≤50%), merah (breached, sudah lewat `sla_due_at`).
3. **Penugasan**: admin/supervisor menugaskan tiket ke tim/teknisi, atau teknisi melakukan self-assign dari daftar tiket tim yang belum ditugaskan. Saat pertama ditugaskan, `first_responded_at` diisi.
4. **Jika prioritas diubah** oleh teknisi/admin (misal ternyata bukan kritikal), `sla_due_at` dihitung ulang dari `created_at` + `resolution_minutes` aturan prioritas baru. Riwayat sebelumnya tidak diubah, hanya target berjalan ke depan yang berubah.
5. **Eskalasi otomatis**: perintah terjadwal (`escalate:overdue-tickets`) berjalan setiap beberapa menit, memeriksa tiket yang belum `resolved`/`closed` dan `sla_due_at` sudah lewat. Untuk setiap tiket demikian: status diubah ke `escalated`, `sla_breached = true`, dibuat baris `Escalation` dengan `escalated_to` = supervisor tim tiket tersebut (jika tim/supervisor tidak ada, jatuh ke admin pertama yang ditemukan sebagai fallback, supaya eskalasi tidak hilang begitu saja).
6. **Supervisor menindaklanjuti**: dashboard supervisor menampilkan tiket timnya yang berstatus `escalated`, dengan aksi reassign ke teknisi lain atau intervensi langsung (tambah komentar/ubah status).
7. **Tiket selesai**: teknisi menandai `resolved` (mengisi `resolved_at`). Jika `resolved_at` > `sla_due_at`, tiket tetap dihitung sebagai breach di laporan meskipun proses eskalasi otomatis belum pernah menjangkau tiket tersebut.
8. **Laporan performa bulanan**: admin (semua tim) dan supervisor (timnya saja) memilih bulan, sistem menghitung dari data tiket riil: jumlah tiket ditangani, rata-rata waktu penyelesaian (jam), jumlah breach SLA, persentase tepat waktu per teknisi dan per tim.

## 6. Kriteria Penerimaan per Fitur Inti

### Fitur 1: Tiket berbasis prioritas dengan timer SLA otomatis
- [ ] Saat tiket dibuat, `sla_due_at` terisi otomatis sesuai `SlaRule` dari prioritas yang dipilih.
- [ ] Mengubah prioritas tiket menghitung ulang `sla_due_at` dari waktu pembuatan tiket + `resolution_minutes` prioritas baru.
- [ ] Halaman detail tiket menampilkan sisa waktu atau keterlambatan yang dihitung di server pada setiap load halaman (bukan hanya countdown JS di browser).
- [ ] Indikator warna (hijau/kuning/merah) tampil konsisten dengan ambang: >50% sisa = hijau, ≤50% sisa = kuning, lewat = merah.

### Fitur 2: Eskalasi otomatis ke supervisor
- [ ] Perintah artisan terjadwal menemukan tiket overdue (status bukan resolved/closed, `sla_due_at` < now) dan mengeskalasinya: status -> `escalated`, `sla_breached` -> true, baris `Escalation` tercatat.
- [ ] `escalated_to` terisi supervisor tim tiket; bila tim/supervisor tidak ada, fallback ke admin.
- [ ] Tiket yang belum overdue TIDAK dieskalasi oleh perintah ini.
- [ ] Supervisor punya halaman yang menampilkan daftar tiket `escalated` milik timnya, dengan aksi reassign teknisi dan tambah komentar/ubah status.

### Fitur 3: Laporan performa bulanan
- [ ] Admin dapat memilih bulan & tahun dan melihat rekap seluruh tim; supervisor hanya melihat timnya.
- [ ] Per teknisi: jumlah tiket ditangani pada bulan tersebut, rata-rata waktu resolusi (jam, hanya tiket yang benar-benar resolved), jumlah breach, persentase tepat waktu.
- [ ] Tiket yang resolve terlambat (setelah `sla_due_at`) tetap terhitung breach di laporan walau proses eskalasi otomatis belum pernah berjalan pada tiket itu.
- [ ] Teknisi tanpa tiket selesai pada bulan tersebut tetap muncul di laporan dengan angka 0, bukan disembunyikan atau diisi angka rekaan.
