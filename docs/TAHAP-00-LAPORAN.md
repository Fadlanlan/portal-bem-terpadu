# Tahap 00 — Audit Kode Aktual

Tanggal audit: 2026-11-01
Lokasi: `C:\xampp\htdocs\portal-bem`

## Ringkasan

Proyek adalah Laravel 13.39.0, PHP 8.5.12, MariaDB/MySQL (`portal_bem`), Blade + Tailwind + Vite. 165 test lulus (701 assertion). Tidak ada modul chat/AI sebelumnya.

## Stack & Dependensi

- Laravel Framework ^13.17, PHP ^8.3.
- Dependensi pihak ketiga: hanya Laravel standard (tinker). Tidak ada SDK Gemini/HTTP client tambahan.
- Tidak ada konfigurasi Gemini di `config/services.php` atau `.env`.

## Modul & Model Aktual

Model publik yang relevan untuk pengetahuan AI:
- `Article` — status `published`, `published_at <= now()`, slug, konten, kategori.
- `WorkProgram` — `publication_status = published`, `published_at`.
- `Event` — status `published`, `started_at`.
- `Organization` — status `published`, `sort_order`.
- `BemMember` — `is_published = true`, `sort_order`.
- `User` — role enum SuperAdmin/Admin/Advocacy/Editor, `is_active`.
- `AdminAuditLog` — audit log admin.
- `Aspiration`, `AspirationStatusHistory` — data sensitif, tidak boleh menjadi sumber publik.
- `SiteSetting` — pengaturan tema/logo.

Tidak ada model/table untuk FAQ, percakapan/chat, lampiran, atau token anonim. FAQ saat ini statis di `PageController::faq()` (array hardcoded).

## RBAC & Middleware

- Middleware: `AdminAuthenticate`, `EnsureAdminRole` (alias `admin.auth`, `admin.role`).
- Role: SuperAdmin (akses semua), Admin, Advocacy (aspirations), Editor (konten).
- Tidak ada role khusus "petugas chat"; perlu diputuskan apakah memakai role existing atau permission baru (tahap selanjutnya).

## Rute Publik Relevan

- `/berita`, `/berita/{slug}` (news.show)
- `/program-kerja`, `/program-kerja/{slug}` (programs.show)
- `/agenda`, `/agenda/{slug}` (events.show)
- `/organisasi` (organizations.index)
- `/struktur-bem` (bem.structure)
- `/tentang`, `/kontak`, `/faq`
- `/aspirasi` (info, create, tracking)

## Konfigurasi & Keamanan

- `.env` lokal: DB MySQL, session database, queue database, cache database, broadcast log.
- Tidak ada `GEMINI_API_KEY` atau konfigurasi AI.
- `APP_DEBUG=true` lokal.
- Filesystem disk `local` (privat) dan `public` tersedia.
- Tidak ada rate limiter khusus chat.

## Audit Log

- `AdminAuditLog` tersedia; dapat digunakan untuk mencatat assignment/status chat.

## Temuan & Keterbatasan

1. Tidak ada tabel/FAQ dinamis; FAQ statis di controller.
2. Tidak ada infrastruktur chat, handoff, unread, attachment.
3. Tidak ada integrasi Gemini.
4. Perlu desain database baru (tahap 01) tanpa migrasi destruktif.
5. Perlu menentukan role petugas chat (mungkin reuse Admin/Advocacy/Editor atau tambah permission).
6. Perlu URL publik valid untuk sumber (route names tersedia).
7. Tests baseline: 165 passed.

## Rekomendasi untuk Tahap Berikutnya

- Desain tabel percakapan, pesan, lampiran, token anonim, assignment, status handoff, FAQ dinamis.
- Jangan ubah auth/RBAC tanpa alasan terbukti.
- Tambahkan konfigurasi Gemini via env (tanpa key di repo).
- Implementasi pencarian lokal sebagai fondasi sebelum AI.
