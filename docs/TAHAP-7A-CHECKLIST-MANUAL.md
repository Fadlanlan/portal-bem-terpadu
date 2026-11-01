# Checklist Manual Tahap 7A — Portal BEM Terpadu

Checklist ini hanya untuk **staging terisolasi**. Jangan gunakan database, storage, mailbox, akun administrator, aspirasi, atau unggahan produksi. Pada pemeriksaan lokal saat Tahap 7A, `app.env` adalah `local`, `app.debug` bernilai aktif, dan koneksi default adalah MySQL; jangan gunakan instance lokal tersebut untuk alur yang menulis data. Gunakan URL staging yang diberikan operator.

## Aturan pelaksanaan dan pencatatan

Sebelum mulai, minta operator memastikan staging memakai database khusus (contoh `portal_bem_staging`), storage terpisah, `APP_ENV=production`, `APP_DEBUG=false`, HTTPS, akun sintetis untuk semua role, serta `MAIL_MAILER=array` atau mailbox staging terkendali. Email SMTP nyata hanya boleh diuji setelah persetujuan eksplisit dan alamat staging yang tepat telah diberikan.

Catat untuk setiap langkah: ID langkah, tanggal/waktu dan zona waktu, browser/versi, ukuran viewport, hasil `PASS`, `FAIL`, atau `BLOCKED`, serta bukti yang sudah disamarkan. Jangan menyimpan cookie/session ID, password, tracking code, kode pemulihan, alamat email pribadi, isi aspirasi, token CSRF, kredensial, atau screenshot yang memuatnya. Untuk data uji, simpan nomor referensi hanya pada catatan operator staging yang aksesnya dibatasi; jangan masukkan nilainya ke laporan umum.

Jika syarat isolasi tidak dapat dibuktikan, hentikan pengujian tulis. Tandai `BLOCKED`, jelaskan syarat yang belum tersedia, dan jangan mencoba memperbaikinya dengan mengubah `.env` lokal atau memakai database `portal_bem`.

## A. Persiapan staging dan konfigurasi debug

**Prasyarat:** URL staging dan operator pemilik staging tersedia; lingkungan hanya menggunakan data sintetis dan database/storage terpisah.

**Tindakan:** Minta operator memverifikasi pada instance staging (tanpa mengirim nilai rahasia) bahwa `APP_ENV=production`, `APP_DEBUG=false`, URL mengarah ke staging, database bukan `portal_bem`, storage terpisah, dan mailer aman. Sesudah konfigurasi staging berubah, operator menjalankan `php artisan optimize:clear`, kemudian membangun ulang cache konfigurasi/rute/view sesuai prosedur deployment staging. Buka beranda, halaman login, dan halaman tidak ditemukan.

**Hasil yang diharapkan:** Halaman merespons normal, exception tidak menampilkan stack trace atau konfigurasi internal, dan identitas environment yang dilihat operator cocok dengan staging.

**PASS/FAIL:** `PASS` hanya jika operator mengonfirmasi semua isolasi dan debug mati serta halaman tampil aman. Jika informasi environment tidak tersedia, `BLOCKED`.

**Pemulihan:** Jangan mengubah konfigurasi produksi atau lokal. Jika cache staging salah, hentikan smoke test dan minta operator mengembalikan konfigurasi staging ke nilai yang disetujui lalu membersihkan/membangun ulang cache di staging.

## B. Halaman publik, navigasi, berita, gambar, dan SEO

**Prasyarat:** Persiapan A lulus; tersedia konten publik sintetis atau konten nonrahasia yang disetujui.

**Tindakan:** Buka beranda, `/berita`, `/program-kerja`, `/agenda`, `/organisasi`, `/faq`, `/kontak`, `/tentang`, `/aspirasi`, `/aspirasi/kirim`, `/aspirasi/lacak`, `/sitemap.xml`, dan `/robots.txt`. Periksa navigasi internal. Buka berita bergambar, berita tanpa gambar, serta gambar yang memang tidak tersedia agar placeholder diuji. Gunakan pencarian, filter, dan pagination berita/program jika datanya cukup.

