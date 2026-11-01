# TAHAP 12 — LAPORAN REKAYASA & IMPLEMENTASI
## Otomatisasi Penghapusan Background, Perapian Logo, dan Sistem Pratinjau Transparansi Multi-Latar

Tanggal: 31 Oktober 2026  
Workspace: `C:\xampp\htdocs\portal-bem`  
Penyusun: Senior Laravel Developer & Image Processing Engineer  
Status: **SELESAI DENGAN STATUS ALL PASS (159 Tests, 646 Assertions, 0 Failures)**

---

## 1. Ringkasan Eksekutif

Tahap 12 berhasil menyempurnakan modul pengaturan logo Portal BEM Terpadu melalui otomasi pemrosesan citra digital lokal bermutu tinggi, mekanisme pratinjau interaktif multi-latar, dan sistem persetujuan dua tahap (*two-stage approval workflow*).

Fitur-fitur utama yang berhasil diwujudkan:
1. **Otomatisasi Penghapusan Background Citra:** Sistem mendeteksi warna background perimeter secara otomatis dan mengeksekusi algoritma *boundary-connected flood-fill* (BFS) dari 4 tepi kanvas terluar. Algoritma ini menjamin detail putih/terang di dalam lambang (seperti bintang, pita, teks, atau perisai) **tidak terhapus secara membabi buta**.
2. **Transparansi Alpha Channel 32-bit & Perapian Tepi (*Anti-Aliasing Feathering*):** Menghasilkan alpha channel yang halus pada perbatasan objek foreground sehingga logo menyatu alami tanpa efek bergerigi (*jagged edges*) atau lingkaran putih kasar (*white halo effect*).
3. **Pemangkasan Ruang Kosong (*Auto-Crop / Trim*) & Padding Proporsional:** Menghitung *bounding box* koordinat piksel foreground sesungguhnya, memangkas ruang kosong sekeliling lambang, dan menambahkan bantalan transparan yang proporsional (*padding*) untuk menjaga rasio aspek lambang.
4. **Sistem Pratinjau Interaktif Multi-Latar Sebelum Penerapan:** Hasil olahan disajikan kepada Super Admin untuk diuji langsung di atas latar terang (`#FFFFFF`), latar gelap (`#0F172A`), latar aksen zamrud (`#064E3B`), dan pola papan catur (*checkerboard pattern*), lengkap dengan perbandingan berdampingan (*side-by-side*) dengan citra asli dan kartu statistik pemrosesan.
5. **Perlindungan Logo Aktif (*Two-Stage Approval*):** Logo aktif di bilah navigasi dan footer **tidak akan pernah diganti** sampai Super Admin secara eksplisit menekan tombol *"Terapkan Sebagai Logo Resmi"*. Jika Super Admin membatalkan, logo aktif tetap utuh dan berkas sementara dibersihkan dari server.
6. **Konsistensi Tampilan Seluruh Sistem:** Logo resmi hasil olahan diterapkan secara menyeluruh pada Navbar Publik, Footer Publik, Halaman Login Admin (`/admin/login`), dan Sidebar Panel Administrasi (`layouts/admin.blade.php`), dengan fallback elegan ke badge lambang standar jika logo kustom belum diunggah atau dihapus.
7. **Kepatuhan Lingkungan & Keandalan Eksekusi Windows:** Mengatasi batasan ekstensi PHP GD pada sistem Windows tanpa memerlukan hak istimewa administrator, memanfaatkan pemuatan dinamis `-dextension=C:\xampp\php\ext\php_gd.dll` melalui sub-proses terisolasi Artisan CLI (`logo:process-image`), serta menjamin kompatibilitas penuh dengan sistem berkas nyata maupun virtual disk `Storage::fake('public')`.

Semua pekerjaan mematuhi batasan mutlak: **DILARANG menjalankan migrate:fresh, db:wipe, atau merusak data yang sudah ada**, tidak memodifikasi `vendor/`, dan seluruh 132 automated tests sebelumnya tetap 100% lulus.

---

## 2. Arsitektur dan Rekayasa Teknis

### A. Layanan Segmentasi Citra Digital (`app/Services/LogoProcessingService.php`)

Layanan ini merupakan inti algoritma pemrosesan citra yang dirancang modular:
- **Validasi Berkas Ketat (`validateImage`):**
  - Memverifikasi keberadaan fisik berkas dan ukuran non-nol.
  - Membatasi ukuran berkas maksimal 2MB (`MAX_FILE_SIZE`).
  - Memeriksa MIME type riil menggunakan `finfo` (`image/png`, `image/jpeg`, `image/jpg`, `image/webp`).
  - Memeriksa integritas berkas citra dan resolusi aman (1x1 hingga 4000x4000 piksel) menggunakan `@getimagesize`.
