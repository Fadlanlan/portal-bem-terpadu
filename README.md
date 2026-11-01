# Portal BEM Terpadu — Sistem Informasi dan Layanan Kemahasiswaan

Portal BEM Terpadu adalah aplikasi web resmi Badan Eksekutif Mahasiswa yang dirancang untuk menyampaikan informasi publik, transparansi program kerja, agenda kegiatan, struktur organisasi, serta kanal advokasi dan aspirasi mahasiswa yang aman, terintegrasi, dan terpercaya.

---

## 🛠️ Stack Teknologi

- **Framework Backend:** Laravel 13 (PHP 8.5)
- **Basis Data:** MariaDB / MySQL (XAMPP Environment)
- **Frontend & Styling:** Blade Templating Engine & Tailwind CSS v4 (`@tailwindcss/vite`)
- **Asset Bundler:** Vite 8
- **Testing Framework:** PHPUnit / Laravel Feature & Unit Testing

---

## 🚀 Fitur Utama Sistem

### 1. Portal Publik Mahasiswa
- **Beranda Interaktif:** Menampilkan pengumuman penting, statistik program kerja, berita terbaru, dan kalender kegiatan terdekat.
- **Kanal Berita & Pengumuman:** Informasi resmi organisasi terfilter berdasarkan kategori dengan penomoran halaman (pagination) dan pencarian.
- **Program Kerja & Agenda:** Transparansi agenda kerja kabinet beserta indikator target pencapaian dan status keterlaksanaan.
- **Struktur Organisasi:** Daftar pengurus kabinet dari BPH, kementerian, hingga biro beserta tugas pokok dan fungsi.
- **Kanal Aspirasi Mahasiswa:** Pengiriman keluhan/saran dengan jaminan kerahasiaan:
  - Pelapor dapat memilih opsi **Anonim** (tanpa menyimpan nama, NIM, email, dan telepon).
  - Setiap laporan menghasilkan **Nomor Tiket Publik** (contoh: `ASP-202610-ABCD`) dan **Kode Pelacakan Rahasia** (hanya diberikan sekali kepada pelapor).
  - Kode pelacakan diverifikasi menggunakan *cryptographic hash* di sisi server; data hash mentah tidak pernah diekspos ke antarmuka atau log.

### 2. Autentikasi & Manajemen Pengelola
- **Proteksi Akses:** Endpoint administrasi terlindungi middleware autentikasi dan rate-limiting (`throttle:admin-login` dengan pembatasan 5 percobaan per menit).
- **Session Security:** Regenerasi ID sesi saat login sukses guna mencegah *session fixation*, dan *session invalidation* saat logout.
- **Proteksi Akun Terakhir:** Akun `super_admin` aktif terakhir dilindungi secara permanen di tingkat controller dan database sehingga tidak dapat dihapus atau dinonaktifkan secara tidak sengaja.
- **Matriks Izin Akses Berbasis Peran (Role-Based Access Control):**

| Fitur / Modul | Super Admin | Admin | Advokasi | Editor |
| :--- | :---: | :---: | :---: | :---: |
| **Dashboard Metrik Utama** | ✅ | ✅ | ✅ | ✅ |
| **Manajemen Pengguna Admin** | ✅ | ❌ | ❌ | ❌ |
| **Audit Log Sistem** | ✅ | ❌ | ❌ | ❌ |
| **Pengelolaan Aspirasi & Tanggapan** | ✅ | ✅ | ✅ | ❌ |
| **Laporan & Rekapitulasi Aspirasi** | ✅ | ✅ | ✅ | ❌ |
| **Ekspor CSV Aspirasi** | ✅ | ✅ | ✅ | ❌ |
| **Kelola Berita / Artikel Publik** | ✅ | ✅ | ❌ | ✅ |
| **Kelola Program Kerja** | ✅ | ✅ | ❌ | ✅ |
| **Kelola Agenda & Acara** | ✅ | ✅ | ❌ | ✅ |
| **Kelola Struktur Organisasi** | ✅ | ✅ | ❌ | ✅ |

