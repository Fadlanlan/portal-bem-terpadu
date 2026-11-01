# TAHAP 12A — LAPORAN REKAYASA & PERBAIKAN
## Resolusi Error cURL Subproses PHP CLI pada Pemrosesan & Pratinjau Logo

Tanggal: 31 Oktober 2026  
Workspace: `C:\xampp\htdocs\portal-bem`  
Penyusun: Senior Laravel Developer & Image Processing Engineer  
Status: **SELESAI DENGAN STATUS ALL PASS (164 Tests, 679 Assertions, 0 Failures)**

---

## 1. Akar Masalah yang Terverifikasi

Berdasarkan audit komparatif mendalam terhadap lingkungan XAMPP Windows, arsitektur proses, dan konfigurasi dynamic library, ditemukan 4 faktor utama yang memicu kegagalan subproses:

1. **Ambiguitas Resolusi Binary PHP (`PHP_BINARY`) pada Lingkungan Web Server:**
   - Pada `LogoProcessingService.php` sebelumnya, subproses memanggil `$phpBinary = PHP_BINARY;`.
   - Ketika aplikasi berjalan di bawah Apache (`mod_php` / `php8apache2_4.dll`), konstanta `PHP_BINARY` menunjuk ke executable server web induk (`C:\xampp\apache\bin\httpd.exe`) atau kosong, bukan executable PHP CLI (`C:\xampp\php\php.exe`). Akibatnya, perintah Artisan yang dilewatkan tidak dieksekusi oleh interpreter CLI yang valid.