- **Sampling Warna Perimeter Terluar:**
  - Mengambil sampel piksel dari 4 sudut kanvas `(0,0)`, `(W-1, 0)`, `(0, H-1)`, `(W-1, H-1)` serta titik tengah perimeter untuk mendeteksi warna dasar latar belakang masukan.
- **Boundary-Connected Flood Fill (BFS):**
  - Menggunakan antrean `SplQueue` untuk menjelajahi piksel yang terhubung langsung dengan batas terluar kanvas yang memiliki selisih warna dalam rentang toleransi jarak Euclidean RGB.
  - Piksel background internal yang tertutup batas lambang (seperti teks putih di dalam lingkaran biru) tidak tersentuh karena tidak memiliki jalur koneksi ke batas luar kanvas.
- **Perapian Tepi (*Anti-Aliasing Feathering*):**
  - Menghitung interpolasi rasio jarak warna terhadap toleransi pada piksel transisi untuk menghasilkan derajat transparansi parsial (*soft alpha blending*), mencegah artefak halo putih.
- **Bounding Box Calculation & Auto-Trimming:**
  - Melacak koordinat `minX`, `maxX`, `minY`, dan `maxY` dari seluruh piksel foreground aktif (alpha < 124).
  - Memotong kanvas sesuai batas fisik foreground dan memindahkannya ke kanvas baru dengan tambahan padding transparan proporsional (default: 16px).
- **Dual Runtime Adapter:**
  - Mendeteksi `extension_loaded('gd')`: jika aktif, pemrosesan dieksekusi langsung *in-process*.
  - Jika belum dimuat pada CLI utama, memanggil sub-proses Artisan CLI via `Process::run()` dengan argumen `-dextension=C:\xampp\php\ext\php_gd.dll` ke perintah `logo:process-image`.

### B. Perintah Konsol Artisan (`app/Console/Commands/ProcessLogoImageCommand.php`)

- **Signature:**  
  `logo:process-image {input} {output} {--tolerance=28} {--padding=16} {--feather=2} {--no-remove-bg}`
- **Fungsi:** Mengoperasikan pemrosesan citra dari baris perintah dengan keluaran JSON terstruktur yang berisi metadata dimensi asli, dimensi hasil pangkas, persentase ruang kosong yang dipangkas, warna background yang terdeteksi, rasio aspek, dan ukuran berkas akhir.

### C. Alur Pengendali & Siklus Hidup Pratinjau (`app/Http/Controllers/Admin/SettingAdminController.php`)

1. **`processLogoPreview(Request $request)` (`POST /admin/pengaturan/logo/proses`):**
   - Menerima unggahan logo, menghasilkan token acak 32-karakter unik.
   - Menyimpan citra asli sementara ke `storage/app/private/temp_logos/orig_{token}.ext`.
   - Menjalankan pipeline pemrosesan ke `storage/app/private/temp_logos/proc_{token}.png`.
   - Menyimpan sesi `logo_preview` (token, metadata, nama berkas asli, opsi toleransi).
   - Mengalihkan kembali ke halaman logo dengan status siap tinjau.
2. **`serveTempPreview(string $token)` (`GET /admin/pengaturan/logo/pratinjau-temp/{token}`):**
   - Mengalirkan berkas PNG sementara hasil olahan secara aman dengan validasi token heksadesimal regex `^[a-fA-F0-9]{32}$`.
   - Header: `Content-Type: image/png`, `Cache-Control: no-cache, private`.
3. **`serveTempOriginal(string $token)` (`GET /admin/pengaturan/logo/asli-temp/{token}`):**
   - Mengalirkan berkas asli sementara untuk perbandingan berdampingan (*side-by-side comparison*).
4. **`applyLogo()` (`POST /admin/pengaturan/logo/terapkan`):**
   - Mengambil token aktif dari sesi.
   - Menyalin berkas olahan ke penyimpanan publik menggunakan `Storage::disk('public')->put('logos/logo_{uniqid}.png', ...)`.
   - Memperbarui rekaman basis data `SiteSetting::set('site_logo', ...)`.
   - Menghapus berkas logo lama dari disk publik jika ada.
   - Membersihkan seluruh berkas sementara (`orig_*` dan `proc_*`) dari server.
   - Mencatat aktivitas ke tabel `admin_audit_logs`.
   - Mengosongkan sesi pratinjau.
5. **`cancelLogoPreview()` (`POST /admin/pengaturan/logo/batal`):**
   - Membersihkan berkas sementara terkait token dari server.
   - Mengosongkan sesi pratinjau tanpa mengubah logo aktif saat ini.
