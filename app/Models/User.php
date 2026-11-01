<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_active' => 'boolean',
        ];
    }

    /**
     * Periksa apakah pengguna adalah Super Admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === UserRole::SuperAdmin;
    }

    /**
     * Periksa apakah pengguna adalah Administrator umum.
     */
    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    /**
     * Periksa apakah pengguna adalah Tim Advokasi.
     */
    public function isAdvocacy(): bool
    {
        return $this->role === UserRole::Advocacy;
    }

    /**
     * Periksa apakah pengguna adalah Editor Konten.
     */
    public function isEditor(): bool
    {
        return $this->role === UserRole::Editor;
    }

    /**
     * Periksa apakah pengguna berhak mengakses modul tertentu.
     */
    public function canAccessModule(string $module): bool
    {
        if (! $this->is_active) {
            return false;
        }

        return $this->role?->canAccessModule($module) ?? false;
    }

    /**
     * Dapatkan label role pengguna.
     */
    public function getRoleLabelAttribute(): string
    {
        return $this->role instanceof UserRole ? $this->role->label() : (string) $this->role;
    }

    /**
     * Dapatkan tipe badge visual untuk role.
     */
    public function getRoleBadgeTypeAttribute(): string
    {
        return $this->role instanceof UserRole ? $this->role->badgeType() : 'default';
    }
}
