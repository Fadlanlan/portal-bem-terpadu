# Laporan Tahap 7: Pengujian Akhir dan Audit Kesiapan Rilis

Tanggal pengujian: 30 Oktober 2026  
Proyek: Portal BEM Terpadu  
Keputusan: **Siap untuk verifikasi manual; belum dinyatakan siap produksi.**

## 1. Ringkasan eksekutif

Suite Laravel berjalan aman menggunakan SQLite in-memory dan seluruh 105 test lulus dengan 454 assertions. Build Vite berhasil, 60 route aplikasi berhasil didaftarkan tanpa route vendor, serta seluruh Blade berhasil dikompilasi lalu cache dibersihkan. Pengujian mengonfirmasi alur publik utama, CRUD, role, privasi aspirasi, CSV, audit, gambar, SEO, dan email melalui fake mailer.

Audit dokumentasi menemukan prosedur dump lama menggunakan `--databases`, yang dapat menyertakan `CREATE DATABASE` dan `USE` sumber sehingga nama target saat pemulihan tidak menjamin isolasi. Perintah dump diperbaiki dan pemulihan diarahkan ke database kosong bernama `portal_bem_restore`. Tidak ada restore, koneksi SMTP nyata, browser automation, atau deployment yang dilakukan.

Tidak ada perubahan kode aplikasi atau skema database. Tidak ada perintah migrasi/destruktif, tidak ada akses tulis ke database MariaDB aktif, dan direktori `vendor/` tidak diubah.

## 2. Lingkungan pengujian

- Windows, direktori kerja `C:\xampp\htdocs\portal-bem`.
- PHP CLI 8.5.12; Laravel Framework 13.39.0; PHPUnit 12.5.42; Vite 8.3.5.
- `phpunit.xml` menetapkan `APP_ENV=testing`, `DB_CONNECTION=sqlite`, `DB_DATABASE=:memory:`, `MAIL_MAILER=array`, cache `array`, serta session `array`.
- Test yang membutuhkan database memakai `RefreshDatabase`; database pengujian tidak menunjuk ke MariaDB `portal_bem`.
- `php artisan about` menunjukkan runtime aplikasi lokal memakai driver MySQL, tetapi suite dipastikan memakai konfigurasi SQLite terisolasi. Nilai rahasia tidak dibaca atau dicantumkan.
- `git status` tidak dapat dijalankan karena direktori ini bukan repository Git.
- Tidak ditemukan browser automation atau tool browser dalam sesi ini.

## 3. Hasil baseline dan regresi akhir

| Pemeriksaan | Baseline | Akhir | Hasil |
|---|---:|---:|---|
| `php artisan test` | 104 lulus, 446 assertions | 105 lulus, 454 assertions | Lulus; 0 gagal, 0 error, 0 skipped dilaporkan |
| `cmd /c npm run build` | Exit code 0 | Exit code 0 | Lulus |
| `php artisan route:list` | Exit code 0, 64 route termasuk vendor | `--except-vendor`: exit code 0, 60 route | Lulus |
| `php artisan view:cache` | Exit code 0 | Exit code 0 | Lulus |
| `php artisan view:clear` | Exit code 0 | Exit code 0 | Lulus |
| `php artisan about` | Exit code 0 | Tidak diulang setelah dokumentasi | Informasi lingkungan tercatat |

Output test akhir: `passed`, 105 tests, 454 assertions, durasi sekitar 18.2 detik. Baseline awal Tahap 7 menghasilkan 104 tests dan 446 assertions. Angka baseline Tahap 6 diverifikasi ulang dengan menjalankan suite; bukan disalin sebagai hasil baru.

## 4. Skenario dan hasil aktual

### Halaman publik, konten, dan SEO

