# TAHAP 11 — CHECKLIST KUSTOMISASI TEMA, PENGELOLAAN LOGO, DAN STRUKTUR KEPENGURUSAN BEM

Tanggal: 31 Oktober 2026  
Workspace: `C:\xampp\htdocs\portal-bem`  
Status: **SELESAI (ALL PASS)**

---

## 1. Lingkup Pekerjaan & Hasil Verifikasi

### Fitur 1: Kustomisasi Tema Website (Super Admin)
- [x] Model `SiteSetting` dengan konstanta default theme dan preset warna siap pakai.
- [x] Penyimpanan pengaturan skema warna ke tabel `site_settings` dengan group `theme` dan tipe `color`.
- [x] Input warna dengan dual-input: Color picker visual HTML5 + teks HEX 6 digit (contoh: `#0F172A`).
- [x] Validasi kode HEX warna pada backend (regex `^#([a-fA-F0-9]{6})$`).
- [x] 5 Preset tema siap pakai:
  - *Integritas & Aksi (Default Navy & Emas)*
  - *Harmoni Hijau Kampus (Emerald & Amber)*
  - *Sinergi Akademik (Royal Blue & Orange)*
  - *Semangat Progresif (Crimson & Amber)*
  - *Modern Digital (Deep Dark & Cyan)*
- [x] Pratinjau langsung interaktif (Live Preview) berbasis JavaScript pada antarmuka admin.
- [x] Tombol reset tema ke standar bawaan dengan konfirmasi dialog.
- [x] Injeksi variabel CSS dinamis (`--theme-primary`, `--theme-secondary`, `--theme-accent`, `--theme-bg`, `--theme-surface`, `--theme-text-primary`, `--theme-text-secondary`) pada tag `<style>` di `:root` layout publik `layouts/app.blade.php`.
- [x] Integrasi token tema di Tailwind CSS v4 `@theme` (`resources/css/app.css`).
- [x] Pembatasan hak akses: Khusus `super_admin`. Peran `admin`, `editor`, `advocacy`, dan `guest` ditolak (403/redirect).
- [x] Pencatatan audit log saat pembaruan dan pemulihan (reset) tema.

### Fitur 2: Pengelolaan Logo Website (Super Admin)
- [x] Tampilan pratinjau logo aktif di dashboard admin pengaturan logo.
- [x] Tampilan fallback badge inisial "BEM" jika belum ada logo kustom yang diunggah.
- [x] Validasi berkas logo: `required`, `image`, `mimes:png,jpeg,jpg,webp`, `max:2048` (maks 2MB).
- [x] Penyimpanan file logo pada disk `public` di direktori `logos/`.
- [x] Penyimpanan path referensi logo di `site_settings` (key: `site_logo`).
- [x] Penghapusan berkas fisik lama secara aman saat logo diperbarui dengan yang baru.
- [x] Tombol hapus logo kustom untuk kembali ke logo inisial standar, dengan penghapusan file fisik dari storage.
- [x] Integrasi tampilan logo pada bilah navigasi utama (`components/navbar.blade.php`) dan footer publik (`components/footer.blade.php`).
- [x] Pembatasan hak akses: Khusus `super_admin`.
- [x] Pencatatan audit log saat pengunggahan dan penghapusan logo kustom.

### Fitur 3: Fitur Hapus Struktur Organisasi di Admin
- [x] Tombol "Hapus" pada tabel manajemen ormawa (`resources/views/admin/organizations/index.blade.php`).
- [x] Konfirmasi dialog pencegahan penghapusan tidak sengaja.
- [x] Endpoint `DELETE admin/organisasi/{organization}` dengan penanganan controller yang aman.
- [x] Penghapusan record ormawa dari database.
- [x] Pencatatan audit log aktivitas penghapusan (`organization.delete`) mencakup ID, kode, dan nama ormawa.
- [x] Otorisasi: hanya dapat diakses oleh pengguna yang berhak atas modul `organizations`.

### Fitur 4: Halaman Publik Khusus Struktur Kepengurusan BEM
- [x] Route publik `/struktur-bem` (nama: `bem.structure`) dan alias redirect `/kepengurusan`.
- [x] Model `BemMember` dengan field: `name`, `position`, `department`, `level`, `period`, `photo_path`, `description`, `sort_order`, `is_published`.
- [x] Pembagian tingkatan hierarki (tiers):
  - **Tier 1: Presma & Wapresma** (kartu hero besar, foto resmi, kutipan visi).
  - **Tier 2: Badan Pengurus Harian (BPH)** (Sekjen, Bendum, dll).
  - **Tier 3: Menteri & Kepala Biro** (dikelompokkan per kementerian/bidang).
  - **Tier 4: Staf & Anggota Kabinet** (grid per departemen).
- [x] Filter & navigasi tab lompat cepat antar tingkatan kepengurusan.
- [x] Fallback foto avatar SVG (`public/images/member-placeholder.svg`) jika pengurus belum memiliki foto kustom.
- [x] Tampilan empty state informatif jika data kepengurusan belum dipublikasikan.
- [x] Tautan silang antara direktori Ormawa (`/organisasi`) dan Struktur Kabinet BEM (`/struktur-bem`).
- [x] CRUD Admin Lengkap untuk pengurus BEM di `/admin/kepengurusan`:
  - List pengurus dengan filter pencarian, filter tingkat hierarki, filter status terbit/draft, dan paginasi.
  - Form tambah pengurus dengan upload foto (maks 2MB), tingkatan hierarki, urutan tampil, dan preview.
  - Form edit pengurus dengan kemampuan ganti foto dan hapus foto lama.
  - Tombol hapus pengurus dengan pembersihan berkas fisik foto dari storage.
  - Hak akses modul `bem_members` untuk `SuperAdmin`, `Admin`, dan `Editor`.

---

## 2. Hasil Verifikasi Pengujian Otomatis

- Total Tests Sebelumnya (Tahap 10): **107 tests, 461 assertions**
- Penambahan Tests Baru (Tahap 11): **25 tests, 113 assertions**
  - `Tahap11ThemeAndLogoTest`: 12 tests, 45 assertions.
  - `Tahap11BemMemberAndOrganizationDeleteTest`: 13 tests, 68 assertions.
- Total Tests Keseluruhan: **132 tests, 574 assertions (ALL PASS)**
- Kompilasi Vite: **Berhasil (built in <2s)**
