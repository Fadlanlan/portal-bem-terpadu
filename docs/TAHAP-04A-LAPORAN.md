# Tahap 04A — Finalisasi Polling, Unread Badge, Assignment Concurrency

## Perubahan
- chat-widget.blade.php: polling, unread badge, loadMessages, dedup by ID.
- ChatController: read_at pada messages JSON.
- ChatAdminController::assign: transaksi + lockForUpdate + conflict handling.

## Hasil
- php artisan test: 180 passed, 727 assertions.
- npm run build: sukses.

## Catatan
- Concurrency test paralel tidak dijalankan; logic lock diimplementasikan.