**Berhasil diverifikasi otomatis:** Beranda, berita, program kerja, agenda, organisasi, informasi aspirasi, formulir pengiriman dan pelacakan, FAQ, kontak, tentang, sitemap XML, dan robots.txt diuji melalui Feature Test dan/atau route test yang tersedia. Berita mendukung pencarian, kategori, pagination, serta mempertahankan filter kategori; halaman program memiliki filter/pagination. Detail slug tak dikenal dan konten draft menghasilkan 404. Empty state berita dan agenda diuji.

Metadata canonical, Open Graph, Twitter card, robots, sitemap yang mengecualikan draft/admin, dan `robots.txt` diuji. `robots.txt` diperlakukan sebagai petunjuk crawler; kontrol akses admin tetap melalui middleware dan diuji terpisah.

### Berita dan gambar

**Berhasil diverifikasi otomatis:** File gambar valid, tipe non-gambar ditolak, file di atas batas 2 MB ditolak, path relatif disimpan, penggantian gambar menangani gambar lama, gambar yang masih dipakai artikel lain tidak terhapus, serta placeholder dipakai untuk gambar kosong/hilang. Gambar publik dan atribut alt diuji. Draft tidak dapat diakses publik dan role tanpa izin ditolak.

Test memakai `Storage::fake`; tidak bergantung pada ekstensi GD dan tidak menghapus unggahan nyata. Nama file unik/path traversal tidak diuji sebagai kasus terpisah pada seluruh jalur.

### Aspirasi dan privasi

**Berhasil diverifikasi otomatis:** Pengiriman valid, anonim, validasi field wajib/kategori/consent, tiket unik, riwayat status awal, dan penolakan manipulasi status/respons diuji. Kode pelacakan disimpan dalam bentuk hash dan verifikasi memakai kode rahasia; kode salah atau tiket fiktif memberikan pesan generik. Tebakan tiket saja tidak memberi akses. Rate limit pengiriman dan pelacakan diuji. Halaman hasil pelacakan tidak menampilkan identitas pelapor.

Email admin baru tidak membawa kode pelacakan. Notifikasi status/respons hanya ditujukan kepada pelapor yang memiliki email dan tidak anonim. Pengujian anonim memastikan identitas tidak disimpan. Dashboard dan CSV juga memiliki test privasi.

**Belum diverifikasi:** Ketahanan terhadap kegagalan DB saat pencatatan audit, semua keluaran log pada setiap skenario produksi, serta isolasi pengguna antar seluruh kombinasi parameter diuji melalui pengujian manual/penetrasi khusus.

### Login, RBAC, modul admin, dan audit

**Berhasil diverifikasi otomatis:** Login benar/salah, akun nonaktif, logout, rate limit login, pengalihan guest, dan role `super_admin`, `admin`, `advokasi`, `editor` diuji melalui request langsung ke server. Modul konten, aspirasi, dan pengguna diuji sesuai izin. Penghapusan/penonaktifan super admin aktif terakhir serta perlindungan diri sendiri diuji. Siklus CRUD berita, program kerja, agenda, dan organisasi diuji; manajemen pengguna dan reset password memiliki test.

Login, logout, CRUD tertentu, pembaruan status, sanitasi metadata audit, pembatasan tampilan audit log, dan proteksi audit log diuji. Perubahan ID sesi setelah login tampak diterapkan di controller, tetapi tidak memiliki assertion khusus. CSRF middleware tidak diuji secara nyata oleh Feature Test standar karena test harness Laravel umumnya menonaktifkan middleware CSRF; perlu verifikasi melalui browser/staging.

### Laporan, CSV, dan email

**Berhasil diverifikasi otomatis:** Filter tanggal/kategori/status, validasi rentang tanggal, ekspor CSV, header MIME, mitigasi formula injection untuk semua prefix yang diminta (`=`, `+`, `-`, `@`, tab, CR, LF), dan pengecualian data privat diuji. Audit log mencatat aksi utama dan memfilter metadata sensitif.

