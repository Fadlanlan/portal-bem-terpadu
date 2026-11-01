<?php

namespace App\Services;

use App\Enums\AspirationStatus;
use App\Mail\AspirationResponseAddedMail;
use App\Mail\AspirationStatusUpdatedMail;
use App\Mail\NewAspirationAdminMail;
use App\Models\Aspiration;
use App\Models\AspirationStatusHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AspirationService
{
    /**
     * Hasilkan nomor tiket unik dengan mekanisme retry untuk mencegah tabrakan.
     */
    public function generateTicketNumber(): string
    {
        for ($attempt = 0; $attempt < 10; $attempt++) {
            $candidate = 'BEM-' . date('Y') . '-' . strtoupper(Str::random(6));

            if (! Aspiration::where('ticket_number', $candidate)->exists()) {
                return $candidate;
            }
        }

        return 'BEM-' . date('Y') . '-' . strtoupper(Str::random(10));
    }

    /**
     * Hasilkan kode pelacakan rahasia dengan entropi tinggi.
     */
    public function generateTrackingCode(): string
    {
        return 'TRK-' . strtoupper(Str::random(4)) . '-' . strtoupper(Str::random(4)) . '-' . strtoupper(Str::random(4));
    }

    /**
     * Simpan aspirasi baru dan riwayat status awalnya dalam satu transaksi basis data.
     *
     * @param array{category: string, subject: string, message: string, is_anonymous?: bool|int|string, reporter_name?: string|null, reporter_email?: string|null, reporter_student_id?: string|null} $payload
     * @return array{aspiration: Aspiration, ticket_number: string, tracking_code: string}
     */
    public function createAspiration(array $payload): array
    {
        return DB::transaction(function () use ($payload): array {
            $ticketNumber = $this->generateTicketNumber();
            $plainTrackingCode = $this->generateTrackingCode();
            $hashedCode = Hash::make($plainTrackingCode);

            $isAnonymous = filter_var($payload['is_anonymous'] ?? false, FILTER_VALIDATE_BOOLEAN);

            $aspiration = Aspiration::create([
                'ticket_number' => $ticketNumber,
                'tracking_code_hash' => $hashedCode,
                'category' => $payload['category'],
                'subject' => $payload['subject'],
                'message' => $payload['message'],
                'is_anonymous' => $isAnonymous,
                'reporter_name' => $isAnonymous ? null : ($payload['reporter_name'] ?? null),
                'reporter_email' => $isAnonymous ? null : ($payload['reporter_email'] ?? null),
                'reporter_student_id' => $isAnonymous ? null : ($payload['reporter_student_id'] ?? null),
                'status' => AspirationStatus::Submitted,
                'admin_response' => null,
                'submitted_at' => now(),
                'resolved_at' => null,
            ]);

            AspirationStatusHistory::create([
                'aspiration_id' => $aspiration->id,
                'old_status' => null,
                'new_status' => AspirationStatus::Submitted,
                'note' => 'Aspirasi berhasil diterima oleh sistem dan masuk ke dalam antrean verifikasi.',
                'changed_by' => null,
            ]);

            DB::afterCommit(function () use ($aspiration) {
                $adminEmail = config('aspirations.admin_notification_email', env('ADMIN_NOTIFICATION_EMAIL', 'admin@bem.kampus.ac.id'));
                if (! empty($adminEmail)) {
                    try {
                        Mail::to($adminEmail)->send(new NewAspirationAdminMail($aspiration));
                    } catch (\Throwable) {
                        Log::warning('Gagal mengirim email notifikasi aspirasi baru ke admin.');
                    }
                }
            });

            return [
                'aspiration' => $aspiration,
                'ticket_number' => $ticketNumber,
                'tracking_code' => $plainTrackingCode,
            ];
        });
    }

    /**
     * Verifikasi kombinasi nomor tiket dan kode pelacakan rahasia.
     */
    public function verifyTracking(string $ticketNumber, string $trackingCode): ?Aspiration
    {
        $cleanTicket = trim(strtoupper($ticketNumber));
        $cleanCode = trim($trackingCode);

        if ($cleanTicket === '' || $cleanCode === '') {
            return null;
        }

        $aspiration = Aspiration::where('ticket_number', $cleanTicket)
            ->with(['statusHistories'])
            ->first();

        if (! $aspiration) {
            return null;
        }

        if (! Hash::check($cleanCode, $aspiration->tracking_code_hash)) {
            return null;
        }

        return $aspiration;
    }

    /**
     * Perbarui status aspirasi dengan pencatatan riwayat audit dan respons pengelola.
     */
    public function updateAspirationStatus(
        Aspiration $aspiration,
        AspirationStatus $newStatus,
        ?string $note = null,
        ?string $adminResponse = null,
        ?int $changedBy = null
    ): Aspiration {
        return DB::transaction(function () use ($aspiration, $newStatus, $note, $adminResponse, $changedBy): Aspiration {
            $oldStatus = $aspiration->status instanceof AspirationStatus
                ? $aspiration->status
                : AspirationStatus::tryFrom($aspiration->status);

            $updateData = [
                'status' => $newStatus,
            ];

            if ($adminResponse !== null) {
                $updateData['admin_response'] = $adminResponse;
            }

            if ($newStatus === AspirationStatus::Resolved) {
                $updateData['resolved_at'] = now();
            } elseif ($oldStatus === AspirationStatus::Resolved && $newStatus !== AspirationStatus::Resolved) {
                $updateData['resolved_at'] = null;
            }

            $aspiration->update($updateData);

            AspirationStatusHistory::create([
                'aspiration_id' => $aspiration->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'note' => $note ?: ('Status laporan diperbarui menjadi ' . $newStatus->label() . '.'),
                'changed_by' => $changedBy,
            ]);

            DB::afterCommit(function () use ($aspiration, $oldStatus, $newStatus, $note, $adminResponse) {
                // Notifikasi ke pelapor hanya jika bukan anonim dan memiliki surel yang valid
                if (! $aspiration->is_anonymous && ! empty($aspiration->reporter_email)) {
                    // 1. Notifikasi perubahan status
                    if ($oldStatus !== $newStatus) {
                        try {
                            Mail::to($aspiration->reporter_email)->send(
                                new AspirationStatusUpdatedMail($aspiration, $note)
                            );
                        } catch (\Throwable) {
                            Log::warning('Gagal mengirim email pembaruan status aspirasi.');
                        }
                    }

                    // 2. Notifikasi jika tanggapan resmi diberikan/diperbarui
                    if (! empty($adminResponse)) {
                        try {
                            Mail::to($aspiration->reporter_email)->send(
                                new AspirationResponseAddedMail($aspiration, $adminResponse)
                            );
                        } catch (\Throwable) {
                            Log::warning('Gagal mengirim email tanggapan resmi aspirasi.');
                        }
                    }
                }
            });

            return $aspiration->fresh(['statusHistories']);
        });
    }
}
