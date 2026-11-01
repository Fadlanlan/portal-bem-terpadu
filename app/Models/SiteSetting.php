<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

#[Fillable(['key', 'value', 'type', 'group'])]
class SiteSetting extends Model
{
    use HasFactory;

    /**
     * Skema warna default identitas visual Portal BEM Terpadu.
     *
     * @var array<string, string>
     */
    public const DEFAULT_THEME = [
        'primary_color' => '#0f172a',
        'secondary_color' => '#1e293b',
        'accent_color' => '#f59e0b',
        'background_color' => '#f8fafc',
        'surface_color' => '#ffffff',
        'text_primary_color' => '#0f172a',
        'text_secondary_color' => '#475569',
    ];

    /**
     * Preset tema profesional yang siap digunakan.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function getPresets(): array
    {
        return [
            'default' => [
                'name' => 'Integritas & Aksi (Default)',
                'description' => 'Identitas resmi BEM Terpadu bernuansa Navy, Slate, dan Emas.',
                'colors' => self::DEFAULT_THEME,
            ],
            'emerald' => [
                'name' => 'Harmoni Hijau Kampus',
                'description' => 'Kombinasi hijau zamrud elegan, dedaunan segar, dan aksen hangat.',
                'colors' => [
                    'primary_color' => '#064e3b',
                    'secondary_color' => '#065f46',
                    'accent_color' => '#d97706',
                    'background_color' => '#f0fdf4',
                    'surface_color' => '#ffffff',
                    'text_primary_color' => '#064e3b',
                    'text_secondary_color' => '#374151',
                ],
            ],
            'royal_blue' => [
                'name' => 'Sinergi Akademik',
                'description' => 'Biru bahari terpadu dengan oranye energetik untuk kesan modern.',
                'colors' => [
                    'primary_color' => '#1e3a8a',
                    'secondary_color' => '#1d4ed8',
                    'accent_color' => '#ea580c',
                    'background_color' => '#f8fafc',
                    'surface_color' => '#ffffff',
                    'text_primary_color' => '#1e293b',
                    'text_secondary_color' => '#475569',
                ],
            ],
            'crimson' => [
                'name' => 'Semangat Progresif',
                'description' => 'Warna marun berani dengan aksen emas yang tegas dan berwibawa.',
                'colors' => [
                    'primary_color' => '#881337',
                    'secondary_color' => '#9f1239',
                    'accent_color' => '#f59e0b',
                    'background_color' => '#fff1f2',
                    'surface_color' => '#ffffff',
                    'text_primary_color' => '#4c0519',
                    'text_secondary_color' => '#4b5563',
                ],
            ],
            'deep_tech' => [
                'name' => 'Modern Digital',
                'description' => 'Kombinasi gelap futuristik dengan aksen cyan tajam.',
                'colors' => [
                    'primary_color' => '#090d16',
                    'secondary_color' => '#1e1b4b',
                    'accent_color' => '#06b6d4',
                    'background_color' => '#f8fafc',
                    'surface_color' => '#ffffff',
                    'text_primary_color' => '#0f172a',
                    'text_secondary_color' => '#475569',
                ],
            ],
        ];
    }

    /**
     * Dapatkan nilai pengaturan berdasarkan key.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        try {
            if (! Schema::hasTable('site_settings')) {
                return $default;
            }

            $setting = static::where('key', $key)->first();

            return $setting ? $setting->value : $default;
        } catch (\Throwable) {
            return $default;
        }
    }

    /**
     * Simpan atau perbarui nilai pengaturan.
     */
    public static function set(string $key, mixed $value, string $group = 'general', string $type = 'string'): self
    {
        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'group' => $group,
                'type' => $type,
            ]
        );
    }

    /**
     * Dapatkan seluruh warna tema aktif dengan fallback ke default.
     *
     * @return array<string, string>
     */
    public static function getThemeSettings(): array
    {
        $theme = self::DEFAULT_THEME;

        try {
            if (Schema::hasTable('site_settings')) {
                $saved = static::where('group', 'theme')->pluck('value', 'key')->toArray();
                foreach ($theme as $colorKey => $defaultVal) {
                    if (isset($saved[$colorKey]) && self::isValidHex($saved[$colorKey])) {
                        $theme[$colorKey] = $saved[$colorKey];
                    }
                }
            }
        } catch (\Throwable) {
            // Gunakan fallback default jika tabel belum tersedia
        }

        return $theme;
    }

    /**
     * Validasi kode warna heksadesimal 6 digit (misal: #0f172a).
     */
    public static function isValidHex(string $color): bool
    {
        return (bool) preg_match('/^#([a-fA-F0-9]{6})$/', trim($color));
    }

    /**
     * Pulihkan seluruh pengaturan tema ke skema warna default.
     */
    public static function resetTheme(): void
    {
        try {
            if (Schema::hasTable('site_settings')) {
                static::where('group', 'theme')->delete();
            }
        } catch (\Throwable) {
            // Abaikan kegagalan jika tabel belum ada
        }
    }

    /**
     * Dapatkan URL publik dari logo kustom aktif, atau null jika memakai logo bawaan.
     */
    public static function getLogoUrl(): ?string
    {
        try {
            $logoPath = self::get('site_logo');

            if ($logoPath && Storage::disk('public')->exists($logoPath)) {
                $driver = config('filesystems.disks.public.driver');
                if ($driver === 'local') {
                    return asset('storage/' . ltrim($logoPath, '/'));
                }

                return Storage::disk('public')->url($logoPath);
            }
        } catch (\Throwable) {
            return null;
        }

        return null;
    }
}
