<?php

namespace App\Models;

use App\Enums\AspirationCategory;
use App\Enums\AspirationStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'ticket_number',
    'tracking_code_hash',
    'category',
    'subject',
    'message',
    'is_anonymous',
    'reporter_name',
    'reporter_email',
    'reporter_student_id',
    'status',
    'admin_response',
    'submitted_at',
    'resolved_at',
])]
#[Hidden(['tracking_code_hash'])]
class Aspiration extends Model
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
            'is_anonymous' => 'boolean',
            'submitted_at' => 'datetime',
            'resolved_at' => 'datetime',
            'category' => AspirationCategory::class,
            'status' => AspirationStatus::class,
        ];
    }

    /**
     * Riwayat status penanganan aspirasi.
     *
     * @return HasMany<AspirationStatusHistory, $this>
     */
    public function statusHistories(): HasMany
    {
        return $this->hasMany(AspirationStatusHistory::class, 'aspiration_id')->orderBy('created_at', 'asc');
    }

    /**
     * Dapatkan label bahasa Indonesia untuk kategori.
     */
    public function getCategoryLabelAttribute(): string
    {
        if ($this->category instanceof AspirationCategory) {
            return $this->category->label();
        }

        return AspirationCategory::tryFrom((string) $this->category)?->label() ?? (string) $this->category;
    }

    /**
     * Dapatkan label bahasa Indonesia untuk status.
     */
    public function getStatusLabelAttribute(): string
    {
        if ($this->status instanceof AspirationStatus) {
            return $this->status->label();
        }

        return AspirationStatus::tryFrom((string) $this->status)?->label() ?? (string) $this->status;
    }

    /**
     * Dapatkan tipe badge tampilan visual untuk status.
     */
    public function getStatusBadgeTypeAttribute(): string
    {
        if ($this->status instanceof AspirationStatus) {
            return $this->status->badgeType();
        }

        return AspirationStatus::tryFrom((string) $this->status)?->badgeType() ?? 'default';
    }
}
