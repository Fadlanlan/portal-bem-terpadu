# Tahap 10 — Laporan Pengujian dan Audit Lokal

Tanggal: 31 Oktober 2026 (Asia/Bangkok)  
Workspace: `C:\xampp\htdocs\portal-bem`

## Ringkasan eksekutif

Pemeriksaan otomatis lokal lulus: **107 test, 461 assertions**, 60 route, 11 migration `Ran` dibaca tanpa migration, build frontend berhasil, dan kompilasi Blade berhasil. HTTP smoke test lokal menghasilkan 200 untuk halaman publik/form dan admin login, 404 untuk tiga slug fiktif, metadata dasar hadir, serta sitemap/robots merespons sebagaimana diuji.

Checklist keseluruhan: **19 PASS, 1 FAIL, 14 BLOCKED, 2 NOT RUN**. FAIL adalah risiko privasi dari isi bebas aspirasi yang disertakan dalam CSV; tidak ada data nyata/sintetis tersimpan yang digunakan untuk menguji atau membuktikan re-identifikasi. Browser berhasil tidak dijalankan: Chrome terpasang, tetapi proses headless tidak menghasilkan DOM karena akses profil dan sandbox. Login/RBAC, submit aspirasi, CSRF POST, perubahan konten, SMTP, serta backup/restore tidak dilakukan.

Keputusan: **hasil lokal otomatis lulus, tetapi audit menyeluruh belum lulus dan belum boleh dianggap siap staging/deployment**. Tidak ada kode, `.env`, database, akun, file upload, log, dependency, atau vendor yang diubah. Artefak Vite dan cache view lokal dibuat ulang oleh command yang diminta.

## 1. Lingkungan dan isolasi

- PHP 8.5.12; Composer 2.11.0; Laravel Framework 13.39.0; PHPUnit 12.5.42; Node 26.13.0; npm 11.20.0; Vite 8.3.5.
- Binary server XAMPP melaporkan MariaDB 10.11.16. `migrate:status` berhasil membaca DB lokal; query SQL versi melalui Tinker tidak berhasil dijalankan karena PsySH menolak penulisan history ke profile, sehingga versi server koneksi tidak dikonfirmasi lewat SQL.
- PHP CLI memiliki PDO MySQL/SQLite dan extension inti untuk Laravel. GD/Imagick tidak ditemukan di CLI.
- `phpunit.xml` memakai `APP_ENV=testing`, SQLite `:memory:`, DB URL kosong, mailer/cache/session `array`. Tidak ada override proses yang relevan atau config cache ketika suite mulai.
- Environment lokal nonrahasia: `APP_ENV=local`, `APP_DEBUG=true`, default database `mysql`, mailer `log`, filesystem default `local`, session/cache `database`, `APP_URL=http://localhost:8000`. Nilai credential tidak dibaca; `.env` tidak diubah.
- `public/storage` adalah junction ke `storage/app/public` lokal. Manifest Vite tersedia.
- Tidak ada listener web pada URL configured saat pemeriksaan awal. Saya menjalankan `php artisan serve` sementara pada `http://localhost:8000` dengan override proses saja: session/cache/mail `array`, queue `sync`; aplikasi tetap local dan DB default tetap MySQL. Hanya GET dilakukan. Server dihentikan setelah pemeriksaan. Tidak ada form/POST, login, upload, email, atau mutasi database yang dilakukan.
- Chrome terpasang pada workstation, tetapi tidak dapat digunakan sebagai bukti browser dalam sesi ini: headless output kosong dan akses profil ditolak; percobaan proses terisolasi ditolak oleh sandbox. HTTP GET di bawah **bukan** browser test.
- `.git` tidak tersedia pada direktori proyek; source/artifact diff tidak dapat dilakukan. Laporan Tahap 8/9 tetap tidak diubah.

## 2. Perintah dan hasil aktual

