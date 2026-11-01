<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

#[Fillable([
    'code',
    'name',
    'slug',
    'level',
    'role_type',
    'description',
    'leader_name',
    'period',
    'logo',
    'functions',
    'structure_overview',
    'sort_order',
    'status',
])]
class Organization extends Model
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
            'sort_order' => 'integer',
        ];
    }

    /**
     * Scope untuk organisasi yang dipublikasikan.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')->orderBy('sort_order', 'asc');
    }

    /**
     * Dapatkan fungsi lembaga sebagai array (kompatibel dengan view publik).
     *
     * @return array<int, string>
     */
    public function getFunctionsAttribute($value): array
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
     * Dapatkan ringkasan struktur sebagai array (kompatibel dengan view publik).
     *
     * @return array<int, string>
     */
    public function getStructureOverviewAttribute($value): array
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
     * Dapatkan fungsi lembaga sebagai array.
     *
     * @return array<int, string>
     */
    public function getFunctionsListAttribute(): array
    {
        return $this->functions;
    }

    /**
     * Dapatkan ringkasan struktur sebagai array.
     *
     * @return array<int, string>
     */
    public function getStructureOverviewListAttribute(): array
    {
        return $this->structure_overview;
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
