<?php

namespace App\Models;

use App\Enums\AspirationStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'aspiration_id',
    'old_status',
    'new_status',
    'note',
    'changed_by',
])]
class AspirationStatusHistory extends Model
{
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'old_status' => AspirationStatus::class,
            'new_status' => AspirationStatus::class,
        ];
    }

    /**
     * Relasi ke entitas aspirasi induk.
     *
     * @return BelongsTo<Aspiration, $this>
     */
    public function aspiration(): BelongsTo
    {
        return $this->belongsTo(Aspiration::class, 'aspiration_id');
    }

    /**
     * Dapatkan label bahasa Indonesia untuk status baru.
     */
    public function getNewStatusLabelAttribute(): string
    {
        if ($this->new_status instanceof AspirationStatus) {
            return $this->new_status->label();
        }

        return AspirationStatus::tryFrom((string) $this->new_status)?->label() ?? (string) $this->new_status;
    }

    /**
     * Dapatkan label bahasa Indonesia untuk status lama.
     */
    public function getOldStatusLabelAttribute(): ?string
    {
        if (! $this->old_status) {
            return null;
        }

        if ($this->old_status instanceof AspirationStatus) {
            return $this->old_status->label();
        }

        return AspirationStatus::tryFrom((string) $this->old_status)?->label() ?? (string) $this->old_status;
    }

    /**
     * Relasi ke pengguna/pengelola yang melakukan perubahan status.
     *
     * @return BelongsTo<User, $this>
     */
    public function changedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
