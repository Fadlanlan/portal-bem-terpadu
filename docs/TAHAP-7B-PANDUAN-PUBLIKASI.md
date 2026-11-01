# Tahap 7B — Panduan Publikasi Portal BEM Terpadu

Panduan operator untuk menyiapkan dan merilis aplikasi. Dokumen ini bukan bukti bahwa server, deployment, backup, atau prosedur pemulihan pernah diuji. Sesuaikan nilai contoh dan jalur dengan topologi milik organisasi sebelum menjalankannya.

## Status verifikasi panduan

- **Diverifikasi lokal:** PHP CLI 8.5.12; batas Composer aplikasi `^8.3`; Laravel 13.39.0; PHPUnit 12.5.42; Node 26.13.0; npm 11.20.0; build Vite lokal berhasil. Versi target server dan provider belum diperiksa.
- **Ditinjau, belum diuji:** prosedur instalasi, HTTPS, migrasi, storage, SMTP, backup, restore, rollback, dan smoke test di bawah.
- **Belum tersedia:** server staging/production, akses operator, mailbox staging, backup terverifikasi, dan lingkungan restore terpisah. Tidak ada deployment atau restore yang dilakukan pada Tahap 7B.

## 1. Prasyarat server

- Gunakan versi PHP yang kompatibel dengan `composer.json` (constraint proyek saat ini `^8.3`); target operasional proyek adalah PHP 8.5. Verifikasi versi CLI dan PHP-FPM/web server cocok.
- Pastikan ekstensi PHP yang dibutuhkan Laravel dan aplikasi aktif, termasuk PDO/MySQL, mbstring, OpenSSL, tokenizer, XML, ctype, JSON, fileinfo, serta GD atau Imagick untuk gambar. Jalankan `composer check-platform-reqs` pada release yang disiapkan.
- Gunakan MariaDB 10.6+ atau MySQL 8.0+ sesuai persyaratan deployment di README; versi server aktual belum diverifikasi.
- Pasang Nginx atau Apache dengan document root tepat ke direktori release `public/`. Jangan sajikan root repository.
- Sediakan HTTPS dengan sertifikat valid, firewall, sinkronisasi waktu, DNS, pemantauan kapasitas, dan akses shell terbatas.
- Siapkan database aplikasi, pengguna dengan hak minimum, storage persisten, serta direktori release/cache yang dapat ditulis oleh user web server.
- Build aset di CI/release host memakai versi Node/npm yang dikunci oleh workflow. Node tidak dibutuhkan pada runtime web bila aset hasil build sudah disertakan.

## 2. Environment dan pengelolaan secret

Simpan konfigurasi di secret manager atau file environment server yang hanya dapat dibaca user aplikasi. Jangan commit `.env`, menaruh secret di command line/log, atau menyalin `.env` production ke staging. Buat `APP_KEY` unik satu kali untuk instalasi baru dan lindungi nilainya; jangan menggantinya saat deployment rutin.

Konfigurasi production setidaknya harus memiliki:

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domain-resmi-kampus
APP_TIMEZONE=Asia/Jakarta

DB_CONNECTION=mysql
DB_HOST=alamat-database-internal
DB_PORT=3306
DB_DATABASE=nama-database-aplikasi
DB_USERNAME=pengguna-aplikasi
DB_PASSWORD=diambil-dari-secret-manager

