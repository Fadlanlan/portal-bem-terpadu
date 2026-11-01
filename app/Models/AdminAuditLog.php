<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

class AdminAuditLog extends Model
{
    use HasFactory;

    /**
     * Tabel hanya memiliki created_at, tidak ada updated_at.
     */
    public const UPDATED_AT = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'action',
        'description',
        'auditable_type',
        'auditable_id',
        'ip_address',
        'user_agent',
        'metadata',
        'created_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Relasi ke pengguna pengelola yang melakukan aksi.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Catat aktivitas audit secara aman tanpa mengganggu transaksi utama.
     *
     * @param array<string, mixed>|null $metadata
     */
    public static function record(
        string $action,
        string $description,
        ?Model $auditable = null,
        ?array $metadata = null,
        ?int $userId = null
    ): ?self {
        try {
            $effectiveUserId = $userId ?? auth()->id();
            $ipAddress = Request::ip();
            $userAgent = Request::header('User-Agent');

            // Sanitasi metadata: jangan izinkan password, token, tracking_code, hash rahasia tersimpan
            $sanitizedMetadata = null;
            if ($metadata !== null) {
                $sanitizedMetadata = self::sanitizeMetadata($metadata);
            }

            return self::create([
                'user_id' => $effectiveUserId,
                'action' => $action,
                'description' => substr($description, 0, 255),
                'auditable_type' => $auditable ? get_class($auditable) : null,
                'auditable_id' => $auditable?->getKey(),
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent ? substr($userAgent, 0, 500) : null,
                'metadata' => $sanitizedMetadata,
                'created_at' => now(),
            ]);
        } catch (\Throwable) {
            Log::warning('Gagal mencatat audit log aktivitas.', [
                'action' => $action,
            ]);
            return null;
        }
    }

    /**
     * Bersihkan metadata dari informasi sensitif atau rahasia.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private static function sanitizeMetadata(array $data): array
    {
        $sensitiveKeys = [
            'password',
            'password_confirmation',
            'new_password',
            'new_password_confirmation',
            'tracking_code',
            'tracking_code_hash',
            'token',
            'secret',
            'remember_token',
            'message', // Isi lengkap aspirasi tidak disimpan sebagai metadata audit
        ];

        $cleaned = [];
        foreach ($data as $key => $val) {
            if (in_array(strtolower((string) $key), $sensitiveKeys, true)) {
                $cleaned[$key] = '[DIRAHSIAKAN]';
            } elseif (is_array($val)) {
                $cleaned[$key] = self::sanitizeMetadata($val);
            } else {
                $cleaned[$key] = $val;
            }
        }

        return $cleaned;
    }
}
