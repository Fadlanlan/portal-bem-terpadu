<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

#[Fillable([
    'title',
    'slug',
    'category',
    'summary',
    'description',
    'location',
    'started_at',
    'ended_at',
    'organizer',
    'registration_url',
    'is_registration_open',
    'speakers',
    'agenda_points',
    'status',
])]
class Event extends Model
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
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
            'is_registration_open' => 'boolean',
        ];
    }

    /**
     * Scope untuk agenda yang dipublikasikan.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope untuk agenda mendatang.
     */
    public function scopeUpcoming($query)
    {
        return $query->published()->where('started_at', '>=', now()->startOfDay());
    }

    /**
     * Format tanggal tampilan Indonesia.
     */
    public function getDateDisplayAttribute(): string
    {
        return $this->started_at ? $this->started_at->translatedFormat('l, d F Y') : '-';
    }

    /**
     * Format tanggal ringkas (kompatibel dengan view beranda).
     */
    public function getDateAttribute(): string
    {
        return $this->started_at ? $this->started_at->translatedFormat('d M Y') : '-';
    }

    /**
     * Format jam pelaksanaan.
     */
    public function getTimeDisplayAttribute(): string
    {
        if (! $this->started_at || ! $this->ended_at) {
            return '-';
        }

        return $this->started_at->format('H:i') . ' - ' . $this->ended_at->format('H:i') . ' WIB';
    }

    /**
     * Format jam (kompatibel dengan view beranda).
     */
    public function getTimeAttribute(): string
    {
        return $this->time_display;
    }

    /**
     * Status apakah agenda mendatang.
     */
    public function getIsUpcomingAttribute(): bool
    {
        return $this->started_at && $this->started_at->gte(now()->startOfDay());
    }

    /**
     * Dapatkan daftar pembicara sebagai array (kompatibel dengan view publik).
     *
     * @return array<int, string>
     */
    public function getSpeakersAttribute($value): array
    {
        if (empty($value)) {
            return [];
        }

        $decoded = json_decode((string) $value, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        return array_values(array_filter(array_map('trim', explode("\n", (string) $value))));
    }

    /**
     * Dapatkan poin agenda sebagai array (kompatibel dengan view publik).
     *
     * @return array<int, string>
     */
    public function getAgendaPointsAttribute($value): array
    {
        if (empty($value)) {
            return [];
        }

        $decoded = json_decode((string) $value, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        return array_values(array_filter(array_map('trim', explode("\n", (string) $value))));
    }

    /**
     * Dapatkan tanggal format mentah Y-m-d.
     */
    public function getDateRawAttribute(): string
    {
        return $this->started_at ? $this->started_at->format('Y-m-d') : '';
    }

    /**
     * Dapatkan daftar pembicara sebagai array.
     *
     * @return array<int, string>
     */
    public function getSpeakersListAttribute(): array
    {
        return $this->speakers;
    }

    /**
     * Dapatkan poin agenda sebagai array.
     *
     * @return array<int, string>
     */
    public function getAgendaPointsListAttribute(): array
    {
        return $this->agenda_points;
    }

    /**
     * Hasilkan slug unik.
     */
    public static function generateUniqueSlug(string $title, ?int $excludeId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $counter = 1;

        while (static::where('slug', $slug)->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))->exists()) {
            $slug = $base . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