2. **Ketidakcocokan Versi Binary OpenSSL Antara Apache dan PHP:**
   - Direktori `C:\xampp\apache\bin\` memuat pustaka OpenSSL versi lama (Juli 2025: OpenSSL 3.5.1, `libcrypto-3-x64.dll` berukuran 7.175.168 byte).
   - Direktori `C:\xampp\php\` memuat pustaka OpenSSL versi yang cocok dengan PHP 8.5.12 (Oktober 2026: OpenSSL 3.5.9, `libcrypto-3-x64.dll` berukuran 7.044.608 byte).
   - Ketika subproses dijalankan tanpa pengkondisian variabel lingkungan `PATH`, Windows DLL search order memuat pustaka OpenSSL 3.5.1 dari `apache\bin`. Saat ekstensi `php_curl.dll` memanggil `curl_global_init(CURL_GLOBAL_DEFAULT)`, inisialisasi SSL gagal karena ketidaksesuaian ABI struktur internal, sehingga PHP memicu fatal error:  
     `PHP Fatal error: Unable to start curl module in Unknown on line 0`.
3. **Absensi Argumen Konfigurasi Eksplisit (`-c`) & Path `PHPRC`:**
   - Subproses sebelumnya dijalankan tanpa flag `-c`, sehingga mengandalkan variabel `PHPRC`. Pada konfigurasi XAMPP Apache, `PHPRC` disetel ke `\xampp\php` (path relatif tanpa drive letter), yang dapat gagal diselesaikan saat direktori kerja berbeda.
4. **Sintaksis Pemuatan Ekstensi GD:**
   - Argumen sebelumnya `-dextension=C:\xampp\php\ext\php_gd.dll` memotong konfigurasi `extension_dir`. Pendekatan yang benar dan sesuai standar PHP adalah menentukan `-dextension_dir=C:\xampp\php\ext` bersama `-dextension=gd` tanpa memuat cURL dua kali (karena cURL sudah aktif secara alami pada `php.ini`).
5. **Kelemahan Deteksi Tepi pada Algoritma Feathering:**
   - Logika anti-aliasing sebelumnya memeriksa kesamaan warna dengan background tanpa memastikan bahwa piksel bertetangga langsung dengan piksel background luar (`isBackgroundPixel`). Akibatnya, piksel putih di dalam lambang sempat ikut ter-feather. Hal ini telah disempurnakan dengan pemeriksaan *4-neighbor adjacency*.

---

## 2. Perintah Subproses Sebelum dan Sesudah Perbaikan

### Sebelum Perbaikan:
```text
[PHP_BINARY] -dextension=C:\xampp\php\ext\php_gd.dll C:\xampp\htdocs\portal-bem\artisan logo:process-image [input] [output] --tolerance=28 --padding=16 --feather=2
```
*Keterangan:* `PHP_BINARY` berisiko mengarah ke `httpd.exe` pada runtime Apache, path DLL di-hardcode tanpa `-c php.ini`, `PATH` lingkungan tidak mengisolasi pustaka `C:\xampp\php`, dan tidak ada pembatasan waktu tunggu (*timeout*).

### Sesudah Perbaikan:
```text
C:\xampp\php\php.exe -c C:\xampp\php\php.ini -dextension_dir=C:\xampp\php\ext -dextension=gd C:\xampp\htdocs\portal-bem\artisan logo:process-image [input] [output] --tolerance=28 --padding=16 --feather=2
```
*Variabel Lingkungan Tambahan (Subprocess Environment):*
- `PATH`: `C:\xampp\php;` diutamakan di depan variabel `PATH` sistem agar DLL OpenSSL 3.5.9 yang cocok dimuat terlebih dahulu.
- `SystemRoot` & `WINDIR`: Dipelihara secara aman dari sistem operasi.
- `timeout`: Dibatasi secara aman pada 30 detik (`->timeout(30)`).
- Penanganan Kesalahan: Output stderr dicatat ke file log internal Laravel (`Log::error`), sementara pengguna hanya menerima pesan kesalahan yang aman dan bersih tanpa stack trace internal.

---

## 3. Berkas yang Diperbarui dan Dibuat

1. **`app/Services/LogoProcessingService.php`:**
   - Menambahkan metode `resolvePhpBinary()`: Mengutamakan `C:\xampp\php\php.exe` jika ada, memverifikasi `PHP_BINARY` berakhiran `php.exe`, atau fallback ke `'php'`.
   - Menambahkan metode `resolvePhpIni()`: Memverifikasi `php_ini_loaded_file()` atau `C:\xampp\php\php.ini`.
   - Memperbaiki `executeViaCli()`: Menyertakan argumen `-c`, `-dextension_dir`, `-dextension=gd`, isolasi `PATH`, timeout 30s, dan pencatatan audit log kegagalan.
   - Memperbaiki algoritma feathering (`executeProcessing`): Memeriksa *4-neighbor adjacency* terhadap `isBackgroundPixel` agar elemen putih internal lambang tidak ikut terpengaruh feathering.
2. **`app/Http/Controllers/Admin/SettingAdminController.php`:**
   - Menambahkan logging diagnostik terstruktur menggunakan `Log::error(...)` pada blok penanganan eksepsi `processLogoPreview` dan `updateLogo`.
   - Memastikan pesan kesalahan ramah pengguna dan tidak menampilkan jejak internal/path server ke antarmuka Blade.
3. **`tests/Feature/Tahap12aSubprocessLogoTest.php`:**
   - Berkas pengujian fitur baru dengan 5 skenario komprehensif menguji subprocess nyata, ketersediaan cURL & GD, analisis citra sintetis programatis, penanganan gambar rusak, dan pembersihan file sementara.
4. **`docs/TAHAP-12A-LAPORAN.md`:**
   - Dokumen laporan rekayasa dan perbaikan teknis ini.

---

## 4. Hasil Pemeriksaan PHP dan Ekstensi

Pemeriksaan baris perintah pada lingkungan XAMPP:

| Pemeriksaan | Hasil Diagnostik | Status |
|---|---|:---:|
| `PHP_BINARY` | `C:\xampp\php\php.exe` | Valid |
| `php --ini` | Loaded Configuration File: `C:\xampp\php\php.ini` | Valid |
| `php -m` (Default) | Memuat cURL, OpenSSL, Fileinfo, Exif, PDO, MBString, OPcache (GD tidak aktif default) | Normal |
| `php --ri curl` | cURL 8.23.0, OpenSSL/3.5.9, libSSH2/1.11.1, CA Bundle terpasang | Aktif & Sehat |
| `php --ri gd` (Default) | `Extension 'gd' not present.` | Sesuai Baseline |
| `php -c ... -dextension=gd -m` | **Keduanya aktif bersamaan: `curl` dan `gd` terdaftar di [PHP Modules]** | **SEMPURNA (Exit Code 0, Stderr Kosong)** |
| `php -c ... -dextension=gd --ri gd` | GD 2.1.0, FreeType 2.15.0, JPEG, PNG 1.6.59, WebP enabled | Aktif & Lengkap |

---

## 5. Bukti Pemrosesan Gambar Nyata

Pengujian dilakukan secara programatis menggunakan citra sintetis 100x100 piksel dengan komposisi:
- **Latar Belakang:** Putih `#FFFFFF` (RGB 255, 255, 255).
- **Foreground Luar:** Persegi Biru Solid (RGB 0, 0, 255) dari koordinat (20,20) hingga (80,80).
- **Foreground Dalam:** Lingkaran Putih Solid (RGB 255, 255, 255) di tengah (50,50).

