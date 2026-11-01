<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-admin
                            {--name= : Nama lengkap pengelola}
                            {--email= : Alamat email pengelola}
                            {--role= : Peran akun (super_admin, admin, advokasi, editor)}
                            {--password= : Kata sandi akun}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Buat akun pengelola / administrator pertama Portal BEM Terpadu secara aman';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('====================================================');
        $this->info('  PORTAL BEM TERPADU — INISIALISASI AKUN ADMIN');
        $this->info('====================================================');
        $this->newLine();

        // 1. Nama Lengkap
        $name = $this->option('name') ?: $this->ask('Masukkan Nama Lengkap');
        if (empty(trim((string) $name))) {
            $this->error('Nama lengkap tidak boleh kosong.');
            return self::FAILURE;
        }

        // 2. Alamat Email
        $email = strtolower(trim((string) ($this->option('email') ?: $this->ask('Masukkan Alamat Email'))));
        $emailValidator = Validator::make(['email' => $email], [
            'email' => ['required', 'email', 'unique:users,email'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar dalam sistem.',
        ]);

        if ($emailValidator->fails()) {
            foreach ($emailValidator->errors()->all() as $err) {
                $this->error($err);
            }
            return self::FAILURE;
        }

        // 3. Pilihan Role
        $roleOptions = [
            UserRole::SuperAdmin->value => 'Super Administrator (Akses Penuh)',
            UserRole::Admin->value => 'Administrator Operasional',
            UserRole::Advocacy->value => 'Tim Advokasi (Khusus Aspirasi)',
            UserRole::Editor->value => 'Editor Konten Publik',
        ];

        $optionRole = $this->option('role');
        if ($optionRole && in_array($optionRole, array_keys($roleOptions), true)) {
            $selectedRoleKey = $optionRole;
        } else {
            $selectedRoleKey = $this->choice(
                'Pilih Peran Akun (Role)',
                array_keys($roleOptions),
                0
            );
        }

        $role = UserRole::from($selectedRoleKey);

        // 4. Kata Sandi Rahasia (Tersembunyi)
        $password = $this->option('password') ?: $this->secret('Masukkan Kata Sandi (Minimal 8 karakter)');
        if (strlen((string) $password) < 8) {
            $this->error('Kata sandi harus minimal 8 karakter.');
            return self::FAILURE;
        }

        if (! $this->option('password')) {
            $passwordConfirmation = $this->secret('Ulangi Konfirmasi Kata Sandi');
            if ($password !== $passwordConfirmation) {
                $this->error('Konfirmasi kata sandi tidak cocok.');
                return self::FAILURE;
            }
        }

        // Simpan Akun
        $user = User::create([
            'name' => trim((string) $name),
            'email' => $email,
            'role' => $role,
            'password' => Hash::make($password),
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->newLine();
        $this->info("✔ Akun pengelola berhasil dibuat!");
        $this->line("  ID    : {$user->id}");
        $this->line("  Nama  : {$user->name}");
        $this->line("  Email : {$user->email}");
        $this->line("  Peran : {$role->label()}");
        $this->line("  Status: Aktif");
        $this->newLine();
        $this->comment("Silakan login melalui tautan: " . route('admin.login', [], false));

        return self::SUCCESS;
    }
}
