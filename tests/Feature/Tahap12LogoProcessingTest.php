<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\AdminAuditLog;
use App\Models\SiteSetting;
use App\Models\User;
use App\Services\LogoProcessingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class Tahap12LogoProcessingTest extends TestCase
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
     * Helper membuat file fake gambar PNG valid (1x1 piksel).
     */
    private function createFakeImage(string $filename = 'test.png', int $kilobytes = 10): UploadedFile
    {
        $basePng = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');

        if ($kilobytes * 1024 > strlen($basePng)) {
            $content = $basePng . str_repeat('X', ($kilobytes * 1024) - strlen($basePng));
        } else {
            $content = $basePng;
        }

        return UploadedFile::fake()->createWithContent($filename, $content);
    }

    // =========================================================================
    // A. PENGUJIAN PROSES & PRATINJAU LOGO
    // =========================================================================

    public function test_super_admin_can_process_logo_preview(): void
    {
        Storage::fake('public');
        $superAdmin = $this->createAdminUser(UserRole::SuperAdmin);

        $file = $this->createFakeImage('logo-bem.png', 20);

        $response = $this->actingAs($superAdmin)->post(route('admin.settings.logo.process'), [
            'logo' => $file,
            'remove_bg' => true,
            'tolerance' => 28,
            'padding' => 16,
            'feather' => 2,
        ]);

        $response->assertRedirect(route('admin.settings.logo'));
        $response->assertSessionHas('success');
        $response->assertSessionHas('logo_preview');

        $preview = session('logo_preview');
        $this->assertNotEmpty($preview['token']);
        $this->assertNotEmpty($preview['metadata']);
        $this->assertEquals('logo-bem.png', $preview['original_filename']);
        $this->assertTrue($preview['metadata']['success'] ?? false);
    }

    public function test_preview_does_not_change_active_logo(): void
    {
        Storage::fake('public');
        $superAdmin = $this->createAdminUser(UserRole::SuperAdmin);

        // Upload terlebih dahulu logo aktif
        $file1 = $this->createFakeImage('logo1.png', 15);
        $this->actingAs($superAdmin)->post(route('admin.settings.logo.update'), ['logo' => $file1]);
        $activeLogo = SiteSetting::get('site_logo');
        $this->assertNotNull($activeLogo);

        // Proses pratinjau file baru
        $file2 = $this->createFakeImage('logo2.png', 15);
        $this->actingAs($superAdmin)->post(route('admin.settings.logo.process'), [
            'logo' => $file2,
        ]);

        // Logo aktif tidak boleh berubah
        $this->assertEquals($activeLogo, SiteSetting::get('site_logo'));
        Storage::disk('public')->assertExists($activeLogo);
    }

    public function test_super_admin_can_apply_previewed_logo(): void
    {
        Storage::fake('public');
        $superAdmin = $this->createAdminUser(UserRole::SuperAdmin);

        // Proses pratinjau
        $file = $this->createFakeImage('logo-apply.png', 15);
        $this->actingAs($superAdmin)->post(route('admin.settings.logo.process'), [
            'logo' => $file,
        ]);

        $preview = session('logo_preview');
        $this->assertNotEmpty($preview['token']);

        // Terapkan
        $response = $this->actingAs($superAdmin)->post(route('admin.settings.logo.apply'));

        $response->assertRedirect(route('admin.settings.logo'));
        $response->assertSessionHas('success');

        $newLogo = SiteSetting::get('site_logo');
        $this->assertNotNull($newLogo);
        Storage::disk('public')->assertExists($newLogo);

        $this->assertDatabaseHas('admin_audit_logs', [
            'action' => 'settings.logo_update',
            'user_id' => $superAdmin->id,
        ]);
    }

    public function test_apply_replaces_old_logo_and_deletes_previous(): void
    {
        Storage::fake('public');
        $superAdmin = $this->createAdminUser(UserRole::SuperAdmin);

        // Upload logo pertama
        $file1 = $this->createFakeImage('logo-old.png', 10);
        $this->actingAs($superAdmin)->post(route('admin.settings.logo.update'), ['logo' => $file1]);
        $oldLogo = SiteSetting::get('site_logo');
        $this->assertNotNull($oldLogo);

        // Proses pratinjau logo baru
        $file2 = $this->createFakeImage('logo-new.png', 10);
        $this->actingAs($superAdmin)->post(route('admin.settings.logo.process'), ['logo' => $file2]);

        // Terapkan
        $this->actingAs($superAdmin)->post(route('admin.settings.logo.apply'));

        $newLogo = SiteSetting::get('site_logo');
        $this->assertNotEquals($oldLogo, $newLogo);
        Storage::disk('public')->assertExists($newLogo);
        Storage::disk('public')->assertMissing($oldLogo);
    }

    public function test_super_admin_can_cancel_preview(): void
    {
        Storage::fake('public');
        $superAdmin = $this->createAdminUser(UserRole::SuperAdmin);

        // Upload logo aktif terlebih dahulu
        $file1 = $this->createFakeImage('active-logo.png', 15);
        $this->actingAs($superAdmin)->post(route('admin.settings.logo.update'), ['logo' => $file1]);
        $activeLogo = SiteSetting::get('site_logo');

        // Proses pratinjau
        $file2 = $this->createFakeImage('preview-logo.png', 15);
        $this->actingAs($superAdmin)->post(route('admin.settings.logo.process'), ['logo' => $file2]);
        $this->assertNotNull(session('logo_preview'));

        // Batalkan
        $response = $this->actingAs($superAdmin)->post(route('admin.settings.logo.cancel'));

        $response->assertRedirect(route('admin.settings.logo'));
        $response->assertSessionHas('info');
        $this->assertNull(session('logo_preview'));

        // Logo aktif tetap tidak berubah
        $this->assertEquals($activeLogo, SiteSetting::get('site_logo'));
    }

    // =========================================================================
    // B. PENGUJIAN VALIDASI BERKAS
    // =========================================================================

    public function test_process_rejects_non_image_file(): void
    {
        Storage::fake('public');
        $superAdmin = $this->createAdminUser(UserRole::SuperAdmin);

        $textFile = UploadedFile::fake()->create('document.txt', 100, 'text/plain');
        $response = $this->actingAs($superAdmin)->post(route('admin.settings.logo.process'), [
            'logo' => $textFile,
        ]);

        $response->assertSessionHasErrors('logo');
    }

    public function test_process_rejects_oversized_file(): void
    {
        Storage::fake('public');
        $superAdmin = $this->createAdminUser(UserRole::SuperAdmin);

        $hugeImage = $this->createFakeImage('huge.png', 3000);
        $response = $this->actingAs($superAdmin)->post(route('admin.settings.logo.process'), [
            'logo' => $hugeImage,
        ]);

        $response->assertSessionHasErrors('logo');
    }

    public function test_upload_rejects_non_image_file(): void
    {
        Storage::fake('public');
        $superAdmin = $this->createAdminUser(UserRole::SuperAdmin);

        $textFile = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');
        $response = $this->actingAs($superAdmin)->post(route('admin.settings.logo.update'), [
            'logo' => $textFile,
        ]);

        $response->assertSessionHasErrors('logo');
    }

    public function test_upload_rejects_oversized_file(): void
    {
        Storage::fake('public');
        $superAdmin = $this->createAdminUser(UserRole::SuperAdmin);

        $hugeImage = $this->createFakeImage('huge.png', 3000);
        $response = $this->actingAs($superAdmin)->post(route('admin.settings.logo.update'), [
            'logo' => $hugeImage,
        ]);

        $response->assertSessionHasErrors('logo');
    }

    // =========================================================================
    // C. PENGUJIAN OTORISASI DAN KONTROL AKSES
    // =========================================================================

    public function test_guest_cannot_access_logo_process(): void
    {
        $this->post(route('admin.settings.logo.process'))->assertRedirect(route('admin.login'));
    }

    public function test_guest_cannot_access_logo_apply(): void
    {
        $this->post(route('admin.settings.logo.apply'))->assertRedirect(route('admin.login'));
    }

    public function test_guest_cannot_access_logo_cancel(): void
    {
        $this->post(route('admin.settings.logo.cancel'))->assertRedirect(route('admin.login'));
    }

    public function test_admin_role_cannot_process_logo(): void
    {
        $admin = $this->createAdminUser(UserRole::Admin);
        $this->actingAs($admin)->post(route('admin.settings.logo.process'))->assertForbidden();
    }

    public function test_editor_role_cannot_process_logo(): void
    {
        $editor = $this->createAdminUser(UserRole::Editor);
        $this->actingAs($editor)->post(route('admin.settings.logo.process'))->assertForbidden();
    }

    public function test_advocacy_role_cannot_process_logo(): void
    {
        $advocacy = $this->createAdminUser(UserRole::Advocacy);
        $this->actingAs($advocacy)->post(route('admin.settings.logo.process'))->assertForbidden();
    }

    public function test_admin_role_cannot_apply_logo(): void
    {
        $admin = $this->createAdminUser(UserRole::Admin);
        $this->actingAs($admin)->post(route('admin.settings.logo.apply'))->assertForbidden();
    }

    public function test_admin_role_cannot_cancel_logo(): void
    {
        $admin = $this->createAdminUser(UserRole::Admin);
        $this->actingAs($admin)->post(route('admin.settings.logo.cancel'))->assertForbidden();
    }

    // =========================================================================
    // D. PENGUJIAN STREAMING FILE SEMENTARA
    // =========================================================================

    public function test_temp_preview_rejects_invalid_token(): void
    {
        $superAdmin = $this->createAdminUser(UserRole::SuperAdmin);

        $response = $this->actingAs($superAdmin)->get(route('admin.settings.logo.temp_preview', ['token' => 'invalid-token!']));
        $response->assertNotFound();
    }

    public function test_temp_preview_returns_404_for_nonexistent_token(): void
    {
        $superAdmin = $this->createAdminUser(UserRole::SuperAdmin);

        $token = bin2hex(random_bytes(16));
        $response = $this->actingAs($superAdmin)->get(route('admin.settings.logo.temp_preview', ['token' => $token]));
        $response->assertNotFound();
    }

    public function test_temp_original_rejects_invalid_token(): void
    {
        $superAdmin = $this->createAdminUser(UserRole::SuperAdmin);

        $response = $this->actingAs($superAdmin)->get(route('admin.settings.logo.temp_original', ['token' => 'xyz!@#']));
        $response->assertNotFound();
    }

    // =========================================================================
    // E. PENGUJIAN DIRECT UPLOAD (updateLogo)
    // =========================================================================

    public function test_super_admin_can_directly_upload_logo(): void
    {
        Storage::fake('public');
        $superAdmin = $this->createAdminUser(UserRole::SuperAdmin);

        $file = $this->createFakeImage('direct-upload.png', 20);

        $response = $this->actingAs($superAdmin)->post(route('admin.settings.logo.update'), [
            'logo' => $file,
        ]);

        $response->assertRedirect(route('admin.settings.logo'));
        $response->assertSessionHas('success');

        $storedPath = SiteSetting::get('site_logo');
        $this->assertNotNull($storedPath);
        Storage::disk('public')->assertExists($storedPath);
        $this->assertNotNull(SiteSetting::getLogoUrl());

        $this->assertDatabaseHas('admin_audit_logs', [
            'action' => 'settings.logo_update',
            'user_id' => $superAdmin->id,
        ]);
    }

    // =========================================================================
    // F. PENGUJIAN APPLY TANPA PRATINJAU
    // =========================================================================

    public function test_apply_without_preview_returns_error(): void
    {
        $superAdmin = $this->createAdminUser(UserRole::SuperAdmin);

        $response = $this->actingAs($superAdmin)->post(route('admin.settings.logo.apply'));

        $response->assertRedirect(route('admin.settings.logo'));
        $response->assertSessionHasErrors('logo');
    }

    // =========================================================================
    // G. PENGUJIAN SERVICE UNIT
    // =========================================================================

    public function test_validate_image_rejects_nonexistent_file(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('tidak ditemukan');

        LogoProcessingService::validateImage('/path/to/nonexistent/file.png');
    }

    public function test_validate_image_rejects_empty_file(): void
    {
        $tmp = tempnam(sys_get_temp_dir(), 'empty_');
        file_put_contents($tmp, '');

        try {
            $this->expectException(\InvalidArgumentException::class);
            LogoProcessingService::validateImage($tmp);
        } finally {
            @unlink($tmp);
        }
    }

    public function test_validate_image_rejects_wrong_mime(): void
    {
        $tmp = tempnam(sys_get_temp_dir(), 'txt_');
        file_put_contents($tmp, 'This is plain text, not an image.');

        try {
            $this->expectException(\InvalidArgumentException::class);
            $this->expectExceptionMessage('Format berkas tidak didukung');
            LogoProcessingService::validateImage($tmp);
        } finally {
            @unlink($tmp);
        }
    }

    public function test_validate_image_accepts_valid_png(): void
    {
        $basePng = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
        $tmp = tempnam(sys_get_temp_dir(), 'valid_');
        file_put_contents($tmp, $basePng);

        try {
            $result = LogoProcessingService::validateImage($tmp);
            $this->assertEquals(1, $result['width']);
            $this->assertEquals(1, $result['height']);
            $this->assertEquals('image/png', $result['mime']);
        } finally {
            @unlink($tmp);
        }
    }

    // =========================================================================
    // H. PENGUJIAN HALAMAN LOGO MENAMPILKAN KONTEN PENTING
    // =========================================================================

    public function test_logo_page_shows_required_elements(): void
    {
        $superAdmin = $this->createAdminUser(UserRole::SuperAdmin);

        $response = $this->actingAs($superAdmin)->get(route('admin.settings.logo'));

        $response->assertOk();
        $response->assertSee('Pengelolaan Logo Website');
        $response->assertSee('Unggah Logo Baru');
    }
}