Fake mailer memastikan mailable yang diharapkan dipicu dan penerima/konten yang diuji sesuai kebijakan. Pembacaan kode mengonfirmasi email dijadwalkan melalui `DB::afterCommit` dan kegagalan email ditangkap. Tidak ada SMTP nyata yang dikonfigurasi atau digunakan; keberhasilan fake mailer bukan bukti email sampai ke penerima.

### Error, performa, dan aksesibilitas

**Berhasil diverifikasi otomatis:** View 404 dan 500 dirender melalui Feature Test; penolakan role menghasilkan HTTP 403. View 403 tersedia, tetapi konten view itu tidak diuji tersendiri. Test pagination dan penggunaan eager loading/pagination ada.

**Belum dilakukan:** Browser automation pada viewport 320×720, 375×812, 768×1024, 1024×768, dan 1440×900; pemeriksaan overflow, interaksi menu, keyboard/focus, screen reader, serta audit visual. Tidak dilakukan benchmark beban atau profiler untuk mengukur N+1 di MariaDB.

## 5. Keamanan dan privasi

- Pengujian database terisolasi dan mailer fake mencegah perubahan data aktif serta email nyata.
- Akses admin dan batas role diuji server-side, termasuk request langsung.
- Pelacakan mengharuskan nomor tiket dan kode rahasia; hash mentah tidak diterima. Identitas anonim disimpan NULL dan tidak muncul pada halaman pelacakan/CSV yang diuji.
- CSV diuji terhadap karakter formula berbahaya (`=`, `+`, `-`, `@`, tab, CR, LF) melalui regression test yang mengekspor payload sintetis.
- Sitemap tidak mencantumkan admin/draft; robots tidak dijadikan kontrol autentikasi.
- Error log aplikasi berisi 12 entri ERROR dan 1 WARNING saat diperiksa. Isi tidak ditampilkan atau disalin ke artefak karena mungkin memuat data sensitif; entri tersebut tidak dapat diatribusikan ke pengujian ini tanpa analisis terkontrol. Tindak lanjut: tinjau log oleh operator berwenang melalui kanal aman.
- Halaman 500 diuji untuk render konsisten, tetapi pemeriksaan seluruh variasi kebocoran stack trace/rahasia memerlukan smoke test server dengan `APP_DEBUG=false`.

## 6. Backup dan pemulihan

Dokumentasi di `README.md` ditinjau dan dilengkapi dengan backup database MariaDB/MySQL, unggahan `storage/app/public`, lokasi offsite, pembatasan akses, checksum, pemulihan ke DB terpisah, pemulihan file, dan validasi pascapemulihan. Perintah dump kini tidak memakai `--databases`, sehingga SQL hasil dump tidak memaksa nama database sumber saat diarahkan ke `portal_bem_restore`.

**Ditinjau, belum diuji langsung:** Tidak ada lingkungan pemulihan terpisah dan arsip backup aktual yang disediakan. Tidak ada restore dijalankan. Prosedur harus diuji oleh operator pada lingkungan kosong sebelum mengandalkan backup operasional.

## 7. Temuan dan perubahan

