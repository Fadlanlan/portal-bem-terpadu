<?php

namespace App\Enums;

enum AspirationStatus: string
{
    case Submitted = 'submitted';
    case UnderReview = 'under_review';
    case InProgress = 'in_progress';
    case Resolved = 'resolved';
    case Rejected = 'rejected';

    /**
     * Dapatkan label bahasa Indonesia untuk status.
     */
    public function label(): string
    {
        return match ($this) {
            self::Submitted => 'Diterima',
            self::UnderReview => 'Sedang Ditinjau',
            self::InProgress => 'Sedang Ditindaklanjuti',
            self::Resolved => 'Selesai',
            self::Rejected => 'Ditolak',
        };
    }

    /**
     * Dapatkan tipe badge tampilan visual.
     */
    public function badgeType(): string
    {
        return match ($this) {
            self::Submitted => 'default',
            self::UnderReview => 'info',
            self::InProgress => 'warning',
            self::Resolved => 'success',
            self::Rejected => 'danger',
        };
    }

    /**
     * Dapatkan daftar opsi status.
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        return [
            self::Submitted->value => self::Submitted->label(),
            self::UnderReview->value => self::UnderReview->label(),
            self::InProgress->value => self::InProgress->label(),
            self::Resolved->value => self::Resolved->label(),
            self::Rejected->value => self::Rejected->label(),
        ];
    }

    /**
     * Dapatkan semua nilai string status yang sah.
     *
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