| Perintah/pemeriksaan | Hasil |
|---|---|
| `php --version`, `php -m`, `composer --version`, `composer show laravel/framework`, `composer show phpunit/phpunit`, Node/npm/Vite version | Exit 0; versi dan modul dicatat di atas |
| `C:\xampp\mysql\bin\mysqld.exe --version` | Exit 0; MariaDB 10.11.16 binary |
| Baca `phpunit.xml`, process override, config nonrahasia, manifest, symlink | Exit 0; hasil dicatat; tidak membuka `.env`/credential |
| `php artisan test --compact` | Exit 0; 107 lulus, 461 assertions; 0 failure/error/skipped dilaporkan; 20.309 ms |
| `php artisan route:list --except-vendor -v` | Exit 0; 60 route dan middleware tercetak |
| `php artisan migrate:status --no-interaction` | Exit 0; 11 migration `Ran`; read-only |
| `cmd /c npm run build` | Exit 0; Vite 8.3.5, 2.72 s |
| `php artisan view:cache` | Exit 0; semua Blade berhasil di-cache |
| GET HTTP ke 11 halaman publik/form dan `/admin/login` | HTTP 200; bukan Chrome/browser |
| GET HTTP ke tiga slug fiktif | HTTP 404 |
| GET `/sitemap.xml`, `/robots.txt` | HTTP 200; pemeriksaan boolean sitemap/robots lulus untuk pola yang disebut checklist |
| HEAD asset CSS/JS dan placeholder | HTTP 200; satu image URL lokal yang terambil dari markup juga memberi 0 kegagalan HEAD |
| Chrome headless | Tidak menghasilkan DOM; akses profile ditolak. Proses terisolasi diblokir policy sandbox; tidak ada screenshot/console bukti |
| `php artisan tinker --execute 'dump(DB::selectOne("SELECT VERSION() AS server_version")->server_version);'` | Exit 1; PsySH history path tidak dapat ditulis, query tidak dikonfirmasi berjalan |
| Probe `Invoke-WebRequest -SkipHttpErrorCheck` | Parameter tidak tersedia pada PowerShell ini; probe diperbaiki dan status HTTP diverifikasi melalui request berikutnya |
| Penghentian server sementara dengan Ctrl+C | Server dihentikan; command session exit 1 karena interrupt, bukan error aplikasi |

Build menulis ulang `public/build/manifest.json` dan menghasilkan CSS `app-UhZswmPq.css` serta JS `app-BwqZ1Mth.js`. Hash CSS berbeda dari build yang pernah tercatat sebelumnya. Source CSS memiliki waktu modifikasi sebelum sesi ini; tanpa Git tidak dapat membandingkan perubahan source/artefak. `view:cache` juga menulis file cache Blade. Tidak ada source aplikasi yang diedit.

## 3. Hasil HTTP lokal

HTTP GET read-only ke `/`, `/berita`, `/program-kerja`, `/agenda`, `/organisasi`, `/tentang`, `/kontak`, `/faq`, `/aspirasi`, `/aspirasi/kirim`, `/aspirasi/lacak`, dan `/admin/login` memberi **200**. Halaman tersebut memiliki title HTML; 11 halaman publik/form yang diperiksa juga memiliki description, canonical, Open Graph title, dan Twitter card tag.

Tiga slug berita/program/agenda fiktif memberi **404**. `/sitemap.xml` dan `/robots.txt` masing-masing **200**. Sitemap tidak memuat `/admin`, `/aspirasi/kirim`, atau `/aspirasi/lacak`; robots memberi Disallow untuk path tersebut dan tidak dianggap access control. Asset CSS/JS dan placeholder SVG memberi **200**. Satu image URL lokal yang ditemukan di markup memberi response HEAD tanpa kegagalan.

Ini membuktikan respons HTTP Laravel lokal pada saat itu, bukan render Chrome, navigasi keyboard, layout visual, image natural dimensions, console browser, atau network waterfall. Saya tidak mengakses page detail berisi data privat dan tidak menyalin body response ke laporan.

## 4. Keamanan, privasi, dan temuan

### Bukti source dan otomatis

- Route admin memakai `admin.auth` dan `admin.role`; submit/login/aspiration routes punya throttle. Middleware login memeriksa akun aktif/role; source meregenerasi session saat login dan menginvalidasi saat logout.
- Form POST/PUT/DELETE yang dipindai menyertakan `@csrf`. Tidak ditemukan Blade raw echo `{!!` pada view aplikasi yang dipindai. Ini inspeksi kode; CSRF/XSS belum diverifikasi melalui browser/runtime payload.
- Filter/search memakai Eloquent/query builder dengan parameter binding. `selectRaw` yang ditemukan pada report berisi agregasi statis; tidak ditemukan raw query dinamis/unprepared pada scan.
- Upload berita memvalidasi tipe image/JPEG/PNG/WebP dan maksimum 2 MB. Suite otomatis mencakup file salah, ukuran, placeholder, dan perlindungan shared image; upload aktual tidak dicoba.
- Validasi aspirasi, hash tracking, null identity saat anonim, rate limiter, email after-commit, CSV formula neutralization, dan audit sanitization memiliki source/test coverage di suite lulus. Tidak ada data aspirasi dikirim.