| Risiko | Temuan | Reproduksi aman | Status akhir |
|---|---|---|---|
| High (diperbaiki) | Instruksi backup lama memakai `mysqldump --databases portal_bem`; dump tersebut dapat memuat `CREATE DATABASE`/`USE` sumber sehingga target restore terpisah tidak efektif dan berisiko menyentuh DB sumber bila dipakai tanpa isolasi. | Ditinjau teks perintah saja; tidak menjalankan dump atau restore. | README diperbaiki: dump tanpa `--databases`, pemulihan diarahkan ke database kosong `portal_bem_restore`, serta validasi isolasi/integritas ditambahkan. Tidak ada database aktif disentuh. |
| Medium (diperbaiki) | CSV sanitizer memakai `trim()`, yang membuang tab/CR/LF sebelum pemeriksaan prefix; sel yang dimulai karakter tersebut tidak menerima awalan pelindung. | Regression test memakai aspirasi sintetis dengan seluruh prefix dan mengekspor CSV di SQLite in-memory. | Pemeriksaan kini mengabaikan spasi biasa saja dan tetap mendeteksi tab/CR/LF. Test regresi ditambahkan dan suite penuh lulus. |
| Informational | Browser, viewport, aksesibilitas visual, SMTP nyata, dan uji restore langsung tidak tersedia dalam sesi. | Checklist manual pada bagian berikut. | Belum diverifikasi; harus diselesaikan sebelum keputusan rilis final. |
| Informational | Log lokal memiliki entri ERROR/WARNING yang isi dan penyebabnya tidak dapat ditelaah aman dalam laporan ini. | Tinjau melalui akses operator yang berwenang, tanpa menyalin rahasia ke laporan. | Tindak lanjut operasional. |

File yang diubah:

- `README.md` — memperjelas penyimpanan backup dan pemulihan aman; memperbaiki bentuk perintah dump agar restore dapat diisolasi.
- `app/Http/Controllers/Admin/ReportAdminController.php` — mempertahankan prefix karakter kontrol saat sanitasi CSV.
- `tests/Feature/Tahap5SecurityAndAuditTest.php` — regression test semua prefix formula injection.
- `docs/TAHAP-7-LAPORAN-PENGUJIAN.md` — laporan hasil dan keterbatasan Tahap 7.
- `public/build/manifest.json` dan `public/build/assets/*` — artefak yang dihasilkan ulang oleh build Vite.

Regression test ditambahkan untuk temuan CSV; perbaikan backup adalah dokumentasi sehingga tidak membutuhkan test aplikasi. Suite penuh dijalankan ulang setelah perubahan kode dan dokumentasi, dan tetap lulus.

## 8. Belum dilakukan dan checklist manual

**Belum dilakukan:** Pengujian browser/viewport, pengiriman SMTP nyata, uji restore aktual, CSRF di browser, smoke test konfigurasi produksi `APP_DEBUG=false`, dan profiling performa/N+1 pada MariaDB. Tidak ada deployment atau pengujian layanan eksternal.

Checklist operator sebelum rilis:

- [ ] Pada staging dengan data sintetis, buka seluruh halaman publik dan tautan navigasi; periksa slug draft, pagination/filter, gambar, serta sitemap/robots.
- [ ] Periksa form aspirasi, anonymous/non-anonymous, tracking salah/benar, rate limit, dan otorisasi semua role; jangan gunakan data produksi.
- [ ] Periksa login/logout, session ID sesudah login, status nonaktif, CSRF melalui browser, serta tampilan 403/404/500 dengan debug mati.
- [ ] Uji CSV di aplikasi spreadsheet dengan payload sintetis dan pastikan tidak ada identitas/kode privat.
- [ ] Uji tampilan dan keyboard pada lima viewport yang tercantum; periksa label, alt, focus, menu mobile, tabel, dan FAQ.
- [ ] Kirim email hanya ke mailbox staging yang disetujui; verifikasi penerima dan isi tanpa tracking code maupun identitas anonim.
- [ ] Pulihkan backup DB dan `storage/app/public` ke lingkungan kosong terpisah, verifikasi checksum, tabel/data, gambar, dan smoke test; jangan menunjuk ke DB aktif.
- [ ] Tinjau log ERROR/WARNING melalui kanal aman dan selesaikan penyebab yang relevan.

## 9. Keputusan rilis

Hasil otomatis stabil dan tidak menunjukkan kegagalan test/build. Perbaikan risiko tinggi pada prosedur backup telah dilakukan. Status akhir adalah **siap untuk verifikasi manual**. Rilis produksi belum direkomendasikan sampai checklist staging, khususnya CSRF/browser, SMTP staging, konfigurasi debug, dan restore terisolasi, dijalankan serta disetujui operator.