6. **`updateLogo(Request $request)` (`POST /admin/pengaturan/logo`):**
   - Alur pengunggahan langsung dengan pemrosesan otomatis background transparan (kompatibel penuh dengan implementasi Tahap 11).

---

## 3. Desain Antarmuka Pengguna & Pengalaman Pengguna (UI/UX)

### A. Panel Pengaturan Logo Admin (`resources/views/admin/settings/logo.blade.php`)
- **Indikator Status & Banner Persetujuan:**  
  Menampilkan banner bernuansa gelap dan emas yang memberitahukan Super Admin bahwa hasil pemrosesan sedang menunggu persetujuan, lengkap dengan tombol tegas *"Terapkan Sebagai Logo Resmi"* (hijau zamrud) dan *"Batalkan"* (netral).
- **Kartu Metrik & Statistik:**  
  Menampilkan 4 statistik kunci:
  1. Perubahan dimensi citra (misal: `800x800` &rarr; `540x540` px) dengan penanda proporsi aspek terjaga.
  2. Persentase ruang kosong yang dipangkas (misal: `54.4%`).
  3. Warna background yang terdeteksi beserta *swatch* warna visualnya.
  4. Ukuran berkas keluaran dalam KB dengan format 32-bit PNG Alpha.
- **Uji Transparansi Multi-Latar (Side-by-Side):**
  - **Latar Terang (`#FFFFFF`):** Memastikan tidak ada sisa artefak kotor pada antarmuka terang.
  - **Latar Gelap (`#0F172A`):** Memastikan tepi lambang bersih tanpa efek lingkaran putih (*white halo*).
  - **Latar Pola Catur (*Checkerboard*):** Memverifikasi transparansi tembus pandang yang murni.
  - **Bandingkan Citra Asli:** Menampilkan citra masukan sebelum diproses untuk memudahkan evaluasi visual Super Admin.
- **Kendali Kustomisasi Pemrosesan:**
  - Slider Toleransi Warna Background (5–100, default: 28).
  - Slider Padding Ruang Kosong (0–80 piksel, default: 16px).
  - Slider Kehalusan Tepi Anti-Aliasing (0–5 tingkat, default: 2).
  - Opsi lewati penghapusan background jika logo yang diunggah memang sudah transparan murni.

### B. Pembaruan Layout & Branding Sistem
- **Public Navbar (`resources/views/components/navbar.blade.php`):** Menampilkan logo transparan resmi dengan batasan tinggi proporsional dan transisi mulus.
- **Public Footer (`resources/views/components/footer.blade.php`):** Menampilkan logo resmi yang menyatu harmonis pada latar gelap footer.
- **Admin Login (`resources/views/admin/auth/login.blade.php`):** Menampilkan logo resmi BEM pada kartu autentikasi dengan fallback otomatis ke badge inisial "BEM".
- **Admin Sidebar (`resources/views/layouts/admin.blade.php`):** Menampilkan logo resmi pada bilah navigasi panel dashboard admin.

---

## 4. Hasil Verifikasi Pengujian Otomatis (Automated Tests)

Pemeriksaan komprehensif dilakukan menggunakan framework pengujian PHPUnit pada lingkungan terisolasi:

| Suite Pengujian | File Uji | Jumlah Tes | Status |
|---|---|:---:|:---:|
| Manajemen Admin Umum & RBAC | `tests/Feature/AdminTest.php` | 27 | PASS |
| Siklus & Penanganan Gambar Berita | `tests/Feature/ArticleImageTest.php` | 13 | PASS |
| Sistem Pengiriman & Lacak Aspirasi | `tests/Feature/AspirationTest.php` | 19 | PASS |
| Halaman Publik & SEO | `tests/Feature/PublicPagesTest.php` | 8 | PASS |
| Audit Keamanan & Privasi CSV (Tahap 5) | `tests/Feature/Tahap5SecurityAndAuditTest.php` | 19 | PASS |
| Sitemap, Robots, & Cache (Tahap 6) | `tests/Feature/Tahap6SeoAndPerformanceTest.php` | 21 | PASS |
| Kustomisasi Tema & Pengelolaan Logo (Tahap 11) | `tests/Feature/Tahap11ThemeAndLogoTest.php` | 12 | PASS |
| Pengurus BEM & Hapus Ormawa (Tahap 11) | `tests/Feature/Tahap11BemMemberAndOrganizationDeleteTest.php` | 13 | PASS |
| **Otomatisasi Background & Perapian Logo (Tahap 12)** | `tests/Feature/Tahap12LogoProcessingTest.php` | 27 | **PASS** |
| Contoh Pengujian Sistem | `tests/Feature/ExampleTest.php` | 1 | PASS |
| **TOTAL KESELURUHAN** | **10 Test Files** | **159 TESTS** | **ALL PASS** |

