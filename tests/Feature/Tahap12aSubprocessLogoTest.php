<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\SiteSetting;
use App\Models\User;
use App\Services\LogoProcessingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class Tahap12aSubprocessLogoTest extends TestCase
{
    use RefreshDatabase;

    private function createAdminUser(UserRole $role = UserRole::SuperAdmin): User
    {
        return User::create([
            'name' => 'Pengguna ' . $role->value,
            'email' => $role->value . '_' . uniqid() . '@bem.kampus.ac.id',
            'role' => $role,
            'password' => Hash::make('Password123!'),
            'is_active' => true,
        ]);
    }

    /**
     * Buat berkas PNG sintetis (100x100) secara programatis:
     * Background: Putih (255, 255, 255)
     * Kotak Foreground: Biru (0, 0, 255) dari (20,20) ke (80,80)
     * Titik Foreground Internal: Putih (255, 255, 255) di tengah (50,50)
     */
    private function createSyntheticImageFile(string $filename = 'synthetic_emblem.png'): string
    {
        $php = LogoProcessingService::resolvePhpBinary();
        $ini = LogoProcessingService::resolvePhpIni();

        $destPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $filename;
        if (file_exists($destPath)) {
            @unlink($destPath);
        }

        $createCode = <<<PHP
\$im = imagecreatetruecolor(100, 100);
\$white = imagecolorallocate(\$im, 255, 255, 255);
imagefill(\$im, 0, 0, \$white);

\$blue = imagecolorallocate(\$im, 0, 0, 255);
imagefilledrectangle(\$im, 20, 20, 80, 80, \$blue);

\$whiteInner = imagecolorallocate(\$im, 255, 255, 255);
imagefilledellipse(\$im, 50, 50, 20, 20, \$whiteInner);

imagepng(\$im, '$destPath');
imagedestroy(\$im);
PHP;

        $args = [$php];
        if ($ini) {
            $args[] = '-c';
            $args[] = $ini;
        }
        $args[] = '-dextension_dir=C:\\xampp\\php\\ext';
        $args[] = '-dextension=gd';
        $args[] = '-r';
        $args[] = $createCode;

        Process::run($args);

        return $destPath;
    }

    /**
     * 1 & 2: Verifikasi ketersediaan cURL dan GD pada subprocess PHP CLI.
     */
    public function test_subprocess_has_curl_and_gd_enabled_without_errors(): void
    {
        $php = LogoProcessingService::resolvePhpBinary();
        $ini = LogoProcessingService::resolvePhpIni();

        $args = [$php];
        if ($ini) {
            $args[] = '-c';
            $args[] = $ini;
        }
        $args[] = '-dextension_dir=C:\\xampp\\php\\ext';
        $args[] = '-dextension=gd';
        $args[] = '-r';
        $args[] = 'echo json_encode(["curl" => extension_loaded("curl"), "gd" => extension_loaded("gd")]);';

        $env = [
            'PATH' => 'C:\\xampp\\php;' . (getenv('PATH') ?: ''),
            'SystemRoot' => getenv('SystemRoot') ?: 'C:\\Windows',
            'WINDIR' => getenv('WINDIR') ?: 'C:\\Windows',
        ];

        $process = Process::env($env)->timeout(15)->run($args);

        $this->assertTrue($process->successful(), 'Subprocess harus keluar dengan exit code 0: ' . $process->errorOutput());
        $this->assertStringNotContainsString('Unable to start curl module', $process->errorOutput());
        $this->assertEmpty($process->errorOutput(), 'Stderr harus kosong tanpa error startup: ' . $process->errorOutput());

        $modules = json_decode(trim($process->output()), true);
        $this->assertIsArray($modules);
        $this->assertTrue($modules['curl'], 'Modul cURL harus aktif pada subprocess.');
        $this->assertTrue($modules['gd'], 'Modul GD harus aktif pada subprocess.');
    }

    /**
     * 3, 4, 5: Verifikasi pemrosesan gambar nyata via subprocess:
     * - Menghasilkan PNG RGBA 32-bit
     * - Background luar menjadi transparan (alpha >= 120)
     * - Foreground biru dipertahankan (alpha == 0)
     * - Foreground putih internal tetap utuh dan opaque (alpha == 0, RGB putih)
     * - Pemangkasan ruang kosong dan penambahan padding
     */
    public function test_subprocess_processes_synthetic_image_with_correct_transparency_and_foreground_preservation(): void
    {
        $synthInput = $this->createSyntheticImageFile('synth_test_input.png');
        $synthOutput = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'synth_test_output.png';
        if (file_exists($synthOutput)) {
            @unlink($synthOutput);
        }

        try {
            $metadata = LogoProcessingService::process($synthInput, $synthOutput, [
                'remove_bg' => true,
                'tolerance' => 28,
                'padding' => 16,
                'feather' => 2,
            ]);

            $this->assertTrue($metadata['success']);
            $this->assertFileExists($synthOutput);
            $this->assertGreaterThan(0, filesize($synthOutput));

            // Bounding box biru: 20 ke 80 -> 61x61 px. Ditambah padding 16*2 -> 93x93 px.
            $this->assertEquals(93, $metadata['trimmed_width']);
            $this->assertEquals(93, $metadata['trimmed_height']);
            $this->assertEquals(1.0, $metadata['aspect_ratio']);

            // Analisis piksel output secara programatis
            $php = LogoProcessingService::resolvePhpBinary();
            $ini = LogoProcessingService::resolvePhpIni();

            $inspectCode = <<<PHP
\$im = imagecreatefrompng('$synthOutput');
\$w = imagesx(\$im);
\$h = imagesy(\$im);

// 1. Cek sudut kanvas terluar (harus transparan penuh)
\$cornerColor = imagecolorat(\$im, 0, 0);
\$cornerAlpha = (\$cornerColor >> 24) & 0x7F;

// 2. Cek area biru foreground (misal di koordinat x=20, y=20)
\$blueColor = imagecolorat(\$im, 20, 20);
\$blueAlpha = (\$blueColor >> 24) & 0x7F;
\$blueB = \$blueColor & 0xFF;

// 3. Cek titik putih internal di tengah lambang (koordinat x=46, y=46)
\$centerColor = imagecolorat(\$im, (int)(\$w / 2), (int)(\$h / 2));
\$centerAlpha = (\$centerColor >> 24) & 0x7F;
\$centerR = (\$centerColor >> 16) & 0xFF;
\$centerG = (\$centerColor >> 8) & 0xFF;
\$centerB = \$centerColor & 0xFF;

echo json_encode([
    'corner_alpha' => \$cornerAlpha,
    'blue_alpha' => \$blueAlpha,
    'blue_is_solid_blue' => (\$blueAlpha === 0 && \$blueB > 200),
    'center_alpha' => \$centerAlpha,
    'center_is_opaque_white' => (\$centerAlpha === 0 && \$centerR > 240 && \$centerG > 240 && \$centerB > 240),
]);
imagedestroy(\$im);
PHP;

            $args = [$php];
            if ($ini) {
                $args[] = '-c';
                $args[] = $ini;
            }
            $args[] = '-dextension_dir=C:\\xampp\\php\\ext';
            $args[] = '-dextension=gd';
            $args[] = '-r';
            $args[] = $inspectCode;

            $inspectProc = Process::run($args);
            $this->assertTrue($inspectProc->successful(), 'Gagal menganalisis output citra: ' . $inspectProc->errorOutput());

            $inspection = json_decode(trim($inspectProc->output()), true);
            $this->assertIsArray($inspection);

            // Verifikasi 1: Sudut luar adalah transparan
            $this->assertGreaterThanOrEqual(120, $inspection['corner_alpha'], 'Sudut luar harus transparan (alpha >= 120).');

            // Verifikasi 2: Kotak biru adalah solid dan tidak pudar
            $this->assertEquals(0, $inspection['blue_alpha'], 'Area biru foreground harus solid (alpha = 0).');
            $this->assertTrue($inspection['blue_is_solid_blue'], 'Warna biru harus terjaga.');

            // Verifikasi 3: Titik putih di tengah lingkaran TIDAK boleh terhapus (tetap solid opaque putih)
            $this->assertEquals(0, $inspection['center_alpha'], 'Titik putih internal harus tetap solid (alpha = 0), tidak terhapus.');
            $this->assertTrue($inspection['center_is_opaque_white'], 'Warna putih internal harus tetap terjaga.');
        } finally {
            if (file_exists($synthInput)) @unlink($synthInput);
            if (file_exists($synthOutput)) @unlink($synthOutput);
        }
    }

    /**
     * 6: Penanganan gambar tidak valid melalui subprocess.
     */
    public function test_subprocess_handles_invalid_image_gracefully(): void
    {
        $corruptFile = tempnam(sys_get_temp_dir(), 'corrupt_');
        file_put_contents($corruptFile, 'not-a-valid-image-content-at-all');
        $outputFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'invalid_out.png';

        try {
            $this->expectException(\InvalidArgumentException::class);
            LogoProcessingService::process($corruptFile, $outputFile);
        } finally {
            @unlink($corruptFile);
            if (file_exists($outputFile)) @unlink($outputFile);
        }
    }

    /**
     * 7: Kegagalan pemrosesan tidak mengganti logo aktif.
     */
    public function test_failed_process_does_not_change_active_logo(): void
    {
        Storage::fake('public');
        $superAdmin = $this->createAdminUser(UserRole::SuperAdmin);

        // Pasang logo aktif awal
        SiteSetting::set('site_logo', 'logos/active_original.png', 'branding', 'image');
        Storage::disk('public')->put('logos/active_original.png', 'active-content');

        $initialLogo = SiteSetting::get('site_logo');

        // Unggah berkas invalid
        $badFile = UploadedFile::fake()->create('corrupted.png', 50, 'image/png');
        // Isinya teks biasa agar getimagesize gagal
        file_put_contents($badFile->getPathname(), 'plain-corrupted-text');

        $response = $this->actingAs($superAdmin)->post(route('admin.settings.logo.process'), [
            'logo' => $badFile,
        ]);

        $response->assertSessionHasErrors('logo');

        // Logo aktif tetap sama
        $this->assertEquals($initialLogo, SiteSetting::get('site_logo'));
        Storage::disk('public')->assertExists('logos/active_original.png');
    }

    /**
     * 8: Pembatalan pratinjau membersihkan file sementara.
     */
    public function test_preview_cancellation_cleans_temporary_files(): void
    {
        Storage::fake('public');
        $superAdmin = $this->createAdminUser(UserRole::SuperAdmin);

        // Buat fake token dan temp files
        $token = bin2hex(random_bytes(16));
        $tempDir = storage_path('app/private/temp_logos');
        if (!is_dir($tempDir)) {
            @mkdir($tempDir, 0755, true);
        }

        $tempOrig = $tempDir . DIRECTORY_SEPARATOR . 'orig_' . $token . '.png';
        $tempProc = $tempDir . DIRECTORY_SEPARATOR . 'proc_' . $token . '.png';
        file_put_contents($tempOrig, 'temp-orig-data');
        file_put_contents($tempProc, 'temp-proc-data');

        $this->assertFileExists($tempOrig);
        $this->assertFileExists($tempProc);

        session(['logo_preview' => [
            'token' => $token,
            'metadata' => ['success' => true],
        ]]);

        $response = $this->actingAs($superAdmin)->post(route('admin.settings.logo.cancel'));

        $response->assertRedirect(route('admin.settings.logo'));
        $response->assertSessionHas('info');
        $this->assertNull(session('logo_preview'));

        // Berkas sementara harus sudah terhapus
        $this->assertFileDoesNotExist($tempOrig);
        $this->assertFileDoesNotExist($tempProc);
    }
}