**Hasil Pemrosesan Subproses:**
- **Dimensi Citra:** Dipangkas dari 100x100 px menjadi 93x93 px (sesuai bounding box 61x61 px + padding 2 x 16 px).
- **Piksel Sudut (0,0):** Alpha channel bernilai **127 (Transparan Penuh)**. Background putih luar berhasil dibersihkan.
- **Piksel Foreground Biru (20,20):** Alpha channel bernilai **0 (Solid Opaque)**, warna biru murni RGB(0,0,255) terjaga utuh.
- **Piksel Putih Internal (Tengah):** Alpha channel bernilai **0 (Solid Opaque)**, warna putih RGB(255,255,255) terlindungi utuh dan tidak terhapus.
- **Exit Code Subproses:** `0` (sukses tanpa warning cURL atau startup error).

---

## 6. Hasil Pengujian Otomatis & Kompilasi

### Matriks Pengujian PHPUnit:
| Suite Pengujian | File Uji | Jumlah Tes | Status |
|---|---|:---:|:---:|
| Manajemen Admin Umum & RBAC | `tests/Feature/AdminTest.php` | 27 | PASS |
| Siklus & Penanganan Gambar Berita | `tests/Feature/ArticleImageTest.php` | 13 | PASS |
| Sistem Pengiriman & Lacak Aspirasi | `tests/Feature/AspirationTest.php` | 19 | PASS |
| Halaman Publik & SEO | `tests/Feature/PublicPagesTest.php` | 8 | PASS |
| Audit Keamanan & Privasi CSV (Tahap 5) | `tests/Feature/Tahap5SecurityAndAuditTest.php` | 19 | PASS |
| Sitemap, Robots, & Cache (Tahap 6) | `tests/Feature/Tahap6SeoAndPerformanceTest.php` | 21 | PASS |
| Kustomisasi Tema & Logo (Tahap 11) | `tests/Feature/Tahap11ThemeAndLogoTest.php` | 12 | PASS |
| Pengurus BEM & Hapus Ormawa (Tahap 11) | `tests/Feature/Tahap11BemMemberAndOrganizationDeleteTest.php` | 13 | PASS |
| Otomatisasi Logo (Tahap 12) | `tests/Feature/Tahap12LogoProcessingTest.php` | 27 | PASS |
| **Subproses Logo & cURL (Tahap 12A)** | `tests/Feature/Tahap12aSubprocessLogoTest.php` | 5 | **PASS** |
| Contoh Pengujian Sistem | `tests/Feature/ExampleTest.php` | 1 | PASS |
| **TOTAL KESELURUHAN** | **11 File Uji** | **164 TESTS** | **ALL PASS** |

- **Total Assertions:** **679 assertions** (100% Lulus, 0 Gagal, 0 Error).
- **Waktu Eksekusi Suite:** 39.61 detik.
- **Kompilasi Frontend (Vite):** Berhasil (`cmd /c npm run build`) dalam 1.88 detik (3 modul).
- **Kompilasi Template Blade:** `php artisan view:cache` berhasil tanpa kesalahan sintaksis.

---

## 7. Kendala yang Masih Tersisa

Tidak ada kendala yang tersisa (*Zero Remaining Issues*). Subproses pemrosesan logo berjalan stabil, mandiri, bebas konflik cURL/OpenSSL, mempertahankan seluruh fitur sebelumnya, dan seluruh pengujian otomatis lulus 100%.
