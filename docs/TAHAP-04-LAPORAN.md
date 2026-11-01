# Tahap 04 — Riwayat Chat, Unread, dan Dashboard Admin

## Implementasi
- Admin ChatController + routes + views.
- ChatController messages endpoint untuk polling/history.
- UserRole/middleware menambah modul chat.
- Unread ditandai via read_at pada load messages.

## Permission
- super_admin, admin, advokasi: chat.
- editor: tidak.

## Test
- php artisan test: 180 passed, 727 assertions.

## Belum selesai
- Widget polling integration UI.
- Unread badge frontend.
- Concurrency handling assignment.
