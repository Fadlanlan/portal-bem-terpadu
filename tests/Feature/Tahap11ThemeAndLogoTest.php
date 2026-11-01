<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\AdminAuditLog;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class Tahap11ThemeAndLogoTest extends TestCase
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
     * Helper membuat file fake gambar valid tanpa ketergantungan ekstensi GD.
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

    public function test_guest_cannot_access_theme_settings(): void
    {
        $response = $this->get(route('admin.settings.theme'));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_non_super_admin_cannot_access_theme_settings(): void
    {
        $admin = $this->createAdminUser(UserRole::Admin);
        $editor = $this->createAdminUser(UserRole::Editor);
        $advocacy = $this->createAdminUser(UserRole::Advocacy);

        $this->actingAs($admin)->get(route('admin.settings.theme'))->assertForbidden();
        $this->actingAs($editor)->get(route('admin.settings.theme'))->assertForbidden();
        $this->actingAs($advocacy)->get(route('admin.settings.theme'))->assertForbidden();
    }

    public function test_super_admin_can_view_theme_settings_page(): void
    {
        $superAdmin = $this->createAdminUser(UserRole::SuperAdmin);

        $response = $this->actingAs($superAdmin)->get(route('admin.settings.theme'));

        $response->assertOk();
        $response->assertSee('Kustomisasi Tema');
        $response->assertSee('primary_color');
        $response->assertSee('Integritas');
    }

    public function test_super_admin_can_update_theme_with_valid_hex_colors(): void
    {
        $superAdmin = $this->createAdminUser(UserRole::SuperAdmin);

        $themeData = [
            'primary_color' => '#064e3b',
            'secondary_color' => '#065f46',
            'accent_color' => '#d97706',
            'background_color' => '#f0fdf4',
            'surface_color' => '#ffffff',
            'text_primary_color' => '#064e3b',
            'text_secondary_color' => '#374151',
        ];

        $response = $this->actingAs($superAdmin)->put(route('admin.settings.theme.update'), $themeData);

        $response->assertRedirect(route('admin.settings.theme'));
        $response->assertSessionHas('success');

        foreach ($themeData as $key => $color) {
            $this->assertEquals($color, SiteSetting::get($key));
        }

        $this->assertDatabaseHas('admin_audit_logs', [
            'action' => 'settings.theme_update',
            'user_id' => $superAdmin->id,
        ]);
    }

    public function test_theme_update_rejects_invalid_hex_colors(): void
    {
        $superAdmin = $this->createAdminUser(UserRole::SuperAdmin);

        $invalidData = [
            'primary_color' => 'red',
            'secondary_color' => '#12345',
            'accent_color' => '#GGGGGG',
            'background_color' => 'invalid-color',
            'surface_color' => '#1234567',
            'text_primary_color' => '',
            'text_secondary_color' => 'rgb(0,0,0)',
        ];

        $response = $this->actingAs($superAdmin)->put(route('admin.settings.theme.update'), $invalidData);

        $response->assertSessionHasErrors([
            'primary_color',
            'secondary_color',
            'accent_color',
            'background_color',
            'surface_color',
            'text_primary_color',
            'text_secondary_color',
        ]);
    }

    public function test_super_admin_can_reset_theme_to_default(): void
    {
        $superAdmin = $this->createAdminUser(UserRole::SuperAdmin);

        // Ubah warna dulu
        SiteSetting::set('primary_color', '#881337', 'theme', 'color');
        $this->assertEquals('#881337', SiteSetting::get('primary_color'));

        $response = $this->actingAs($superAdmin)->post(route('admin.settings.theme.reset'));

        $response->assertRedirect(route('admin.settings.theme'));
        $response->assertSessionHas('success');

        $this->assertNull(SiteSetting::where('key', 'primary_color')->first());
        $this->assertEquals(SiteSetting::DEFAULT_THEME['primary_color'], SiteSetting::getThemeSettings()['primary_color']);

        $this->assertDatabaseHas('admin_audit_logs', [
            'action' => 'settings.theme_reset',
            'user_id' => $superAdmin->id,
        ]);
    }

    public function test_guest_and_non_super_admin_cannot_access_logo_settings(): void
    {
        $this->get(route('admin.settings.logo'))->assertRedirect(route('admin.login'));

        $admin = $this->createAdminUser(UserRole::Admin);
        $this->actingAs($admin)->get(route('admin.settings.logo'))->assertForbidden();
    }

    public function test_super_admin_can_view_logo_page(): void
    {
        $superAdmin = $this->createAdminUser(UserRole::SuperAdmin);

        $response = $this->actingAs($superAdmin)->get(route('admin.settings.logo'));

        $response->assertOk();
        $response->assertSee('Pengelolaan Logo Website');
        $response->assertSee('Unggah Logo Baru');
    }

    public function test_super_admin_can_upload_valid_logo(): void
    {
        Storage::fake('public');
        $superAdmin = $this->createAdminUser(UserRole::SuperAdmin);

        $file = $this->createFakeImage('bem-logo.png', 50);

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

    public function test_logo_upload_validates_image_mimes_and_size(): void
    {
        Storage::fake('public');
        $superAdmin = $this->createAdminUser(UserRole::SuperAdmin);

        // File non-image (txt)
        $textDoc = UploadedFile::fake()->create('document.txt', 100, 'text/plain');
        $response = $this->actingAs($superAdmin)->post(route('admin.settings.logo.update'), [
            'logo' => $textDoc,
        ]);
        $response->assertSessionHasErrors('logo');

        // File terlalu besar (> 2048 KB)
        $hugeImage = $this->createFakeImage('huge.png', 3000);
        $responseHuge = $this->actingAs($superAdmin)->post(route('admin.settings.logo.update'), [
            'logo' => $hugeImage,
        ]);
        $responseHuge->assertSessionHasErrors('logo');
    }

    public function test_super_admin_can_delete_custom_logo(): void
    {
        Storage::fake('public');
        $superAdmin = $this->createAdminUser(UserRole::SuperAdmin);

        $file = $this->createFakeImage('bem-logo.png', 50);
        $this->actingAs($superAdmin)->post(route('admin.settings.logo.update'), ['logo' => $file]);

        $storedPath = SiteSetting::get('site_logo');
        $this->assertNotNull($storedPath);
        Storage::disk('public')->assertExists($storedPath);

        // Hapus logo
        $response = $this->actingAs($superAdmin)->delete(route('admin.settings.logo.destroy'));

        $response->assertRedirect(route('admin.settings.logo'));
        $response->assertSessionHas('success');

        $this->assertNull(SiteSetting::get('site_logo'));
        Storage::disk('public')->assertMissing($storedPath);
        $this->assertNull(SiteSetting::getLogoUrl());

        $this->assertDatabaseHas('admin_audit_logs', [
            'action' => 'settings.logo_remove',
            'user_id' => $superAdmin->id,
        ]);
    }

    public function test_public_pages_render_dynamic_theme_and_logo(): void
    {
        Storage::fake('public');

        SiteSetting::set('primary_color', '#123456', 'theme', 'color');
        $file = $this->createFakeImage('custom-logo.png', 50);
        $stored = $file->store('logos', 'public');
        SiteSetting::set('site_logo', $stored, 'branding', 'image');

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('--theme-primary: #123456;', false);
        $response->assertSee(SiteSetting::getLogoUrl(), false);
    }

    public function test_public_navbar_and_footer_and_subpages_reflect_theme_and_logo(): void
    {
        Storage::fake('public');

        SiteSetting::set('primary_color', '#064e3b', 'theme', 'color');
        SiteSetting::set('accent_color', '#d97706', 'theme', 'color');
        $file = $this->createFakeImage('campus-logo.png', 40);
        $stored = $file->store('logos', 'public');
        SiteSetting::set('site_logo', $stored, 'branding', 'image');

        $logoUrl = SiteSetting::getLogoUrl();
        $this->assertNotNull($logoUrl);
        $this->assertStringContainsString('storage/logos/', $logoUrl);

        $pages = [
            route('home'),
            route('news.index'),
            route('programs.index'),
            route('events.index'),
            route('about'),
        ];

        foreach ($pages as $url) {
            $response = $this->get($url);
            $response->assertOk();
            $response->assertSee($logoUrl, false);
            $response->assertSee('--theme-primary: #064e3b;', false);
            $response->assertSee('--theme-accent: #d97706;', false);
        }
    }
}