### 3. Notifikasi Email Aspirasi (Decoupled & Secure)
- **Pemberitahuan Aspirasi Masuk:** Mengirim ringkasan informasi aspirasi baru ke email pengelola (`ADMIN_NOTIFICATION_EMAIL`) tanpa pernah menyertakan *tracking code* rahasia.
- **Pemberitahuan Status & Tanggapan Resmi:** Dikirim ke email pelapor hanya apabila pelapor bukan anonim dan menyertakan alamat email valid.
- **Prinsip Keamanan Transaksi:**
  - Pengiriman email diikat pada callback `DB::afterCommit(...)` sehingga tidak pernah mengirimkan email jika transaksi basis data gagal atau di-rollback.
  - Kegagalan koneksi mail server ditangani secara anggun (*graceful failure*) dengan logging aman tanpa menggagalkan proses penyimpanan aspirasi.

### 4. Rekapitulasi Laporan & Ekspor CSV Aman
- **Filter Fleksibel:** Filter laporan berdasarkan rentang tanggal (maksimal 5 tahun), kategori aspirasi, dan status verifikasi.
- **Mitigasi CSV Formula Injection (CWE-1236):** Karakter pemicu formula berbahaya (`=`, `+`, `-`, `@`, `\t`, `\r`, `\n`) otomatis diberi prefiks petik tunggal (`'`) agar spreadsheet memperlakukannya murni sebagai teks statis.
- **Privasi Terjamin:** Ekspor CSV mengecualikan identitas pelapor anonim, email, telepon, IP address, hash pelacakan, dan catatan internal.

### 5. Audit Log Sistem
- Pencatatan seluruh riwayat login, logout, modifikasi konten, eskalasi status aspirasi, dan manajemen akun pengelola.
- **Sanitasi Metadata Otomatis:** Menghapus data sensitif seperti password, remember token, CSRF token, kode pelacakan rahasia, dan hash dari muatan log audit.
- Akses audit log dibatasi ketat hanya untuk peran `super_admin`.

### 6. Pengerasan Integritas Konten Publik
- Mengeliminasi ketergantungan *fallback data dummy* setelah migrasi tabel riil diterapkan. Basis data riil menjadi *single source of truth*.
- Artikel, acara, atau program kerja bertatus *draft* terisolasi sepenuhnya dan merespons `HTTP 404` jika diakses langsung melalui slug publik.

---

## ⚙️ Konfigurasi Environment (`.env`)

Tambahkan variabel konfigurasi berikut pada berkas `.env`:

```dotenv
APP_NAME="Portal BEM Terpadu"
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_TIMEZONE="Asia/Jakarta"
APP_URL=http://localhost/portal-bem/public

# Basis Data
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=portal_bem
DB_USERNAME=root
DB_PASSWORD=

# Notifikasi Aspirasi
ADMIN_NOTIFICATION_EMAIL=advokasi@bem.kampus.ac.id

# Konfigurasi Mail Server
MAIL_MAILER=log
# Untuk Mailpit (Pengujian Lokal):
# MAIL_MAILER=smtp
# MAIL_HOST=127.0.0.1
# MAIL_PORT=1025
# MAIL_USERNAME=null
# MAIL_PASSWORD=null
# MAIL_ENCRYPTION=null
# MAIL_FROM_ADDRESS="no-reply@bem.kampus.ac.id"
# MAIL_FROM_NAME="${APP_NAME}"
```

---

## 📦 Panduan Instalasi & Menjalankan Aplikasi

### 1. Kloning dan Dependensi
Pastikan PHP 8.5 dan Composer telah terpasang:
```bash
composer install
npm install
```

### 2. Konfigurasi Basis Data & Migrasi
Jalankan migrasi basis data (tanpa perintah destruktif):
```bash
php artisan migrate
```

