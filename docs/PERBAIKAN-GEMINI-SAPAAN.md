# Perbaikan Gemini Live Chat untuk Sapaan

## Penyebab

`ChatConversationService::sendMessage()` sebelumnya hanya memanggil Gemini bila pencarian sumber Portal BEM menghasilkan sumber. Sapaan seperti `hai` tidak cocok dengan sumber publik, sehingga percakapan langsung diberi respons “informasi belum tersedia” dan dialihkan ke petugas. Akibatnya Gemini tidak pernah menerima sapaan tersebut. Audit lanjutan juga menemukan deteksi pertanyaan resmi terlalu luas: kata `BEM` saja menandai pertanyaan definisi umum seperti `apa itu BEM?` sebagai pertanyaan resmi.

## Perubahan

- `app/Services/Chat/ChatConversationService.php`: membedakan percakapan umum dari permintaan fakta Portal BEM yang spesifik. Sapaan, pertanyaan umum, definisi BEM, dan percakapan tentang konsep organisasi/universitas dapat dijawab Gemini tanpa sumber lokal. Pertanyaan resmi spesifik seperti jadwal, kepengurusan, atau informasi Portal tetap perlu sumber publik yang cocok dan tidak diteruskan ke Gemini jika sumber tidak ada. Kegagalan API untuk percakapan umum menghasilkan pesan koneksi yang jujur, bukan pesan ketiadaan sumber. Status handoff maupun mode petugas menghentikan panggilan AI.
- `app/Services/Chat/GeminiChatService.php`: tetap memakai model `services.gemini.model` dan endpoint `models/{model}:generateContent`; mengirim key melalui `x-goog-api-key`; membaca teks dari kandidat yang tersedia; dan mencatat model, status HTTP, durasi, serta kategori kegagalan tanpa key, teks percakapan, token anonim, atau isi lampiran. Key/izin tidak valid, kuota, model tak ditemukan, gangguan jaringan/timeout, error provider, dan respons tanpa teks diklasifikasikan secara internal.
- Tes unit dan feature di `tests/Unit/GeminiChatServiceTest.php`, `tests/Feature/ChatConversationServiceTest.php`, dan `tests/Feature/ChatApiTest.php` memverifikasi format request, delapan sapaan/pertanyaan umum, sumber resmi, tidak adanya fakta resmi tanpa sumber, kegagalan API, timeout, parsing kandidat, tidak terkirimnya data aspirasi/status/catatan internal, endpoint widget, dan penghentian balasan saat handoff.

## Validasi

- `php artisan test`: lulus, 192 tes dan 770 assertion setelah validasi lanjutan.
- `npm.cmd run build`: lulus, Vite menghasilkan build produksi.
- `php artisan route:list --path=chat`: route publik chat, lampiran, handoff, dan route admin terdaftar. Middleware throttle pada route publik tetap terpasang.
- `git diff --check`: lulus.
- API Gemini nyata belum diuji. Tes HTTP menggunakan fake, jadi hasil tes tidak membuktikan koneksi atau kredensial Gemini nyata berhasil.

## Verifikasi manual dengan konfigurasi lokal

1. Pastikan `.env` lokal berisi `GEMINI_API_KEY` dan `GEMINI_MODEL` yang memang dipilih pengguna. Key tidak perlu dikirim lewat chat dan jangan menyalinnya ke log atau laporan. Pastikan `GEMINI_BASE_URL` mengarah ke base URL API yang benar.
2. Jika konfigurasi `.env` baru saja diubah dan aplikasi memakai config cache, jalankan `php artisan config:clear`.
3. Buka widget Live Chat dan kirim `hai`. Pastikan balasan sapaan muncul di widget dan percakapan tidak beralih ke handoff.
4. Ajukan pertanyaan umum, lalu pertanyaan faktual Portal BEM yang memiliki sumber publik. Pastikan sumber terkait tampil untuk pertanyaan Portal BEM.
5. Ajukan pertanyaan resmi tanpa sumber. Pastikan bot menyatakan informasi belum tersedia dan mengarahkan ke petugas.
6. Untuk pemeriksaan operasional, tinjau log Laravel. Entri diagnostik hanya memuat nama model, status HTTP, durasi, dan kategori kegagalan; jangan membagikan log yang mungkin berisi data lain.