**Hasil yang diharapkan:** Halaman yang tersedia berstatus normal, bahasa dan navigasi tampil, gambar tidak merusak layout, placeholder muncul untuk gambar yang hilang, pagination mempertahankan filter, sitemap berisi URL publik dan bukan draft/admin/aspirasi privat. `robots.txt` boleh memberi arahan crawler, tetapi akses privat harus tetap ditolak oleh autentikasi/otorisasi.

**PASS/FAIL:** Catat status dan hasil per URL serta apakah tautan internal benar. Jangan menyertakan URL internal staging yang mengandung token.

**Pemulihan:** Jika link atau aset gagal, catat path/halaman dan waktu tanpa menyalin query rahasia; minta operator memeriksa route, cache/aset, atau storage staging. Jangan mengubah file upload yang sudah ada.

## C. Draft dan slug tidak ditemukan

**Prasyarat:** Akun editor staging tersedia dan diperbolehkan membuat satu draft sintetis; tidak ada publikasi ke produksi.

**Tindakan:** Buat draft dengan judul sintetis yang unik, catat slug hanya di catatan staging terbatas, lalu buka URL detailnya sebagai guest. Buka juga slug fiktif yang tidak pernah dibuat. Jangan ubah status draft menjadi published.

**Hasil yang diharapkan:** Kedua URL menghasilkan halaman 404 aman tanpa isi draft atau stack trace. Konten publik lain tetap dapat diakses.

**PASS/FAIL:** `PASS` jika draft tersembunyi dan slug fiktif memberi 404; selain itu `FAIL`.

**Pemulihan:** Jika draft terekspos, hentikan pengujian konten dan beri tahu operator keamanan. Jangan menerbitkan atau menghapus konten nyata. Draft sintetis yang dibuat khusus boleh dihapus hanya setelah identitasnya dipastikan dan sesuai prosedur cleanup staging.

## D. Login, logout, session, akun nonaktif, dan role

**Prasyarat:** Akun sintetis aktif untuk `super_admin`, `admin`, `advokasi`, dan `editor`; akun sintetis nonaktif; ada sekurangnya dua super admin staging aktif agar pengujian tidak mengunci akses. Jangan menggunakan akun aktif sebenarnya.

**Tindakan:** Login dengan masing-masing role lalu coba route langsung (bukan hanya menu):

- `super_admin`: dashboard, berita, program kerja, agenda, organisasi, aspirasi, laporan, pengguna, dan audit log.
- `admin`: dashboard dan modul operasi/konten/laporan yang diizinkan; coba `/admin/pengguna` dan `/admin/audit-log` yang harus ditolak sesuai kebijakan saat ini.
- `advokasi`: dashboard, aspirasi, dan laporan; coba berita, program, agenda, organisasi, pengguna, dan audit log yang harus ditolak sesuai kebijakan.
- `editor`: dashboard dan modul berita/program/agenda/organisasi; coba aspirasi, pengguna, laporan, dan audit log yang harus ditolak sesuai kebijakan.
- Login akun nonaktif dengan password akun uji yang benar; pastikan login tetap ditolak.
- Untuk perubahan ID session, buka DevTools > Application/Storage sebelum login dan setelah login. Bandingkan apakah cookie sesi berubah tanpa menyalin atau menyimpan nilainya.
- Logout, tekan tombol Back, dan coba URL admin langsung.

**Hasil yang diharapkan:** Hak akses ditegakkan server-side; request langsung yang tidak berizin mendapat 403, guest diarahkan ke login, akun nonaktif ditolak, session ID berubah sesudah login, dan logout mengakhiri sesi.

**PASS/FAIL:** Catat role, route, status HTTP/redirect, session berubah `ya/tidak`, dan logout `ya/tidak`. Jangan catat nilai cookie.

**Pemulihan:** Bila role salah mendapat akses, hentikan semua aktivitas akun tersebut, simpan bukti yang disamarkan, dan beri tahu operator. Jangan mengubah role akun nyata. Pastikan setidaknya satu super admin staging tetap aktif; bersihkan akun uji hanya berdasarkan ID uji yang telah dikonfirmasi.

## E. Formulir aspirasi, pelacakan, dan rate limit

**Prasyarat:** Staging terisolasi, mailer `array` atau sink non-eksternal, data uji sintetis, dan tidak ada orang lain memakai IP staging yang sama untuk tes rate limit. Untuk pelapor terbuka gunakan email mailbox sintetis yang tidak dapat menerima email nyata sampai tes SMTP terpisah disetujui.

