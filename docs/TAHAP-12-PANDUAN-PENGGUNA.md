# PANDUAN PENGGUNA — OTOMATISASI PENGHAPUSAN BACKGROUND & PERAPIAN LOGO
## Panduan Operasional Super Administrator Portal BEM Terpadu

---

## 1. Pengantar

Fitur **Otomatisasi Logo Website** pada Tahap 12 memungkinkan Super Administrator untuk mengunggah lambang resmi BEM (organisasi mahasiswa, universitas, atau kabinet) secara mudah tanpa perlu melakukan pengeditan manual transparansi di aplikasi desain grafis terlebih dahulu.

Sistem secara cerdas akan:
1. Mendeteksi warna background gambar secara otomatis.
2. Menghapus background menjadi transparan menggunakan segmentasi batas (*boundary-connected flood-fill*) tanpa merusak warna putih atau detail di dalam lambang.
3. Memotong ruang kosong yang tidak perlu di sekeliling lambang (*auto-crop / trim*).
4. Menambahkan bantalan transparan (*padding*) proporsional agar logo tampak seimbang di bilah navigasi dan footer.
5. Merapikan tepi objek dengan teknik *anti-aliasing feathering* agar menyatu mulus di latar terang maupun gelap.
6. Menyediakan antarmuka **Pratinjau Multi-Latar** sebelum Anda memutuskan untuk menerapkannya secara permanen ke website.

---

## 2. Langkah Penggunaan Fitur

### Langkah 1: Membuka Halaman Pengelolaan Logo
1. Masuk ke Panel Administrasi Portal BEM sebagai **Super Administrator**.
2. Pada bilah navigasi samping kiri, pilih menu **Administrasi Sistem** &rarr; **Pengaturan Logo** (atau akses URL `/admin/pengaturan/logo`).

### Langkah 2: Mengunggah Berkas Gambar Logo
1. Pada kartu formulir **"Unggah Logo Baru"** di sisi kanan:
   - Klik area kotak unggah (*dropzone*) atau seret berkas gambar langsung ke dalam kotak.
   - **Ketentuan berkas:**
     - Format: **PNG**, **JPG/JPEG**, atau **WebP**.
     - Ukuran maksimal: **2 Megabyte (MB)**.
     - Resolusi yang didukung: minimal 1x1 px hingga maksimal 4000x4000 px.
2. Sistem akan menampilkan pratinjau instan berkas yang dipilih di sisi peramban.

### Langkah 3: Menyesuaikan Parameter Pemrosesan (Opsional)
Di bawah area pemilihan berkas, terdapat pengaturan lanjutan yang dapat disesuaikan sesuai kebutuhan:
- **Hapus Background Otomatis:** Centang opsi ini jika gambar Anda memiliki background solid (misal putih, hitam, abu-abu) yang ingin dihilangkan. (Hilangkan centang jika berkas yang diunggah sudah transparan murni dan Anda hanya ingin merapikan ruang kosongnya).
- **Toleransi Warna Background (Default: 28):** Mengatur seberapa sensitif sistem mendeteksi variasi warna background. Naikkan nilai jika latar belakang gambar memiliki gradasi halus, atau turunkan jika tepi lambang Anda memiliki warna yang hampir mirip dengan latar.
- **Padding Ruang Kosong (Default: 16 piksel):** Menentukan jarak aman transparan di sekeliling tepi lambang agar logo tidak menempel terlalu rapat ke pinggir navbar.
- **Kehalusan Tepi / Anti-Aliasing (Default: 2):** Menentukan tingkat kehalusan transisi piksel pada tepi lambang agar tidak tampak bergerigi (*jagged*).

### Langkah 4: Memproses & Melihat Pratinjau Server
1. Klik tombol **"Proses & Pratinjau Server"**.
2. Sistem akan menjalankan algoritma segmentasi, menghapus background, memangkas ruang kosong, dan menghaluskan tepi tanpa mengubah logo aktif website saat ini.
3. Setelah selesai, banner gelap bertuliskan **"Hasil Pemrosesan Otomatis Siap Ditinjau"** akan muncul di bagian atas halaman.

### Langkah 5: Meninjau Hasil di Berbagai Jenis Latar
Pada panel hasil pratinjau, periksa secara saksama:
1. **Kartu Statistik:**
   - Perubahan dimensi gambar dari ukuran awal ke ukuran hasil pangkas.
   - Persentase ruang kosong yang berhasil dihilangkan.
   - Warna background yang berhasil diidentifikasi.
   - Ukuran berkas hasil olahan.
