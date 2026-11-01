# Tahap 03A — Floating Live Chat Widget

## File dibuat/diubah
- resources/views/components/chat-widget.blade.php
- resources/views/layouts/app.blade.php
- app/Http/Controllers/ChatController.php
- routes/web.php

## Endpoint digunakan
- POST /chat/conversations
- POST /chat/conversations/{conversation}/messages

## Fitur berhasil
- Floating button + panel responsif.
- Kirim pesan via fetch dengan CSRF.
- Render teks aman (textContent).
- Sumber sebagai tautan.
- Fallback error.

## Masih menunggu backend
- Riwayat percakapan load ulang.
- Unread indicator nyata.
- Assignment petugas.

## Pengujian
- php artisan test: 179 passed, 723 assertions.
- npm run build: sukses.

## Checklist manual
- Buka halaman publik, klik tombol, kirim pesan, tutup/buka ulang.
- Uji mobile viewport.
- Uji pesan HTML tidak dieksekusi.
