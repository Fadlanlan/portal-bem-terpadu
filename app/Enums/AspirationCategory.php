<?php

namespace App\Enums;

enum AspirationCategory: string
{
    case Academic = 'akademik';
    case Facility = 'fasilitas';
    case Administration = 'administrasi';
    case Organization = 'organisasi';
    case Activity = 'kegiatan';
    case Welfare = 'kesejahteraan';
    case Feedback = 'kritik_saran';
    case Other = 'lainnya';

    /**
     * Dapatkan label bahasa Indonesia untuk kategori.
     */
    public function label(): string
    {
        return match ($this) {
            self::Academic => 'Akademik',
            self::Facility => 'Fasilitas Kampus',
            self::Administration => 'Pelayanan Administrasi',
            self::Organization => 'Organisasi Mahasiswa',
            self::Activity => 'Kegiatan & Program Kerja',
            self::Welfare => 'Beasiswa & Kesejahteraan Mahasiswa',
            self::Feedback => 'Kritik & Saran Umum',
            self::Other => 'Lainnya',
        };
    }

    /**
     * Dapatkan daftar opsi untuk elemen select formulir.
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        return [
            self::Academic->value => self::Academic->label(),
            self::Facility->value => self::Facility->label(),
            self::Administration->value => self::Administration->label(),
            self::Organization->value => self::Organization->label(),
            self::Activity->value => self::Activity->label(),
            self::Welfare->value => self::Welfare->label(),
            self::Feedback->value => self::Feedback->label(),
            self::Other->value => self::Other->label(),
        ];
    }

    /**
     * Dapatkan semua nilai string kategori yang sah.
     *
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
