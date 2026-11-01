<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminAuditLog;
use App\Models\SiteSetting;
use App\Services\LogoProcessingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class SettingAdminController extends Controller
{
    /**
     * Tampilkan halaman penyesuaian tema warna website (Khusus Super Admin).
     */
    public function theme(): View
    {
        $currentTheme = SiteSetting::getThemeSettings();
        $presets = SiteSetting::getPresets();

        return view('admin.settings.theme', [
            'theme' => $currentTheme,
            'presets' => $presets,
        ]);
    }

    /**
     * Simpan kustomisasi tema warna website.
     */
    public function updateTheme(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'primary_color' => ['required', 'string', 'regex:/^#([a-fA-F0-9]{6})$/'],
            'secondary_color' => ['required', 'string', 'regex:/^#([a-fA-F0-9]{6})$/'],
            'accent_color' => ['required', 'string', 'regex:/^#([a-fA-F0-9]{6})$/'],
            'background_color' => ['required', 'string', 'regex:/^#([a-fA-F0-9]{6})$/'],
            'surface_color' => ['required', 'string', 'regex:/^#([a-fA-F0-9]{6})$/'],
            'text_primary_color' => ['required', 'string', 'regex:/^#([a-fA-F0-9]{6})$/'],
            'text_secondary_color' => ['required', 'string', 'regex:/^#([a-fA-F0-9]{6})$/'],
        ], [
            '*.regex' => 'Format warna harus berupa kode HEX 6 digit yang valid (contoh: #0f172a).',
            '*.required' => 'Setiap kode warna wajib ditentukan.',
        ]);

        foreach ($validated as $key => $colorHex) {
            SiteSetting::set($key, strtolower(trim($colorHex)), 'theme', 'color');
        }

        AdminAuditLog::record(
            'settings.theme_update',
            'Pengaturan tema warna visual website berhasil diperbarui oleh administrator.',
            null,
            $validated
        );

        return redirect()
            ->route('admin.settings.theme')
            ->with('success', 'Tema warna website berhasil diperbarui.');
    }

    /**
     * Pulihkan pengaturan tema warna website ke default (Integritas & Aksi).
     */
    public function resetTheme(): RedirectResponse
    {
        SiteSetting::resetTheme();

        AdminAuditLog::record(
            'settings.theme_reset',
            'Skema warna visual website dikembalikan ke pengaturan default (Integritas & Aksi).',
            null,
            SiteSetting::DEFAULT_THEME
        );

        return redirect()
            ->route('admin.settings.theme')
            ->with('success', 'Tema warna berhasil dipulihkan ke pengaturan default.');
    }

    /**
     * Tampilkan halaman pengelolaan logo website.
     */
    /**
     * Tampilkan halaman pengelolaan logo website.
     */
    public function logo(): View
    {
        $logoUrl = SiteSetting::getLogoUrl();
        $rawLogoPath = SiteSetting::get('site_logo');

        $preview = session('logo_preview');
        if ($preview) {
            $token = $preview['token'] ?? '';
            $previewFile = storage_path('app/private/temp_logos/proc_' . $token . '.png');
            if (! file_exists($previewFile)) {
                session()->forget('logo_preview');
                $preview = null;
            }
        }

        return view('admin.settings.logo', [
            'logoUrl' => $logoUrl,
            'rawLogoPath' => $rawLogoPath,
            'preview' => $preview,
        ]);
    }

    /**
     * Proses gambar logo untuk pratinjau (penghapusan background, trimming, feathering).
     */
    public function processLogoPreview(Request $request): RedirectResponse
    {
        $request->validate([
            'logo' => ['required', 'file', 'mimes:png,jpeg,jpg,webp', 'max:2048'],
            'remove_bg' => ['nullable', 'boolean'],
            'tolerance' => ['nullable', 'integer', 'min:5', 'max:100'],
            'padding' => ['nullable', 'integer', 'min:0', 'max:80'],
            'feather' => ['nullable', 'integer', 'min:0', 'max:5'],
        ], [
            'logo.required' => 'Pilih berkas gambar logo yang akan diunggah.',
            'logo.file' => 'Berkas yang diunggah harus berupa berkas yang sah.',
            'logo.mimes' => 'Format logo yang diizinkan hanya PNG, JPEG, JPG, atau WebP.',
            'logo.max' => 'Ukuran berkas logo maksimal 2MB.',
        ]);

        $file = $request->file('logo');
        if (! $file || ! $file->isValid()) {
            return back()->withErrors(['logo' => 'Berkas gambar tidak valid atau gagal diunggah.']);
        }

        try {
            $inputPath = $file->getRealPath() ?: $file->getPathname();
            if (! $inputPath || ! file_exists($inputPath)) {
                $inputPath = $file->path();
            }

            $token = bin2hex(random_bytes(16));
            $tempDir = storage_path('app/private/temp_logos');
            if (! is_dir($tempDir)) {
                @mkdir($tempDir, 0755, true);
            }

            $ext = $file->getClientOriginalExtension() ?: 'png';
            $origTempPath = $tempDir . DIRECTORY_SEPARATOR . 'orig_' . $token . '.' . $ext;
            copy($inputPath, $origTempPath);

            LogoProcessingService::validateImage($origTempPath);

            $procTempPath = $tempDir . DIRECTORY_SEPARATOR . 'proc_' . $token . '.png';

            $options = [
                'remove_bg' => $request->boolean('remove_bg', true),
                'tolerance' => $request->integer('tolerance', 28),
                'padding' => $request->integer('padding', 16),
                'feather' => $request->integer('feather', 2),
            ];

            $metadata = LogoProcessingService::process($origTempPath, $procTempPath, $options);

            session(['logo_preview' => [
                'token' => $token,
                'metadata' => $metadata,
                'original_filename' => $file->getClientOriginalName(),
                'ext' => $ext,
                'options' => $options,
                'created_at' => now()->timestamp,
            ]]);

            return redirect()
                ->route('admin.settings.logo')
                ->with('success', 'Gambar logo berhasil diproses! Silakan tinjau hasil pratinjau sebelum menerapkannya.');
        } catch (Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Gagal memproses gambar logo', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors(['logo' => 'Gagal memproses gambar: ' . $e->getMessage()]);
        }
    }

    /**
     * Tampilkan berkas pratinjau sementara hasil olahan (khusus Super Admin).
     */
    public function serveTempPreview(string $token): BinaryFileResponse
    {
        if (! preg_match('/^[a-fA-F0-9]{32}$/', $token)) {
            abort(404);
        }

        $path = storage_path('app/private/temp_logos/proc_' . $token . '.png');
        if (! file_exists($path)) {
            abort(404);
        }

        return response()->file($path, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'no-cache, private',
        ]);
    }

    /**
     * Tampilkan berkas asli sementara untuk perbandingan berdampingan (khusus Super Admin).
     */
    public function serveTempOriginal(string $token): BinaryFileResponse
    {
        if (! preg_match('/^[a-fA-F0-9]{32}$/', $token)) {
            abort(404);
        }

        $matches = glob(storage_path('app/private/temp_logos/orig_' . $token . '.*'));
        if (empty($matches) || ! file_exists($matches[0])) {
            abort(404);
        }

        $mime = mime_content_type($matches[0]) ?: 'image/png';

        return response()->file($matches[0], [
            'Content-Type' => $mime,
            'Cache-Control' => 'no-cache, private',
        ]);
    }

    /**
     * Terapkan hasil pratinjau logo sebagai logo resmi website.
     */
    public function applyLogo(): RedirectResponse
    {
        $preview = session('logo_preview');
        if (! $preview || empty($preview['token'])) {
            return redirect()
                ->route('admin.settings.logo')
                ->withErrors(['logo' => 'Tidak ada pratinjau logo yang aktif untuk diterapkan.']);
        }

        $token = $preview['token'];
        $tempPath = storage_path('app/private/temp_logos/proc_' . $token . '.png');

        if (! file_exists($tempPath)) {
            session()->forget('logo_preview');

            return redirect()
                ->route('admin.settings.logo')
                ->withErrors(['logo' => 'Berkas pratinjau sementara sudah kedaluwarsa atau tidak ditemukan.']);
        }

        $newFilename = 'logo_' . uniqid() . '.png';
        $destinationRelative = 'logos/' . $newFilename;

        // Simpan menggunakan disk public (mendukung local storage & fake storage)
        $saved = Storage::disk('public')->put($destinationRelative, file_get_contents($tempPath));

        if (! $saved) {
            return redirect()
                ->route('admin.settings.logo')
                ->withErrors(['logo' => 'Gagal memindahkan berkas logo ke penyimpanan publik server.']);
        }

        $oldLogo = SiteSetting::get('site_logo');

        // Perbarui konfigurasi logo resmi
        SiteSetting::set('site_logo', $destinationRelative, 'branding', 'image');

        // Hapus logo lama secara aman jika ada dan tidak sama
        if (! empty($oldLogo)
            && $oldLogo !== $destinationRelative
            && Storage::disk('public')->exists($oldLogo)
            && ! str_contains($oldLogo, '..')
        ) {
            Storage::disk('public')->delete($oldLogo);
        }

        // Bersihkan berkas sementara
        $this->cleanupTempFiles($token);
        session()->forget('logo_preview');

        AdminAuditLog::record(
            'settings.logo_update',
            'Logo resmi website berhasil diperbarui dengan hasil pemrosesan transparan otomatis.',
            null,
            [
                'logo_path' => $destinationRelative,
                'metadata' => $preview['metadata'] ?? [],
            ]
        );

        return redirect()
            ->route('admin.settings.logo')
            ->with('success', 'Logo resmi berhasil diterapkan! Tampilan di bilah navigasi dan footer telah diperbarui.');
    }

    /**
     * Batalkan pratinjau logo dan pertahankan logo aktif saat ini.
     */
    public function cancelLogoPreview(): RedirectResponse
    {
        $preview = session('logo_preview');
        if ($preview && ! empty($preview['token'])) {
            $this->cleanupTempFiles($preview['token']);
        }

        session()->forget('logo_preview');

        return redirect()
            ->route('admin.settings.logo')
            ->with('info', 'Pratinjau logo dibatalkan. Logo resmi aktif tetap dipertahankan.');
    }

    /**
     * Unggah langsung dan terapkan logo baru untuk website (dengan pemrosesan otomatis).
     */
    public function updateLogo(Request $request): RedirectResponse
    {
        $request->validate([
            'logo' => ['required', 'file', 'mimes:png,jpeg,jpg,webp', 'max:2048'],
        ], [
            'logo.required' => 'Pilih berkas gambar logo yang akan diunggah.',
            'logo.file' => 'Berkas harus berupa gambar yang valid.',
            'logo.mimes' => 'Format logo yang diizinkan hanya PNG, JPEG, JPG, atau WebP.',
            'logo.max' => 'Ukuran berkas logo maksimal 2MB.',
        ]);

        $file = $request->file('logo');
        if (! $file || ! $file->isValid()) {
            return back()->withErrors(['logo' => 'Unggahan berkas logo gagal diproses.']);
        }

        try {
            $inputPath = $file->getRealPath() ?: $file->getPathname();
            if (! $inputPath || ! file_exists($inputPath)) {
                $inputPath = $file->path();
            }

            $token = bin2hex(random_bytes(16));
            $tempDir = storage_path('app/private/temp_logos');
            if (! is_dir($tempDir)) {
                @mkdir($tempDir, 0755, true);
            }

            $ext = $file->getClientOriginalExtension() ?: 'png';
            $tempInput = $tempDir . DIRECTORY_SEPARATOR . 'upload_' . $token . '.' . $ext;
            copy($inputPath, $tempInput);

            LogoProcessingService::validateImage($tempInput);

            $tempOutput = $tempDir . DIRECTORY_SEPARATOR . 'proc_direct_' . $token . '.png';

            // Jalankan pipeline pemrosesan otomatis
            $metadata = LogoProcessingService::process($tempInput, $tempOutput, [
                'remove_bg' => true,
                'tolerance' => 28,
                'padding' => 16,
                'feather' => 2,
            ]);

            $newFilename = 'logo_' . uniqid() . '.png';
            $relativeDestination = 'logos/' . $newFilename;

            // Simpan menggunakan disk public
            Storage::disk('public')->put($relativeDestination, file_get_contents($tempOutput));

            @unlink($tempInput);
            @unlink($tempOutput);

            $oldLogo = SiteSetting::get('site_logo');

            // Simpan referensi logo baru
            SiteSetting::set('site_logo', $relativeDestination, 'branding', 'image');

            // Hapus logo lama secara aman jika ada dan tidak sama
            if (! empty($oldLogo)
                && $oldLogo !== $relativeDestination
                && Storage::disk('public')->exists($oldLogo)
                && ! str_contains($oldLogo, '..')
            ) {
                Storage::disk('public')->delete($oldLogo);
            }

            session()->forget('logo_preview');

            AdminAuditLog::record(
                'settings.logo_update',
                'Logo resmi website berhasil diperbarui dengan berkas baru.',
                null,
                [
                    'logo_path' => $relativeDestination,
                    'metadata' => $metadata,
                ]
            );

            return redirect()
                ->route('admin.settings.logo')
                ->with('success', 'Logo resmi website berhasil diperbarui dengan background transparan.');
        } catch (Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Gagal memperbarui logo secara langsung', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors(['logo' => 'Pemrosesan logo gagal: ' . $e->getMessage()]);
        }
    }

    /**
     * Hapus logo kustom dan kembali menggunakan badge/logo default sistem.
     */
    public function destroyLogo(): RedirectResponse
    {
        $oldLogo = SiteSetting::get('site_logo');

        if (! empty($oldLogo)
            && Storage::disk('public')->exists($oldLogo)
            && ! str_contains($oldLogo, '..')
        ) {
            Storage::disk('public')->delete($oldLogo);
        }

        SiteSetting::set('site_logo', null, 'branding', 'image');
        session()->forget('logo_preview');

        AdminAuditLog::record(
            'settings.logo_remove',
            'Logo kustom dihapus, sistem kembali menggunakan badge identitas default.',
            null,
            []
        );

        return redirect()
            ->route('admin.settings.logo')
            ->with('success', 'Logo kustom berhasil dihapus. Sistem kembali menampilkan badge logo default.');
    }

    /**
     * Bersihkan berkas sementara hasil olahan logo berdasarkan token.
     */
    private function cleanupTempFiles(string $token): void
    {
        if (! preg_match('/^[a-fA-F0-9]{32}$/', $token)) {
            return;
        }

        $tempDir = storage_path('app/private/temp_logos');
        $files = glob($tempDir . DIRECTORY_SEPARATOR . '*_' . $token . '.*');
        if (is_array($files)) {
            foreach ($files as $f) {
                if (file_exists($f)) {
                    @unlink($f);
                }
            }
        }
    }
}