2. **Uji Transparansi 4 Kotak Uji:**
   - **Latar Terang (Putih `#FFFFFF`):** Pastikan tidak ada kotoran atau artefak warna yang tersisa di area luar lambang.
   - **Latar Gelap (Navy `#0F172A`):** Pastikan tepi lambang tidak memiliki garis putih kasar (*white halo effect*).
   - **Latar Zamrud (`#064E3B`):** Memastikan logo tetap kontras dan elegan pada warna tema kampus.
   - **Pola Papan Catur (*Checkerboard*):** Memverifikasi bahwa area latar belakang benar-benar tembus pandang (transparan murni).
   - **Bandingkan dengan Asli:** Tinjau gambar asli di sisi kanan untuk memastikan teks, bintang, padi, atau ornamen putih di dalam lambang **tetap utuh dan tidak hilang**.

### Langkah 6: Menerapkan atau Membatalkan
- **Jika Hasil Sudah Sempurna:**  
  Klik tombol hijau **"Terapkan Sebagai Logo Resmi"**. Sistem akan memindahkan logo ke penyimpanan permanen publik, memperbarui tampilan di seluruh website (Navbar, Footer, Halaman Login, dan Sidebar Admin), dan mencatat aktivitas ini ke Audit Log sistem.
- **Jika Ingin Membatalkan atau Mencoba Gambar Lain:**  
  Klik tombol **"Batalkan"**. Seluruh berkas pratinjau sementara akan dihapus seketika dan logo aktif website Anda tetap aman tidak berubah.

---

## 3. Menghapus Logo Kustom (Kembali ke Logo Bawaan)

Jika di masa mendatang Anda ingin menghapus logo kustom dan mengembalikan tampilan ke badge inisial "BEM" standar bawaan sistem:
1. Buka menu **Pengaturan Logo**.
2. Pada panel kiri di bawah pratinjau logo aktif, klik tombol merah **"Hapus Logo"**.
3. Konfirmasikan dialog konfirmasi peramban.
4. Logo kustom akan dihapus permanen dari server dan website akan kembali menampilkan badge identitas resmi standar.

---

## 4. Tips Menghasilkan Logo Paling Optimal

1. **Gunakan Gambar Berkontras Jelas:** Pastikan warna latar belakang gambar memiliki kontras yang cukup dengan warna garis tepi terluar lambang.
2. **Hindari Latar Belakang Foto Kompleks:** Algoritma segmentasi bekerja terbaik pada gambar lambang resmi dengan latar belakang solid (putih, abu-abu muda, hitam, dsb.). Hindari mengunggah foto lambang di atas kain bendera atau dinding bercorak jika menginginkan hasil pemotongan otomatis yang bersih.
3. **Resolusi Ideal:** Gambar logo berukuran 500x500 hingga 1500x1500 piksel merupakan resolusi paling ideal untuk ketajaman tampilan layar retina dan efisiensi waktu pemrosesan.
4. **Elemen Putih Internal Lambang:** Anda tidak perlu khawatir jika lambang Anda memiliki bintang putih, perisai putih, atau pita putih di dalamnya. Algoritma kami secara otomatis mempertahankan seluruh warna putih yang berada di dalam garis tepi lambang.

---

## 5. Pertanyaan Umum (FAQ)

**T: Apakah logo aktif website saya akan langsung berubah saat saya menekan tombol "Proses & Pratinjau"?**  
*J: Tidak. Sistem menggunakan alur persetujuan dua tahap (two-stage approval). Logo website aktif tidak akan pernah berubah sebelum Anda menekan tombol "Terapkan Sebagai Logo Resmi".*

**T: Bagaimana jika lambang yang saya miliki sudah memiliki latar transparan sejak awal?**  
*J: Anda dapat tetap mengunggahnya. Cukup hilangkan centang pada opsi "Hapus Background Otomatis". Sistem akan memangkas ruang kosong berlebih di sekelilingnya dan memastikan padding proporsional.*

**T: Siapa saja yang memiliki wewenang mengubah logo website?**  
*J: Hanya akun dengan peran **Super Administrator** yang diizinkan memproses, menerapkan, atau menghapus logo website. Peran lain (Administrator biasa, Tim Advokasi, Editor) akan ditolak oleh sistem keamanan RBAC.*
