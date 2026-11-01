<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Fillable([
    'name',
    'position',
    'department',
    'level',
    'period',
    'photo_path',
    'description',
    'sort_order',
    'is_published',
])]
class BemMember extends Model
{
    use HasFactory;

    /**
     * Daftar tingkatan hierarki struktur kepengurusan BEM.
     *
     * @var array<string, string>
     */
    public const LEVELS = [
        'presma_wapresma' => 'Presiden & Wakil Presiden Mahasiswa',
        'bph' => 'Badan Pengurus Harian (Sekretaris & Bendahara)',
        'minister' => 'Menteri & Kepala Biro / Koordinator Bidang',
        'staff' => 'Staf / Anggota Departemen',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Scope untuk pengurus yang dipublikasikan secara resmi di halaman publik.
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true)->orderBy('sort_order', 'asc');
    }

    /**
     * Scope berdasarkan tingkatan hierarki struktural.
     */
    public function scopeTier($query, string $level)
    {
        return $query->where('level', $level)->orderBy('sort_order', 'asc');
    }

    /**
     * Dapatkan label tingkatan struktural dalam bahasa Indonesia.
     */
    public function getLevelLabelAttribute(): string
    {
        return self::LEVELS[$this->level] ?? 'Anggota';
    }

    /**
     * Dapatkan URL publik dari foto profil pengurus, atau fallback avatar SVG.
     */
    public function getPhotoUrlAttribute(): string
    {
        if (! empty($this->photo_path)) {
            if (Str::startsWith($this->photo_path, ['http://', 'https://', '/'])) {
                return $this->photo_path;
            }

            if (Storage::disk('public')->exists($this->photo_path)) {
                return Storage::disk('public')->url($this->photo_path);
            }
        }

        return asset('images/member-placeholder.svg');
    }

    /**
     * Periksa apakah foto kustom valid dan tersimpan di storage publik.
     */
    public function getHasCustomPhotoAttribute(): bool
    {
        if (empty($this->photo_path)) {
            return false;
        }

        if (Str::startsWith($this->photo_path, ['http://', 'https://'])) {
            return true;
        }

        return Storage::disk('public')->exists($this->photo_path);
    }
}
