<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

#[Fillable([
    'name',
    'slug',
    'organization',
    'department',
    'status',
    'publication_status',
    'period',
    'description',
    'objectives',
    'targets',
    'indicators',
    'timeline',
    'pic',
    'published_at',
])]
class WorkProgram extends Model
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
            'published_at' => 'datetime',
        ];
    }

    /**
     * Scope untuk program kerja yang dipublikasikan.
     */
    public function scopePublished($query)
    {
        return $query->where('publication_status', 'published');
    }

    /**
     * Dapatkan daftar target dalam bentuk array (kompatibel dengan view publik).
     *
     * @return array<int, string>
     */
    public function getTargetsAttribute($value): array
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
     * Dapatkan daftar target dalam bentuk array.
     *
     * @return array<int, string>
     */
    public function getTargetsListAttribute(): array
    {
        return $this->targets;
    }

    /**
     * Hasilkan slug unik.
     */
    public static function generateUniqueSlug(string $name, ?int $excludeId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $counter = 1;

        while (static::where('slug', $slug)->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))->exists()) {
            $slug = $base . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