Buat akun administrator pertama secara aman melalui perintah interaktif (nama, email, peran, dan kata sandi rahasia dikonfirmasi dua kali):
```bash
php artisan app:create-admin
# atau non-interaktif:
php artisan app:create-admin --name="Nama Lengkap" --email=admin@bem.kampus.ac.id --role=super_admin --password="KataSandiAman8!"
```

### 3. Kompilasi Aset Frontend
Kompilasi styling Tailwind CSS v4 dan JavaScript menggunakan Vite:
```bash
# Untuk mode pengembangan (hot reload):
npm run dev

# Untuk mode produksi:
npm run build
```

### 4. Menjalankan Server Aplikasi
Jalankan server pengembangan Laravel:
```bash
php artisan serve
```
Akses portal melalui peramban web:
- Portal Publik: `http://localhost:8000`
- Dashboard Pengelola: `http://localhost:8000/admin/login`

---

## 🧪 Pengujian Otomatis (Testing)

Proyek ini dilengkapi rangkaian pengujian unit dan fitur komprehensif (107 pengujian, 461 asersi per 31 Oktober 2026) yang mencakup skenario fungsionalitas dan keamanan, termasuk SEO, paginasi daftar publik, privasi aspirasi anonim, ekspor CSV aman, dan sanitasi detail error log.

Jalankan seluruh pengujian dengan perintah:
```bash
php artisan test
```

Untuk menjalankan kelompok pengujian keamanan dan audit secara spesifik:
```bash
php artisan test --filter=Tahap5SecurityAndAuditTest
```

Untuk pengujian regresi SEO, paginasi, dan halaman error (Tahap 6):
```bash
php artisan test --filter=Tahap6SeoAndPerformanceTest
```

---

## 🌐 Persiapan Deployment, Backup, dan Pemulihan

Bagian ini menjelaskan langkah aman menerapkan portal ke server produksi serta menjaga ketersediaan data. **Penting:** cadangan basis data (`database`) dan folder unggahan publik (`storage/app/public`) sama-sama penting — keduanya wajib dicadang bersamaan.

