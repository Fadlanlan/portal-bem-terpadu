<?php

namespace App\Enums;

enum UserRole: string
{
    case SuperAdmin = 'super_admin';
    case Admin = 'admin';
    case Advocacy = 'advokasi';
    case Editor = 'editor';

    /**
     * Dapatkan label bahasa Indonesia untuk role.
     */
    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Administrator',
            self::Admin => 'Administrator',
            self::Advocacy => 'Tim Advokasi',
            self::Editor => 'Editor Konten',
        };
    }

    /**
     * Dapatkan warna/tipe badge tampilan visual.
     */
    public function badgeType(): string
    {
        return match ($this) {
            self::SuperAdmin => 'danger',
            self::Admin => 'info',
            self::Advocacy => 'warning',
            self::Editor => 'default',
        };
    }

    /**
     * Dapatkan daftar opsi untuk formulir.
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        return [
            self::SuperAdmin->value => self::SuperAdmin->label(),
            self::Admin->value => self::Admin->label(),
            self::Advocacy->value => self::Advocacy->label(),
            self::Editor->value => self::Editor->label(),
        ];
    }

    /**
     * Dapatkan semua nilai string role.
     *
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Periksa apakah role ini memiliki akses ke modul tertentu.
     */
    public function canAccessModule(string $module): bool
    {
        return match ($this) {
            self::SuperAdmin => true,
            self::Admin => in_array($module, ['dashboard', 'aspirations', 'articles', 'programs', 'events', 'organizations', 'bem_members', 'chat']),
            self::Advocacy => in_array($module, ['dashboard', 'aspirations', 'chat']),
            self::Editor => in_array($module, ['dashboard', 'articles', 'programs', 'events', 'organizations', 'bem_members']),
        };
    }
}