### Temuan

| ID | Prioritas | Temuan dan bukti | Status/tindak lanjut |
|---|---|---|---|
| SEC-10-01 | P2 — privasi CSV | Export mengecualikan kolom nama/email/NIM/IP/hash/catatan internal dan menangani formula injection, tetapi memuat ticket, subjek, isi aspirasi lengkap, dan respons admin. Teks bebas dapat memuat identitas yang ditulis sendiri oleh pelapor anonim. Ini risiko desain dari source, bukan kebocoran yang diuji pada record aktual. | Checklist FAIL. Minta pemilik privasi menentukan kebijakan export/redaksi dan batas akses sebelum memakai CSV di luar tim berwenang. |
| SEC-10-02 | P3 — minimisasi/retensi audit | `AdminAuditLog` menyimpan IP address dan user-agent; sebagian deskripsi/metadata aksi user mengandung nama/email pengelola. UI dibatasi ke super admin dan key secret/message disaring, tetapi retensi/minimisasi tidak diaudit. | BLOCKED untuk penilaian policy runtime. Pemilik keamanan/privasi perlu menetapkan retensi dan kebutuhan field. |
| SEC-10-03 | Informasi | Local `APP_DEBUG=true`, session/cache default database dan filesystem local. Ini konfigurasi developer lokal, bukan mode aman untuk host terbuka. | Tidak diubah. Jangan expose XAMPP; sebelum hosting gunakan environment terpisah, HTTPS dan `APP_DEBUG=false`. |

Tidak ada temuan SQL injection atau unescaped Blade output dari scan terbatas ini. Ini bukan penetration test dan tidak menghilangkan kebutuhan uji runtime.

## 5. Pengujian tertunda

**BLOCKED:** Chrome/browser visual, lima viewport, keyboard/focus/overflow/console, akun/admin login/logout/session, role request langsung, akun nonaktif, super admin guard runtime, CRUD konten, upload aktual, submit aspirasi, tracking/rate limit, export CSV/audit role, CSRF invalid POST, 403 dan 500 dengan debug mati, SMTP, backup/checksum, dan restore terisolasi.

Alasan utama: browser headless dibatasi sandbox, akun uji belum tersedia, session/cache default lokal memakai database, local DB merupakan data utama, tidak ada target DB/storage test, tidak ada persetujuan email, dan tidak ada arsip/tujuan restore.

**NOT RUN:** isi log aplikasi tidak dibaca (tidak ada insiden yang meminta korelasi; log sensitif dipertahankan); query Tinker untuk versi server gagal sebelum hasil SQL diperoleh. Log tidak dihapus atau dikosongkan.

## 6. File dan perubahan

Dokumen Tahap 10 dibuat:

- `docs/TAHAP-10-LAPORAN.md`
- `docs/TAHAP-10-CHECKLIST-LOKAL.md`
- `docs/TAHAP-10-RENCANA-PERBAIKAN.md`

Tidak ada source/test/dependency/config/`.env`/vendor/database/upload/log yang diubah. Build menghasilkan ulang public build assets dan view cache seperti dilaporkan di atas. Tidak ada Git untuk membandingkan file.

## 7. Rekomendasi sebelum staging/deployment

1. Putuskan kebijakan data bebas CSV (SEC-10-01) dan masa retensi IP/user-agent audit (SEC-10-02); tambahkan regression tests jika implementasi berubah.
2. Sediakan DB dan storage test baru berisi data sintetis saja, akun uji berizin, mailer `array`, session/cache terisolasi. Jangan gunakan database lokal utama untuk POST atau upload.
3. Jalankan browser pada desktop/runner berizin; catat viewport, console dan bukti nonrahasia. Uji 403/500 hanya dengan metode lokal/isolated yang disetujui.
4. Jalankan SMTP hanya setelah persetujuan eksplisit serta mailbox test dikonfirmasi.
5. Latih backup/restore pada DB dan direktori yang baru, setelah arsip/checksum dan target dibuktikan. Jangan reset/restore database utama.
6. Sebelum host apa pun, terapkan HTTPS, `APP_DEBUG=false`, permission minimum, secret management, backup dan monitoring. Audit lokal ini bukan deployment approval.