**Tindakan:**

1. Kirim satu aspirasi anonim dengan data sintetis; isi field identitas uji secara sengaja bila ingin memeriksa bahwa aplikasi mengosongkannya.
2. Kirim satu aspirasi nonanonim menggunakan identitas sintetis.
3. Simpan nomor tiket dan tracking code yang dihasilkan hanya sementara pada catatan privat staging. Lacak pertama dengan pasangan benar, lalu dengan kode salah. Jangan menyimpan atau melaporkan nilai rahasia.
4. Coba kirim formulir dengan field wajib kosong/kategori invalid dan pastikan validasi menolak.
5. Secara terkontrol, kirim sampai batas rate limit yang didokumentasikan (5 submit/menit per IP dan 10 tracking/menit per IP), lalu satu request tambahan. Lakukan hanya di staging khusus; data sintetis yang dihasilkan harus ditandai untuk cleanup.

**Hasil yang diharapkan:** Pengiriman valid berhasil; identitas anonim tidak tersimpan/ditampilkan; tracking benar menampilkan hanya data yang terkait, tracking salah memberi pesan umum dan tidak mengungkap keberadaan tiket; validasi menolak input salah; request berlebih mendapat 429.

**PASS/FAIL:** Catat hasil per kasus tanpa nomor tiket, kode, nama, pesan aspirasi, atau alamat email. Jika batas rate limit dipengaruhi penguji lain, tandai `BLOCKED`, jangan menyimpulkan kegagalan.

**Pemulihan:** Jika tracking salah mengungkap data, hentikan semua tes aspirasi dan laporkan segera. Jangan menghapus data di luar dua rekaman uji yang dibuat dan dapat diidentifikasi dengan pasti. Cleanup dilakukan hanya di staging oleh operator; jangan menghapus bukti yang diperlukan untuk investigasi.

## F. CSV dan privasi anonim

**Prasyarat:** Data aspirasi sintetis di staging, salah satunya anonim dengan field identitas sintetis, dan role yang memang berizin mengekspor laporan.

**Tindakan:** Ekspor CSV, buka dengan editor teks terlebih dahulu, lalu bila perlu aplikasi spreadsheet dengan eksekusi macro/formula eksternal dinonaktifkan. Periksa baris anonim dan payload aman sintetis yang diawali `=`, `+`, `-`, `@`, tab, CR, dan LF.

**Hasil yang diharapkan:** Nilai berbahaya diberi awalan pelindung dan diperlakukan sebagai teks; nama/email/NIM anonim, IP, hash tracking, kode tracking, serta catatan internal tidak ada di CSV. Role tanpa izin tidak dapat mengekspor.

**PASS/FAIL:** Catat keberadaan sanitasi dan kategori kolom yang dikecualikan; jangan melampirkan CSV mentah karena dapat mengandung data privat.

**Pemulihan:** Jika data pribadi terikut, hentikan distribusi file, hapus salinan lokal hanya jika salinan itu memang dibuat oleh penguji dan kebijakan retensi mengizinkan, lalu lapor ke operator. Jangan mengubah atau menghapus data sumber.

## G. Error 403, 404, dan 500

**Prasyarat:** Staging dengan debug mati. Untuk 403 gunakan role yang memang tidak berizin. Untuk 500, operator harus menyediakan fault injection khusus staging yang telah disetujui; jangan membuat error secara sengaja di produksi.

**Tindakan:** Buka route admin terlarang sebagai role uji (403), slug fiktif/draft (404), dan URL fault injection staging yang disediakan operator (500). Periksa teks, status, dan respons HTML. Jangan membuka stack trace atau mengirim detail internal ke laporan.

**Hasil yang diharapkan:** Status sesuai (403/404/500), pesan aman berbahasa Indonesia, tidak ada stack trace, path server, SQL, secret, atau konfigurasi internal.

**PASS/FAIL:** Catat status, ada/tidaknya detail internal, dan apakah tombol navigasi error berfungsi. 500 tanpa fault injection yang aman ditandai `BLOCKED`.

