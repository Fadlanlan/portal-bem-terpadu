<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Fillable([
    'title',
    'slug',
    'excerpt',
    'content',
    'category',
    'image',
    'author_name',
    'status',
    'published_at',
    'read_time',
])]
class Article extends Model
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
     * Scope untuk artikel yang dipublikasikan secara resmi.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /**
     * Dapatkan penulis artikel (kompatibel dengan view publik).
     */
    public function getAuthorAttribute(): string
    {
        return $this->author_name ?: 'Biro Komunikasi & Informasi';
    }

    /**
     * Dapatkan URL publik dari gambar berita, dengan fallback aman ke placeholder lokal.
     */
    public function getImageUrlAttribute(): string
    {
        if (! empty($this->image)) {
            if (Str::startsWith($this->image, ['http://', 'https://', '/'])) {
                return $this->image;
            }

            if (Storage::disk('public')->exists($this->image)) {
                return Storage::disk('public')->url($this->image);
            }
        }

        return asset('images/news-placeholder.svg');
    }

    /**
     * Periksa apakah artikel memiliki berkas gambar kustom yang valid di penyimpanan publik.
     */
    public function getHasCustomImageAttribute(): bool
    {
        if (empty($this->image)) {
            return false;
        }

        if (Str::startsWith($this->image, ['http://', 'https://'])) {
            return true;
        }

        return Storage::disk('public')->exists($this->image);
    }

    /**
     * Dapatkan tanggal publikasi berformat tampilan (kompatibel dengan view publik).
     */
    public function getDateAttribute(): string
    {
        return $this->published_at
            ? $this->published_at->translatedFormat('d F Y')
            : ($this->created_at ? $this->created_at->translatedFormat('d F Y') : '-');
    }

    /**
     * Dapatkan waktu baca artikel (kompatibel dengan view publik).
     */
    public function getReadTimeAttribute(?string $value): string
    {
        if ($value) {
            return $value;
        }

        $wordCount = str_word_count(strip_tags((string) $this->content));
        $minutes = max(1, (int) ceil($wordCount / 200));

        return $minutes . ' menit';
    }

    /**
     * Dapatkan topik/tag terkait artikel.
     *
     * @return array<int, string>
     */
    public function getTagsAttribute(): array
    {
        return [$this->category, 'BEM', 'Informasi'];
    }

    /**
     * Dapatkan perkiraan waktu baca jika belum diisi.
     */
    public function getReadTimeDisplayAttribute(): string
    {
        return $this->read_time;
    }

    /**
     * Otomatisasi pembuatan slug unik.
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
