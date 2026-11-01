# Tahap 10 — Rencana Perbaikan dan Tindak Lanjut Lokal

Tanggal audit: 31 Oktober 2026 (Asia/Bangkok)  
Status: rencana tindak lanjut; belum ada perubahan source aplikasi.

## Ringkasan

Pemeriksaan lokal menemukan satu risiko privasi tingkat **P2/medium** pada CSV aspirasi: export menghilangkan kolom identitas langsung, tetapi menyertakan subjek, isi aspirasi, tiket, dan tanggapan admin. Teks bebas dapat mengidentifikasi pelapor anonim jika pelapor menyebut identitasnya sendiri. Tidak ada record atau data nyata yang digunakan untuk mereproduksi kebocoran; temuan berasal dari tinjauan kode.

Audit log juga menyimpan IP address, user-agent, serta metadata aksi admin. Ini mengikuti implementasi saat ini dan akses route dibatasi, tetapi retensi/minimisasi belum dibuktikan pada sesi ini.

Jangan ubah kode sebelum pemilik layanan menentukan kebutuhan isi CSV, tingkat anonimisasi, dan masa retensi audit. Tidak ada bug yang diperbaiki pada Tahap 10.

## Prioritas

| ID | Prioritas | Temuan/risiko | Bukti | Tindakan yang disarankan | Syarat/verifikasi |
|---|---|---|---|---|---|
| FIX-01 | P2 — Privasi | CSV memuat subjek, teks aspirasi lengkap, nomor tiket dan tanggapan admin. Formula injection dinetralkan dan kolom nama/email/NIM/IP/hash/catatan internal tidak disertakan, tetapi teks bebas dapat berisi identitas. | `ReportAdminController::export()` membangun kolom dari `ticket_number`, `subject`, `message`, `admin_response`. Tidak diuji dengan aspirasi nyata atau data sintetis tersimpan. | Minta keputusan pemilik: apakah export operasional tetap perlu teks lengkap; pertimbangkan export teredaksi untuk laporan agregat, akses paling minimum, dan pemberitahuan bahwa teks bebas bisa mengidentifikasi. Jika diubah, buat test regresi untuk field anon/nonanonim serta formula prefixes. | Persetujuan product/privacy owner dan database test terisolasi; jangan menguji di database utama. |
| FIX-02 | P3 — Privasi/retensi | Audit log merekam IP address dan user-agent, serta beberapa deskripsi aksi memuat nama/email pengelola. Masa retensi dan penghapusan terjadwal tidak diverifikasi. | `AdminAuditLog::record()` menyimpan field tersebut; daftar route membatasi UI audit ke middleware super admin. | Tetapkan tujuan, hak akses, retensi, minimisasi, backup/retensi dan proses pemenuhan kebijakan organisasi. Pertahankan field hanya jika kebutuhan audit disetujui. | Tinjauan pemilik keamanan/privasi; jangan mengekspos nilai log dalam tiket/laporan umum. |
| FIX-03 | P2 sebelum hosting publik | Environment lokal memakai `APP_DEBUG=true`, session/cache database dan mailer log. Ini adalah konfigurasi developer saat ini; tidak diuji sebagai server internet. | `config:show` nonrahasia. | Sebelum hosting, gunakan environment terpisah, `APP_DEBUG=false`, HTTPS, secret manager, DB/storage baru dan mailer sesuai persetujuan. | Pemeriksaan deployment khusus; jangan mengubah `.env` lokal untuk simulasi production. |
| FIX-04 | P2 sebelum uji tulis | Browser/admin, form POST, CSRF runtime, CRUD dan upload belum diuji langsung. Akun sintetis dan database/storage test terisolasi belum disediakan. | Tidak ada akun test/approval. Chrome tersedia tetapi proses headless tidak dapat dijalankan oleh sandbox. | Minta operator membuat DB test baru dan storage terpisah, menyediakan akun sintetis terotorisasi, lalu menjalankan checklist browser dari desktop/runner yang berizin. | Operator memastikan tidak ada koneksi ke DB utama dan mailer awal `array`; uji rate limit hanya pada instance yang tidak dipakai orang lain. |
| FIX-05 | P2 operasional | Backup, checksum dan restore belum diperiksa secara aktual. | README memiliki prosedur; arsip dan target restore tidak tersedia. | Operator/DBA menyediakan backup yang diketahui asalnya, checksum cocok, DB baru, serta direktori restore baru; lakukan latihan dan smoke test di target terpisah. | Jangan menimpa backup, menghapus DB, atau merestore ke database/storage utama. |

## Urutan tindakan yang disarankan

1. Pemilik produk/privacy meninjau FIX-01 dan menentukan format CSV/anonimisasi.
2. Security/operations meninjau retensi audit log serta konfigurasi lokal sebelum hosting.
3. Operator menyediakan database dan storage uji baru dengan data sintetis dan akun test yang disetujui.
4. Jalankan test browser, role, form, CSRF, upload dan aksesibilitas hanya pada lingkungan uji tersebut; catat bukti nonrahasia.
5. Jalankan SMTP hanya setelah izin eksplisit serta mailbox staging terkonfirmasi.
6. Uji backup/restore hanya pada target baru setelah checksum dan isolasi dibuktikan.
7. Jika FIX-01 diubah, tambahkan test regresi, jalankan suite penuh dan build. Jangan mengubah source hanya untuk membuat audit tampak selesai.

### Tidak termasuk pekerjaan otomatis

Tidak ada domain publik atau hosting pada tahap ini. Tidak ada perubahan production, database, migration, akun admin, upload, log, `.env`, atau dependency. Rencana ini tidak mengesahkan deploy atau menyatakan siap produksi.