**Pemulihan:** Matikan fault injection staging segera setelah tes melalui operator dan verifikasi route itu tidak tersedia lagi. Jika detail internal terlihat, jangan sebarkan screenshot; batasi akses bukti dan laporkan sebagai insiden.

## H. Responsivitas dan aksesibilitas

**Prasyarat:** Browser desktop dengan DevTools responsive mode, halaman staging lulus A/B, dan akun sintetis untuk halaman admin. Gunakan tinggi viewport berikut agar hasil dapat diulang.

| Lebar × tinggi | Area yang diperiksa |
|---|---|
| 320 × 720 | Beranda, menu mobile, form aspirasi, login |
| 375 × 812 | Berita/detail, gambar, form aspirasi |
| 768 × 1024 | Navigasi, daftar konten, tabel laporan |
| 1024 × 768 | Dashboard, tabel, form admin |
| 1440 × 900 | Seluruh layout publik dan admin |

**Tindakan:** Pada setiap viewport, cek overflow horizontal yang tidak disengaja, menu mobile, gambar/placeholder, form, tabel laporan, label form, heading, alt gambar, tombol/link, serta FAQ. Navigasikan tanpa mouse dengan Tab, Shift+Tab, Enter, Space, dan Escape; pastikan focus terlihat dan accordion FAQ dapat dioperasikan keyboard jika interaktif.

**Hasil yang diharapkan:** Konten dapat digunakan tanpa terpotong/scroll horizontal tak disengaja; tabel punya cara akses di layar kecil; form tidak terpotong; fokus jelas; kontrol memiliki label; gambar informatif memiliki alt.

**PASS/FAIL:** Catat viewport, halaman, overflow `ya/tidak`, keyboard `lulus/gagal`, dan masalah visual dengan screenshot yang sudah disamarkan.

**Pemulihan:** Jika ada masalah, catat ukuran dan selector/area tanpa data pengguna; jangan mengubah CSS di DevTools sebagai perbaikan. Kembalikan viewport ke ukuran awal setelah selesai.

## I. CSRF melalui browser

**Prasyarat:** Staging A lulus dan operator menyetujui pengujian request gagal. Gunakan route/form staging dengan payload sintetis yang tidak menimbulkan efek samping jika validasi lolos; jangan lakukan ini di produksi.

**Tindakan:** Buka formulir normal dan pastikan request browser berisi token CSRF. Dari DevTools/Network replay request POST yang sama tanpa field `_token` atau dengan token acak. Gunakan route login dengan kredensial uji kosong/salah atau endpoint form staging yang disetujui; hindari aspirasi valid pada percobaan ini.

**Hasil yang diharapkan:** Request tanpa token sah mendapat 419/penolakan CSRF dan tidak membuat perubahan data. Form normal dengan token sesi yang sah tetap dapat dikirim sesuai validasi.

**PASS/FAIL:** Catat status respons dan konfirmasi operator bahwa tidak ada record samping; jangan menyimpan request mentah/header/cookie/token.

**Pemulihan:** Jika request tanpa token mengubah data, hentikan pengujian tulis dan laporkan. Hapus hanya artefak sintetis yang pasti dibuat oleh request tersebut, dan hanya setelah operator mengonfirmasi identitasnya di staging.

## J. Sitemap dan robots.txt

**Prasyarat:** Halaman publik dan tes slug selesai; ada satu draft sintetis yang masih draft.

**Tindakan:** Buka `/sitemap.xml` dan `/robots.txt` di staging. Periksa sitemap hanya berisi URL publik yang semestinya serta tidak berisi admin, draft, tiket, atau aspirasi privat. Periksa robots memuat arahan yang sesuai.

**Hasil yang diharapkan:** XML valid dan URL absolut mengarah ke host staging; robots mengiklankan sitemap serta boleh melarang crawl area sensitif. Membuka route admin secara langsung tetap perlu login/otorisasi tanpa mengandalkan robots.

**PASS/FAIL:** Catat validitas XML, host, dan kategori URL yang masuk/absen. Jangan menganggap robots sebagai keamanan.

**Pemulihan:** Jika draft/private URL muncul, simpan bukti tersamarkan dan minta operator memperbaiki sitemap; jangan mengubah aturan akses untuk menyembunyikan temuan.

## K. SMTP staging — memerlukan persetujuan