### 1. Persyaratan Server & Versi Dependensi
- PHP 8.5 dengan ekstensi: `pdo`, `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`, `gd`/`imagick` (untuk pemrosesan gambar).
- MariaDB ≥ 10.6 atau MySQL ≥ 8.0.
- Composer, Node.js ≥ 20 (untuk build aset), dan npm.
- Web server: Nginx atau Apache dengan document root mengarah ke direktori `public/`.
- HTTPS (Let's Encrypt) wajib untuk produksi.

### 2. Instalasi Dependensi
```bash
composer install --no-dev --optimize-autoloader
npm install --ignore-scripts
npm run build
```

### 3. Konfigurasi `.env` Produksi
Salin `.env.example` lalu sesuaikan. Gunakan nilai placeholder untuk seluruh rahasia — jangan menyimpan kredensial nyata di repositori.
```dotenv
APP_NAME="Portal BEM Terpadu"
APP_ENV=production
APP_KEY=base64:GENERATED_KEY         # php artisan key:generate
APP_DEBUG=false
APP_TIMEZONE="Asia/Jakarta"
APP_URL=https://portal-bem.kampus.ac.id
APP_LOCALE=id

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=portal_bem
DB_USERNAME=portal_user
DB_PASSWORD=RAHASIA_DATABASE          # jangan di-commit

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
FILESYSTEM_DISK=public

# Email notifikasi aspirasi (SMTP produksi)
MAIL_MAILER=smtp
MAIL_HOST=smtp penyedia email Anda
MAIL_PORT=587
MAIL_USERNAME=no-reply@bem.kampus.ac.id
MAIL_PASSWORD=RAHASIA_MAIL            # jangan di-commit
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="no-reply@bem.kampus.ac.id"
MAIL_FROM_NAME="${APP_NAME}"
ADMIN_NOTIFICATION_EMAIL=advokasi@bem.kampus.ac.id
```
> **Catatan keamanan:** `APP_DEBUG` wajib `false` agar rincian galat tidak bocor ke publik. Set `APP_URL` ke domain final agar URL kanonik, Open Graph, sitemap, dan tautan email mengarah dengan benar.

### 4. Pembuatan Basis Data & Migrasi Aman
Buat basis data kosong lewat panel MariaDB/MySQL, lalu jalankan migrasi **tanpa** menghapus data:
```bash
php artisan key:generate
php artisan migrate --force
```
Jangan pernah menjalankan `migrate:fresh`, `migrate:reset`, `migrate:rollback` tanpa cadangan, maupun `db:wipe` di server produksi. Untuk perubahan skema di masa depan, gunakan migration baru yang kompatibel dengan data yang ada.

### 5. Konfigurasi Mail Notifikasi (tanpa kredensial di repositori)
Seluruh kredensial email hanya boleh berada di berkas `.env` server (yang tidak di-commit). Email notifikasi aspirasi (ke admin saat ada laporan baru, dan ke pelapor saat status berubah) dikirim melalui callback `DB::afterCommit`, sehingga kegagalan pengiriman email tidak membatalkan transaksi penyimpanan aspirasi.

### 6. Konfigurasi Filesystem & Symbolic Link Storage
```bash
php artisan storage:link
```
Pastikan folder `storage/app/public` (berisi gambar berita) dan `storage/framework/{sessions,views,cache}` dapat ditulis oleh web server (`www-data`/`nginx`). Hak akses yang disarankan:
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 7. Konfigurasi Document Root & HTTPS
- Arahkan document root server ke subdirektori `public/`. **Jangan** mengarahkan root ke direktori proyek (akan membocorkan `.env`, `composer.json`, dll).
- Aktifkan HTTPS dan redirect HTTP → HTTPS. Set `APP_URL` menggunakan `https://`.
- `robots.txt` dan `sitemap.xml` dilayani dinamis lewat rute `SeoController` mengikuti `APP_URL`, sehingga keduanya otomatis sesuai domain produksi.

### 8. Optimasi untuk Produksi
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```
Jika mengubah konfigurasi/rute, jalankan `config:clear`/`route:clear` sebelum meng-cache ulang.

### 9. Membuat Akun Administrator Pertama (Aman)
```bash
php artisan app:create-admin
```
Perintah ini meminta nama, email, peran (`super_admin`, `admin`, `advokasi`, `editor`), dan kata sandi rahasia (disembunyikan saat diketik dan dikonfirmasi). Akun super_admin aktif terakhir dilindungi dari penghapusan/penonaktifan.

### 10. Backup Basis Data (MariaDB/MySQL)
Lakukan pencadangan terjadwal (mis. harian via cron):
```bash
mysqldump --single-transaction --routines --triggers -u portal_user -p portal_bem | gzip > /backup/portal_bem_$(date +%F).sql.gz
```
Simpan cadangan terenkripsi di lokasi terpisah dari server sumber (mis. bucket offsite), batasi aksesnya hanya untuk petugas yang berwenang, dan terapkan masa retensi yang ditetapkan organisasi. Jangan menaruh kredensial database di command line atau repositori. Catat checksum dan tanggal backup agar integritas salinan dapat diperiksa.

### 11. Backup File Unggahan
Cadangkan folder unggahan publik (gambar berita) bersamaan dengan basis data:
```bash
tar -czf /backup/storage_public_$(date +%F).tar.gz -C storage/app public
```
Simpan arsip ini di lokasi offsite yang sama-sama dibatasi aksesnya. Cocokkan waktu backup database dan file agar referensi gambar di database sesuai dengan file yang dipulihkan.

### 12. Prosedur Pemulihan Backup
Lakukan pemulihan terlebih dahulu pada lingkungan terpisah dengan database dan direktori unggahan kosong. Jangan arahkan prosedur ini ke database atau storage aktif. Pastikan nama database tujuan secara eksplisit adalah database pemulihan sebelum menjalankan perintah:
```bash
# 1. Siapkan basis data kosong, lalu pulihkan:
gunzip < /backup/portal_bem_TGL.sql.gz | mysql -u portal_user -p portal_bem_restore
# 2. Pulihkan unggahan gambar:
tar -xzf /backup/storage_public_TGL.tar.gz -C /path/to/portal-bem-restore/storage/app
# 3. Pastikan symbolic link storage: php artisan storage:link
# 4. Bersihkan & bangun ulang cache: php artisan optimize:clear && php artisan optimize
```
Setelah pemulihan, verifikasi integritas arsip dengan checksum, pastikan migrasi dan tabel utama tersedia, cocokkan jumlah data dengan catatan operasional backup, buka beberapa gambar yang dipulihkan, dan jalankan smoke test aplikasi pada lingkungan tersebut. Catat hasil, waktu pemulihan, serta setiap selisih sebelum menyatakan cadangan dapat dipakai.

### 13. Langkah Validasi Pasca-Deployment
- Buka `https://portal-bem.kampus.ac.id` → beranda tampil (data demo hilang jika DB sudah terisi).
- Buka `https://portal-bem.kampus.ac.id/sitemap.xml` → XML valid berisi URL publik.
- Buka `https://portal-bem.kampus.ac.id/robots.txt` → memblokir `/admin`, `/aspirasi/kirim`, `/aspirasi/lacak`, `/aspirasi/sukses`.
- Akses `https://portal-bem.kampus.ac.id/admin/login` → login sebagai admin.
- Kirim aspirasi uji coba dan lacak dengan kode rahasia.
- Verifikasi gambar berita termuat melalui `storage` symlink.
- Jalankan `php artisan test` di server staging (bukan produksi) sebelum go-live final.

### 13a. Smoke Test Konfigurasi Produksi pada Staging
Jalankan pemeriksaan ini pada instance staging terpisah, bukan pada aplikasi lokal yang memakai `.env` pengembang dan bukan pada server produksi. Gunakan database kosong khusus staging (mis. `portal_bem_staging`), storage unggahan terpisah, akun sintetis, serta konfigurasi email `array` atau mailbox staging sampai pengiriman nyata disetujui. Jangan menyalin `.env` produksi atau database produksi ke staging.

Pada konfigurasi milik instance staging, pastikan nilainya setara dengan:
```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://staging.example.invalid
```
Setelah perubahan environment staging, bersihkan cache konfigurasi pada instance itu, lalu bangun ulang cache yang diperlukan:
```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```
Jangan menjalankan perintah cache tersebut terhadap instance produksi sebagai bagian dari uji ini. Periksa `php artisan about` tanpa menyalin nilai rahasia; pastikan environment production, debug mati, database staging, dan URL staging. Dari browser staging, verifikasi beranda, login, sitemap, robots, slug 404, penolakan akses role (403), aset/gambar, serta pesan 500 generik menggunakan mekanisme fault injection khusus staging yang telah disetujui. Jangan sengaja membuat error pada produksi. Setelah uji, pulihkan hanya data sintetis yang dibuat untuk staging dan catat hasil tanpa token atau kredensial.

### 14. Prosedur Rollback Aplikasi & Migration
- **Rollback kode:** kembalikan commit sebelumnya (`git checkout <tag-stabil>`) atau pulihkan tarball rilis, lalu `composer install --no-dev --optimize-autoloader`, `npm run build`, `php artisan optimize`.
- **Rollback migration (hati-hati):** hanya jalankan `php artisan migrate:rollback --step=1` bila migration baru benar-benar aman dibatalkan dan telah dicadangkan terlebih dahulu. Hindari rollback terhadap migration yang telah lama berjalan.
- Selalu miliki cadangan basis data terbaru sebelum rollback.

### 15. Pekerjaan Lanjutan (opsional)
- Tambahkan scheduler (cron `* * * * * php artisan schedule:run`) bila mengaktifkan antrian untuk email.
- Pertimbangkan membatasi ukuran gambar unggahan dan menambahkan pembuatan thumbnail untuk optimasi performa.
- Audit log bersifat append-only di tingkat aplikasi; pertimbangkan pembatasan hak tulis di tingkat basis data untuk kepatuhan ketat.