FILESYSTEM_DISK=public
MAIL_MAILER=smtp
MAIL_HOST=host-smtp
MAIL_PORT=587
MAIL_USERNAME=pengguna-smtp
MAIL_PASSWORD=diambil-dari-secret-manager
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=no-reply@domain-resmi-kampus
MAIL_FROM_NAME="Portal BEM Terpadu"
ADMIN_NOTIFICATION_EMAIL=mailbox-advokasi-yang-disetujui
```

Gunakan `SESSION_SECURE_COOKIE=true` di HTTPS bila konfigurasi session aplikasi mendukungnya. Pilih driver session/cache/queue yang tersedia dan operasional; uji konsekuensi konfigurasi tersebut pada staging. Rahasia dan alamat contoh di atas bukan kredensial aktif.

## 3. Persiapan dan build release

1. Tinjau perubahan kode, migration baru, perubahan konfigurasi, dependency lockfile, serta catatan rilis. Pastikan tidak ada `.env`, dump database, atau file upload privat dalam paket.
2. Siapkan release di direktori baru agar release sebelumnya dapat dipulihkan. Jangan menimpa release aktif sebelum pemeriksaan selesai.
3. Pasang dependency PHP dari lockfile:

   ```bash
   composer install --no-dev --prefer-dist --optimize-autoloader
   composer check-platform-reqs
   ```

4. Pasang dependency frontend dari lockfile dan build aset pada CI/release host:

   ```bash
   npm ci
   npm run build
   ```

   Build lokal Tahap 7B berhasil, tetapi proses CI dan target server belum diuji.

5. Pasang secret/environment pada release baru melalui mekanisme terkelola. Verifikasi tanpa mencetak nilainya bahwa `APP_ENV=production`, `APP_DEBUG=false`, URL HTTPS, database yang dituju, storage, dan penerima email sesuai lingkungan.

## 4. Backup dan migrasi database

1. Jadwalkan rilis dan tentukan penanggung jawab aplikasi, database, serta rollback.
2. Ambil backup database dan `storage/app/public` secara konsisten sebelum perubahan. Enkripsi dan simpan salinan di lokasi terpisah; batasi akses dan retensi.
3. Verifikasi checksum arsip pada sumber dan salinan, misalnya `sha256sum arsip`; jangan melanjutkan jika tidak cocok. Catat waktu, ukuran, checksum, dan identitas backup tanpa menyalin data aplikasi ke tiket umum.
4. Pastikan nama host/database tujuan dari secret manager dan koneksi deployment sebelum command migrasi. Cocokkan dengan inventaris; **jangan mengarah ke database pengembangan `portal_bem`**.
5. Periksa `php artisan migrate:status` pada target dan tinjau semua migration yang akan berjalan. Uji migration pada staging yang memuat skema/data representatif. Pastikan perubahan kompatibel dengan kode lama selama proses rilis dan punya strategi pemulihan.
6. Setelah backup, persetujuan operator, dan jendela rilis dikonfirmasi, jalankan migrasi yang telah ditinjau:

   ```bash
   php artisan migrate --force
   ```

   Jangan jalankan `migrate:fresh`, `db:wipe`, `DROP DATABASE`, atau rollback massal. Jangan jalankan migration sebelum identitas target database dibuktikan.

Panduan backup di README telah dibaca, tetapi checksum/backup/restore aktual belum diuji. Untuk pemulihan, gunakan database dan direktori baru yang terpisah (contoh database `portal_bem_restore`), bukan database aktif. Ikuti bagian restore di bawah sebelum mengandalkan salinan sebagai backup yang dapat dipulihkan.

## 5. Storage dan permission

- Pastikan `storage/app/public` berada pada storage persisten yang sesuai dan termasuk backup bersama database.
- Buat symlink publik bila belum ada:

  ```bash
  php artisan storage:link
  ```

- Berikan hak tulis minimum yang diperlukan pada `storage/` dan `bootstrap/cache/`; hindari permission terbuka untuk semua user. Pastikan worker/web server dapat membaca upload dan tidak dapat mengakses file rahasia di luar public disk.
- Uji gambar berita dan placeholder dari browser staging sesudah deployment. Perintah dan hak akses server belum diverifikasi pada host aktual.

## 6. HTTPS, mailer, dan cache

- Arahkan semua HTTP ke HTTPS, aktifkan sertifikat valid, dan pastikan `APP_URL` menggunakan domain final. Verifikasi cookie sesi secure dan canonical URL dari luar jaringan deployment.
- Konfigurasikan SMTP dengan secret manager dan mailbox yang disetujui. Sebelum SMTP nyata diaktifkan, uji dahulu menggunakan mailer aman atau mailbox staging terkendali. Pastikan notifikasi admin tidak berisi kode pelacakan atau identitas anonim.
- Setelah environment dipasang pada release baru, bangun cache pada release tersebut:

  ```bash
  php artisan optimize:clear
  php artisan config:cache
  php artisan route:cache
  php artisan view:cache
  ```

  Jika event discovery/cache digunakan oleh workflow proyek, jalankan `php artisan event:cache` setelah memeriksa dampaknya. Verifikasi URL, database, mailer, debug, dan storage melalui pemeriksaan yang tidak mencetak secret.

## 7. Aktivasi dan smoke test pasca-deployment

Aktifkan release melalui mekanisme atomik/symlink organisasi setelah migration dan cache berhasil. Jika aplikasi memakai queue worker, restart worker dengan prosedur process manager yang sesuai. Smoke test melalui HTTPS:

- Beranda, berita, program kerja, agenda, organisasi, tentang, kontak, FAQ.
- Login dan logout dengan akun sintetis/akun operator yang disetujui; request langsung per role harus mengikuti kebijakan RBAC.
- Sitemap dan robots; pastikan sitemap hanya berisi URL publik dan tidak memperlakukan robots sebagai proteksi akses.
- Gambar/placeholder, halaman 404, navigasi, serta asset CSS/JS.
- Formulir aspirasi hanya dengan prosedur uji yang disetujui dan data sintetis. Jangan membuat laporan uji pada kanal produksi tanpa persetujuan pemilik layanan.
- Pastikan log tidak menunjukkan error baru dan tidak menampilkan stack trace atau data rahasia ke pengguna.

Uji SMTP produksi hanya jika pemilik menyetujui secara eksplisit mailbox penerima dan waktunya. Uji browser/CSRF, aksesibilitas, SMTP, dan recovery di staging sebelum menjadikannya kriteria kandidat rilis. Smoke test deployment belum pernah dilakukan pada sesi ini.

## 8. Pemulihan backup pada target terpisah

Jalankan latihan restore berkala pada host pemulihan terisolasi, bukan production:

1. Identifikasi arsip dari katalog backup dan verifikasi checksum terhadap catatan independen.
2. DBA membuat database kosong baru dan memverifikasi host/nama target secara independen. Gunakan nama yang jelas seperti `portal_bem_restore`; jangan menghapus database yang telah ada untuk menyiapkannya.
3. Pulihkan dump ke database tersebut dengan kredensial terbatas. Sebelum restore, pastikan file SQL tidak memuat perintah yang mengubah nama/target database (`CREATE DATABASE` atau `USE`) yang dapat mengarahkan restore ke sumber lain. Hentikan jika isi dump atau target belum dapat dibuktikan aman.
4. Ekstrak arsip storage ke direktori pemulihan baru yang kosong dan terpisah. Jangan mengekstrak ke storage aktif.
5. Cocokkan checksum, tabel inti, jumlah record yang telah dicatat, file gambar, dan referensi file. Bangun symlink/cache untuk instance pemulihan dan jalankan smoke test memakai konfigurasi/environment pemulihan.
6. Catat durasi, hasil, selisih, dan tindakan korektif tanpa memasukkan PII atau isi aspirasi ke laporan umum. Pertahankan backup sumber sesuai retensi.

Prosedur ini ditinjau secara dokumentasi; belum ada arsip atau lingkungan restore aktual yang tersedia dan tidak ada restore yang diuji.

## 9. Rollback dan respons insiden

- Jika smoke test gagal, hentikan promosi traffic dan pertahankan log/bukti. Alihkan aplikasi ke release kode sebelumnya melalui mekanisme release yang disetujui, lalu periksa kompatibilitas skema sebelum mengalihkan kode lama ke database baru.
- Jangan menjalankan rollback migration secara otomatis. Migration yang sudah mengubah/menulis data mungkin tidak dapat dibatalkan dengan aman. DBA dan pemilik aplikasi memilih pemulihan berdasarkan backup terverifikasi dan target terpisah.
- Untuk insiden privasi/security: batasi akses yang terdampak, pertahankan log/artefak asli, jangan menyalin payload rahasia ke tiket terbuka, eskalasi ke petugas keamanan/privasi organisasi, dan ikuti kebijakan pelaporan insiden serta retensi yang berlaku.
- Setelah pemulihan/rollback, ulangi smoke test dan catat keputusan operator. Jangan menyatakan insiden selesai sebelum akses dan integritas data diperiksa.

## 10. Pemantauan pasca-rilis

- Pantau status HTTP, error rate, kesehatan koneksi database, disk, storage, queue/SMTP, masa berlaku TLS, serta keberhasilan backup.
- Tinjau log oleh personel berwenang. Ringkas kategori/waktu tanpa menyebarkan password, token, cookie/session ID, tracking code, identitas pelapor, isi aspirasi, atau kredensial SMTP.
- Jangan menghapus atau mengosongkan log untuk menutupi error. Pertahankan log sesuai kebijakan retensi dan batasi aksesnya.
- Periksa alert backup dan jadwalkan latihan restore ke target terpisah. Keberadaan file backup saja bukan bukti bahwa pemulihan akan berhasil.

## Checklist persetujuan operator

- [ ] Server, versi runtime, ekstensi, TLS, dan document root telah diperiksa.
- [ ] Secret/environment production disediakan di kanal aman; debug mati dan URL HTTPS benar.
- [ ] Backup database dan storage baru selesai, checksum cocok, dan target restore terpisah telah dipetakan.
- [ ] Migration ditinjau dan diuji pada staging; target production dikonfirmasi DBA.
- [ ] Build dan dependency lockfile cocok dengan release yang disetujui.
- [ ] Hak storage, cache, SMTP, queue, dan pemantauan telah diverifikasi.
- [ ] Smoke test dan rollback owner/jadwal tersedia.
- [ ] Pemilik layanan menyetujui jendela rilis serta setiap pengiriman email nyata.

Checklist ini adalah prosedur; kotak tidak berarti tindakan telah dilakukan.
