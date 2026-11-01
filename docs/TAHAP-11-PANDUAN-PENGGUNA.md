# PANDUAN PENGGUNA — FITUR TAHAP 11 PORTAL BEM TERPADU

Panduan ini ditujukan bagi Administrator, Editor, dan Super Administrator dalam mengelola kustomisasi tema, logo resmi website, penghapusan ormawa, dan pengelolaan kepengurusan kabinet BEM.

---

## 1. Kustomisasi Skema Warna & Tema Website (Khusus Super Admin)

### Lokasi Menu:
Buka panel admin &rarr; Menu **Administrasi Sistem** &rarr; **Kustomisasi Tema** (`/admin/pengaturan/tema`).

### Langkah Penggunaan:
1. **Memilih Preset Siap Pakai:**
   - Pada bagian atas halaman, klik salah satu kartu preset skema warna (*Integritas & Aksi*, *Harmoni Hijau Kampus*, *Sinergi Akademik*, *Semangat Progresif*, atau *Modern Digital*).
   - Seluruh input kode HEX dan picker visual di bawahnya akan terisi secara otomatis seketika.
2. **Menyesuaikan Warna Tertentu:**
   - Anda dapat mengubah kode HEX 6 digit secara langsung (contoh: `#0F172A`) atau mengklik kotak pemilih warna (color picker) di sampingnya.
   - Variabel yang dapat disesuaikan:
     - **Warna Utama (Primary):** Identitas utama, navbar, tombol prioritas.
     - **Warna Sekunder (Secondary):** Aksen pendukung dan tombol netral.
     - **Warna Aksen (Accent):** Highlight, sorotan penting, badge interaktif.
     - **Warna Latar Belakang (Background):** Warna kanvas dasar website.
     - **Warna Permukaan (Surface):** Latar belakang kartu kontainer konten.
     - **Warna Teks Utama & Sekunder:** Warna kontras tipografi bacaan.
3. **Melihat Pratinjau Langsung (Live Preview):**
   - Panel di sebelah kanan akan memperbarui tampilannya secara real-time saat Anda memilih atau mengetik kode warna.
4. **Menyimpan Perubahan:**
   - Klik tombol **"Simpan Perubahan Tema"**. Variabel warna akan langsung aktif dan diterapkan ke seluruh antarmuka publik.
5. **Mengembalikan ke Standar (Reset):**
   - Jika ingin memulihkan skema warna bawaan, klik tombol **"Reset ke Tema Standar"** di pojok kanan atas dan konfirmasi dialog.

---

## 2. Pengelolaan Logo Resmi Website (Khusus Super Admin)

### Lokasi Menu:
Buka panel admin &rarr; Menu **Administrasi Sistem** &rarr; **Pengaturan Logo** (`/admin/pengaturan/logo`).

### Langkah Penggunaan:
1. **Melihat Status Logo Saat Ini:**
   - Panel kiri menampilkan logo yang sedang aktif digunakan di navbar dan footer portal.
   - Jika belum ada logo kustom, sistem menampilkan badge inisial "BEM" standar secara anggun.
2. **Mengunggah Logo Baru:**
   - Pada formulir di panel kanan, klik area dropzone berkas atau seret berkas gambar ke dalamnya.
   - **Ketentuan berkas:**
     - Format yang didukung: **PNG**, **JPG/JPEG**, atau **WebP**.
     - Ukuran maksimal: **2 Megabyte (MB)**.
     - **Rekomendasi:** Gunakan gambar PNG dengan latar belakang transparan beresolusi tajam agar menyatu sempurna dengan latar navigasi.
   - Periksa pratinjau instan berkas yang dipilih.
   - Klik **"Unggah & Simpan Logo"**.
3. **Menghapus Logo Kustom:**
   - Di bawah pratinjau logo aktif pada panel kiri, klik tombol **"Hapus Logo"**.
   - Berkas lama akan dibersihkan dari server dan portal otomatis kembali menampilkan badge logo standar bawaan.

---

## 3. Menghapus Lembaga Struktur Organisasi Kampus (Ormawa)

### Lokasi Menu:
Buka panel admin &rarr; Menu **Struktur Ormawa** (`/admin/organisasi`).

### Langkah Penggunaan:
1. Pada tabel daftar lembaga kemahasiswaan (BEM, MAM, DPM, HIMA, HMPS, UKM), temukan baris ormawa yang ingin dihapus.
2. Klik tombol merah **"Hapus"** pada kolom Aksi di sisi kanan baris.
3. Dialog konfirmasi browser akan muncul untuk memastikan Anda tidak sengaja mengklik:
   *"Apakah Anda yakin ingin menghapus lembaga ormawa [Nama] ([Kode])? Tindakan ini tidak dapat dibatalkan."*
4. Klik **OK** untuk melanjutkan. Data ormawa akan dihapus dan aktivitas tersebut otomatis tercatat ke dalam Audit Log sistem untuk akuntabilitas.

---

## 4. Pengelolaan Fungsionaris Kabinet BEM (Super Admin, Admin, Editor)

### Lokasi Menu:
Buka panel admin &rarr; Menu **Pengurus Kabinet BEM** (`/admin/kepengurusan`).

### Langkah Penggunaan:
1. **Menambah Pengurus Baru:**
   - Klik tombol **"Tambah Pengurus Baru"** di sudut kanan atas.
   - Isi informasi fungsionaris:
     - **Nama Lengkap & Gelar / NIM:** Nama pejabat kepengurusan.
     - **Jabatan Struktural:** Contoh: *Presiden Mahasiswa*, *Menteri Advokasi*, dsb.
     - **Tingkatan Hierarki:**
       - *Presiden & Wakil Presiden Mahasiswa* (Tier 1 - Tampil paling atas dalam kartu hero besar).
       - *Badan Pengurus Harian (Sekretaris & Bendahara)* (Tier 2).
       - *Menteri & Kepala Biro / Koordinator Bidang* (Tier 3).
       - *Staf / Anggota Departemen* (Tier 4).
     - **Departemen / Kementerian:** Nama kementerian tempat pengurus bernaung (dapat dikosongkan jika Presma/Wapresma).
     - **Periode Kepengurusan:** Contoh: *2025/2026*.
     - **Urutan Tampil (Sort Order):** Angka prioritas urutan tampil (contoh: 1 untuk Presma, 2 untuk Wapresma).
     - **Foto Profil:** Unggah foto resmi pengurus (maks 2MB). Jika belum ada foto, sistem otomatis menyematkan avatar placeholder SVG resmi.
     - **Fokus Tugas / Deskripsi:** Kutipan personal atau uraian tanggung jawab.
     - **Status Publikasi:** Centang checkbox agar fungsionaris tampil di halaman publik.
   - Klik **"Simpan Pengurus"**.
2. **Mengubah / Memperbarui Data Pengurus:**
   - Klik tombol **"Edit"** pada baris pengurus yang ingin diubah.
   - Perbarui isian formulir atau unggah foto pengganti.
   - Klik **"Simpan Perubahan"**.
3. **Menghapus Data Pengurus:**
   - Klik tombol **"Hapus"** dan konfirmasi dialog. Berkas foto fisik yang tersimpan di server akan dibersihkan secara otomatis.
4. **Melihat Halaman Publik:**
   - Klik tombol **"Lihat Halaman Publik"** atau buka URL `/struktur-bem` (atau `/kepengurusan`) untuk melihat representasi visual hierarki kabinet BEM.
