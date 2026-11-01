# Tahap 02 — Backend Percakapan dan Pencarian Pengetahuan Lokal

## File dibuat/diubah
Baru:
- app/Services/Chat/PortalKnowledgeService.php
- app/Services/Chat/ChatConversationService.php
- tests/Unit/PortalKnowledgeServiceTest.php
- tests/Feature/ChatConversationServiceTest.php
- docs/TAHAP-02-LAPORAN.md

## Alur
1. Validasi pesan.
2. Simpan pesan pengguna.
3. Cari sumber lokal.
4. Panggil Gemini hanya jika sumber ada dan AI terkonfigurasi.
5. Simpan balasan AI + sources.
6. Fallback jujur jika tidak ada sumber/gagal.

## Sumber diizinkan
Article published, WorkProgram published, Event published, Organization published, BemMember published, ChatFaq published.

## Privasi/otorisasi
- canAccess memeriksa user_id untuk account, hash token untuk anonymous.
- Tidak mengirim aspirasi/audit/user private ke Gemini.
- Pesan dibatasi 1000 karakter.

## Hasil pengujian
- php artisan test: 179 passed, 723 assertions.

## Belum selesai
- UI live chat, upload lampiran, assignment petugas, rate limiting route, FAQ CRUD admin.

## Rekomendasi Tahap 03
- Integrasi Gemini context yang lebih ketat, handoff state, unread, admin inbox.