**Prasyarat:** Persetujuan eksplisit pemilik proyek, mailbox staging yang tepat untuk admin dan pelapor sintetis, kredensial SMTP staging, konfigurasi staging terpisah, dan operator yang dapat memeriksa log dengan aman. Tanpa seluruh syarat ini, tandai `BLOCKED` dan jangan mengirim.

**Tindakan:** Setelah persetujuan, lakukan satu aspirasi anonim untuk menguji email admin dan satu aspirasi terbuka yang memakai alamat mailbox staging yang disetujui. Pastikan penerima admin/pelapor tepat. Periksa subjek dan isi, pastikan admin tidak menerima tracking code dan identitas anonim tidak muncul. Setelah perubahan status/respons, periksa notifikasi pelapor. Pastikan record sudah committed dan request berhasil sebelum pesan diterima. Untuk kegagalan SMTP, gunakan mailbox/transport staging yang dapat ditolak secara sengaja; verifikasi penyimpanan aspirasi tetap berhasil dan operator hanya mencatat kategori error, bukan detail kredensial/PII.

**Hasil yang diharapkan:** Pesan hanya menuju mailbox staging yang disetujui; isi sesuai kebijakan privasi; kegagalan transport tidak membatalkan penyimpanan. Fake mailer pada test suite hanya membuktikan pemanggilan mailable, bukan pengiriman SMTP atau penerimaan email nyata. Implementasi `DB::afterCommit` telah ditinjau, tetapi pengiriman SMTP nyata belum dibuktikan.

**PASS/FAIL:** Catat nama template, jumlah pesan, penerima sebagai label `admin-staging`/`reporter-staging`, dan hasil privasi; jangan mencatat alamat lengkap, body, tracking code, atau kredensial.

**Pemulihan:** Hentikan pengiriman jika penerima salah/bocor; minta operator mematikan konfigurasi SMTP staging dan meninjau log dengan akses terbatas. Jangan hapus log.

## L. Restore backup terisolasi — memerlukan arsip dan DB khusus

**Prasyarat:** Backup dikenal dan disetujui operator, checksum yang cocok, akses ke server MariaDB staging terpisah, direktori restore baru yang kosong, serta konfirmasi tertulis bahwa host dan database tujuan bukan produksi dan bukan `portal_bem`. Bila `portal_bem_restore` sudah ada atau identitas server meragukan, hentikan; jangan menjalankan restore.

**Tindakan:** Verifikasi checksum dan asal arsip; periksa daftar isi arsip storage tanpa mengekstrak ke lokasi aktif. Pada instance staging terpisah, pastikan database tujuan kosong dan gunakan nama `portal_bem_restore`. Gunakan prosedur README dengan kredensial staging yang tidak diletakkan pada command line. Ekstrak storage ke direktori restore terpisah. Jangan gunakan `DROP DATABASE`, `db:wipe`, `migrate:fresh`, atau target `portal_bem`.

**Hasil yang diharapkan:** Tabel inti tersedia, integritas/checksum cocok, jumlah data sesuai catatan backup, gambar dapat dibuka dan cocok dengan referensi data, lalu aplikasi staging lulus smoke test dengan debug mati.

**PASS/FAIL:** Catat ID backup non-rahasia, waktu mulai/selesai, nama target, hasil checksum, cek tabel/data/gambar, serta selisih. Jangan sertakan data aspirasi atau kredensial.

**Pemulihan:** Jika gagal, hentikan aplikasi staging dan simpan log secara aman untuk operator. Jangan mengulang di target yang tidak kosong. Hapus hanya direktori/database uji yang dibuat untuk kegiatan ini, setelah operator memastikan nama/host target secara eksplisit; jika ada keraguan, biarkan dan minta DBA menangani. Jangan pernah menghapus backup sumber.

## Rekap hasil manual

| ID | Status (PASS/FAIL/BLOCKED) | Tanggal/waktu | Catatan non-rahasia / referensi bukti tersamarkan |
|---|---|---|---|
| A |  |  |  |
| B |  |  |  |
| C |  |  |  |
| D |  |  |  |
| E |  |  |  |
| F |  |  |  |
| G |  |  |  |
| H |  |  |  |
| I |  |  |  |
| J |  |  |  |
| K |  |  |  |
| L |  |  |  |