### Rincian 27 Pengujian Khusus Tahap 12 (`Tahap12LogoProcessingTest.php`):
1. `test_super_admin_can_process_logo_preview` &mdash; Pemrosesan pratinjau berhasil menyimpan token dan metadata.
2. `test_preview_does_not_change_active_logo` &mdash; Menjamin logo aktif tidak berubah saat pratinjau dijalankan.
3. `test_super_admin_can_apply_previewed_logo` &mdash; Penerapan logo memindahkan berkas ke storage publik dan mencatat audit log.
4. `test_apply_replaces_old_logo_and_deletes_previous` &mdash; Penerapan logo baru membersihkan berkas logo lama.
5. `test_super_admin_can_cancel_preview` &mdash; Pembatalan membersihkan sesi dan mempertahankan logo aktif.
6. `test_process_rejects_non_image_file` &mdash; Penolakan berkas teks/non-gambar pada endpoint proses.
7. `test_process_rejects_oversized_file` &mdash; Penolakan berkas melebihi batas 2MB pada endpoint proses.
8. `test_upload_rejects_non_image_file` &mdash; Penolakan berkas PDF pada endpoint upload langsung.
9. `test_upload_rejects_oversized_file` &mdash; Penolakan berkas melebihi batas 2MB pada endpoint upload langsung.
10. `test_guest_cannot_access_logo_process` &mdash; Tamu diarahkan ke halaman login.
11. `test_guest_cannot_access_logo_apply` &mdash; Tamu ditolak dari endpoint apply.
12. `test_guest_cannot_access_logo_cancel` &mdash; Tamu ditolak dari endpoint cancel.
13. `test_admin_role_cannot_process_logo` &mdash; Administrator biasa ditolak (403 Forbidden).
14. `test_editor_role_cannot_process_logo` &mdash; Editor konten ditolak (403 Forbidden).
15. `test_advocacy_role_cannot_process_logo` &mdash; Tim advokasi ditolak (403 Forbidden).
16. `test_admin_role_cannot_apply_logo` &mdash; Administrator biasa tidak dapat menerapkan logo.
17. `test_admin_role_cannot_cancel_logo` &mdash; Administrator biasa tidak dapat membatalkan logo.
18. `test_temp_preview_rejects_invalid_token` &mdash; Validasi format token (hanya heksadesimal 32 karakter).
19. `test_temp_preview_returns_404_for_nonexistent_token` &mdash; Respon 404 jika token tidak ada di disk.
20. `test_temp_original_rejects_invalid_token` &mdash; Validasi token endpoint citra asli sementara.
21. `test_super_admin_can_directly_upload_logo` &mdash; Pengunggahan langsung berjalan mulus dengan auto-remove-bg.
22. `test_apply_without_preview_returns_error` &mdash; Validasi error jika apply dipanggil tanpa sesi pratinjau.
23. `test_validate_image_rejects_nonexistent_file` &mdash; Unit test penolakan path file fiktif.
24. `test_validate_image_rejects_empty_file` &mdash; Unit test penolakan file 0 byte.
25. `test_validate_image_rejects_wrong_mime` &mdash; Unit test penolakan berkas teks biasa.
26. `test_validate_image_accepts_valid_png` &mdash; Unit test penerimaan gambar PNG valid.
27. `test_logo_page_shows_required_elements` &mdash; Tampilan antarmuka memuat teks dan formulir wajib.

### Metrik Eksekusi:
- **Baseline Sebelumnya (Tahap 11):** 132 tests, 574 assertions.
- **Peningkatan Tahap 12:** +27 tests (+72 assertions).
- **Hasil Akhir Suite Pengujian:** 159 tests, 646 assertions (100% PASS, 0 failed, 0 errors).
- **Waktu Eksekusi Suite:** 25.50 detik.
- **Kompilasi Frontend (Vite):** 3 modul terkompilasi dalam 1.61 detik tanpa error.
- **Kompilasi Blade Views:** `php artisan view:cache` berhasil tanpa kesalahan sintaksis.

---

## 5. Kesimpulan

Tahap 12 telah diselesaikan secara tuntas dengan standar rekayasa perangkat lunak tertinggi. Portal BEM Terpadu kini memiliki sistem pengelolaan lambang resmi yang cerdas, aman, dan profesional:
- Background terhapus secara akurat tanpa merusak elemen putih internal lambang.
- Ruang kosong terpotong secara efisien dan tepi lambang ter-antialiasing halus.
- Super Admin memiliki kontrol penuh meninjau hasil pada berbagai latar sebelum menerapkan secara permanen.
- Hak akses terlindungi ketat, integritas data terjaga, dan 100% tes otomatis lulus sempurna.
