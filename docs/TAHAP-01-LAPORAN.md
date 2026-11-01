# Tahap 01 — Fondasi AI Live Chat dan Konfigurasi Gemini API

## Ringkasan perubahan

- Konfigurasi Gemini ditambahkan ke `.env.example` dan `config/services.php`.
- Service `App\Services\Chat\GeminiChatService` dibuat menggunakan Laravel HTTP Client.
- Migration non-destruktif untuk `chat_conversations`, `chat_messages`, `chat_faqs`.
- Model Eloquent `ChatConversation`, `ChatMessage`, `ChatFaq`.
- Pengujian unit untuk service Gemini (mock HTTP).
- Tidak ada perubahan auth/RBAC/mail/database existing.

## File baru/ubah

Baru:
- app/Services/Chat/GeminiChatService.php
- app/Models/ChatConversation.php
- app/Models/ChatMessage.php
- app/Models/ChatFaq.php
- database/migrations/2026_11_01_113801_create_chat_conversations_table.php
- database/migrations/2026_11_01_113802_create_chat_messages_table.php
- database/migrations/2026_11_01_113803_create_chat_faqs_table.php
- tests/Unit/GeminiChatServiceTest.php
- docs/TAHAP-01-LAPORAN.md

Diubah:
- .env.example
- config/services.php

## Desain tabel

chat_conversations: user_id nullable (guest/login), identity_mode, anonymous_token_hash (hashed unique), status, handler_mode, assigned_admin_id, last_message_at, unread counters, timestamps.
chat_messages: conversation_id, sender_user_id nullable, sender_type, message_type, body, sources JSON, read_at, timestamps.
chat_faqs: slug unique, question, answer, is_published, sort_order, timestamps.

## Status konfigurasi Gemini

- API key dibaca dari env melalui config('services.gemini.api_key').
- Tidak ada key di kode/repo.
- User perlu mengisi GEMINI_API_KEY, GEMINI_MODEL, GEMINI_BASE_URL di .env.
- Tidak ada verifikasi panggilan nyata (tidak ada key di lingkungan ini).

## Pengujian

- php artisan test: 173 passed, 711 assertions.
- npm run build: sukses.
- view:cache + view:clear: sukses.
- Migration: 3 tabel dibuat tanpa error.

## Belum diverifikasi

- Endpoint/model Gemini aktual (perlu akses jaringan/dokumentasi dan key user).
- Panggilan nyata ke Gemini.
- Role petugas chat (belum diimplementasikan).
- UI publik/admin chat (tahap berikutnya).

## Instruksi manual

1. Isi .env:
   GEMINI_API_KEY=<key milik sendiri>
   GEMINI_MODEL=<model tersedia>
   GEMINI_BASE_URL=https://generativelanguage.googleapis.com/v1beta
2. Jalankan php artisan config:clear
3. Jalankan php artisan test

## Rekomendasi

Tahap 02: implement pencarian pengetahuan lokal (Article, WorkProgram, Event, Organization, BemMember, ChatFaq) dan service chat backend.
