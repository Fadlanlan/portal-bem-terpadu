<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\AdminAuditLog;
use App\Models\BemMember;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class Tahap11BemMemberAndOrganizationDeleteTest extends TestCase
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

    private function createOrganization(): Organization
    {
        return Organization::create([
            'code' => 'UKM-MUSIK',
            'name' => 'Unit Kegiatan Mahasiswa Seni Musik',
            'slug' => 'ukm-seni-musik',
            'level' => 'Universitas',
            'role_type' => 'Unit Kegiatan Mahasiswa',
            'description' => 'Wadah pengembangan kreativitas seni musik mahasiswa.',
            'sort_order' => 10,
            'status' => 'published',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Fitur 3: Hapus Struktur Organisasi (Organization)
    |--------------------------------------------------------------------------
    */

    public function test_admin_can_delete_organization_and_audit_log_is_recorded(): void
    {
        $admin = $this->createAdminUser(UserRole::Admin);
        $org = $this->createOrganization();

        $response = $this->actingAs($admin)->delete(route('admin.organizations.destroy', $org));

        $response->assertRedirect(route('admin.organizations.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('organizations', [
            'id' => $org->id,
        ]);

        $this->assertDatabaseHas('admin_audit_logs', [
            'action' => 'organization.delete',
            'user_id' => $admin->id,
        ]);
    }

    public function test_unauthorized_user_cannot_delete_organization(): void
    {
        $org = $this->createOrganization();

        // Tamu (guest)
        $this->delete(route('admin.organizations.destroy', $org))
            ->assertRedirect(route('admin.login'));

        // Advokasi (tidak punya hak akses modul organizations)
        $advocacy = $this->createAdminUser(UserRole::Advocacy);
        $this->actingAs($advocacy)
            ->delete(route('admin.organizations.destroy', $org))
            ->assertForbidden();

        $this->assertDatabaseHas('organizations', ['id' => $org->id]);
    }

    /*
    |--------------------------------------------------------------------------
    | Fitur 4: Pengelolaan Fungsionaris BEM (BemMember)
    |--------------------------------------------------------------------------
    */

    public function test_guest_cannot_access_bem_members_admin(): void
    {
        $this->get(route('admin.bem_members.index'))->assertRedirect(route('admin.login'));
    }

    public function test_advocacy_role_cannot_access_bem_members_admin(): void
    {
        $advocacy = $this->createAdminUser(UserRole::Advocacy);
        $this->actingAs($advocacy)->get(route('admin.bem_members.index'))->assertForbidden();
    }

    public function test_admin_and_editor_can_access_bem_members_admin(): void
    {
        $admin = $this->createAdminUser(UserRole::Admin);
        $editor = $this->createAdminUser(UserRole::Editor);

        $this->actingAs($admin)->get(route('admin.bem_members.index'))->assertOk();
        $this->actingAs($editor)->get(route('admin.bem_members.index'))->assertOk();
    }

    public function test_admin_can_create_bem_member_with_photo_upload(): void
    {
        Storage::fake('public');
        $admin = $this->createAdminUser(UserRole::Admin);

        $photo = $this->createFakeImage('presma.jpg', 50);

        $data = [
            'name' => 'Fulan bin Fulan',
            'position' => 'Presiden Mahasiswa',
            'department' => 'Pimpinan Utama',
            'level' => 'presma_wapresma',
            'period' => '2025/2026',
            'description' => 'Mewujudkan BEM yang inklusif dan kolaboratif.',
            'sort_order' => 1,
            'is_published' => '1',
            'photo' => $photo,
        ];

        $response = $this->actingAs($admin)->post(route('admin.bem_members.store'), $data);

        $response->assertRedirect(route('admin.bem_members.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('bem_members', [
            'name' => 'Fulan bin Fulan',
            'position' => 'Presiden Mahasiswa',
            'level' => 'presma_wapresma',
            'is_published' => true,
        ]);

        $member = BemMember::where('name', 'Fulan bin Fulan')->first();
        $this->assertNotNull($member->photo_path);
        Storage::disk('public')->assertExists($member->photo_path);

        $this->assertDatabaseHas('admin_audit_logs', [
            'action' => 'bem_member.create',
            'user_id' => $admin->id,
        ]);
    }

    public function test_admin_can_update_bem_member_and_replace_photo(): void
    {
        Storage::fake('public');
        $admin = $this->createAdminUser(UserRole::Admin);

        $oldPhoto = $this->createFakeImage('old.jpg', 30);
        $oldPath = $oldPhoto->store('bem-members', 'public');

        $member = BemMember::create([
            'name' => 'Ahmad Fauzi',
            'position' => 'Menteri Advokasi',
            'department' => 'Kementerian Advokasi & Kesejahteraan',
            'level' => 'minister',
            'period' => '2025/2026',
            'sort_order' => 5,
            'is_published' => true,
            'photo_path' => $oldPath,
        ]);

        Storage::disk('public')->assertExists($oldPath);

        $newPhoto = $this->createFakeImage('new.jpg', 40);

        $response = $this->actingAs($admin)->put(route('admin.bem_members.update', $member), [
            'name' => 'Ahmad Fauzi, S.Kom.',
            'position' => 'Menteri Koordinator Advokasi',
            'department' => 'Kementerian Advokasi & Kesejahteraan',
            'level' => 'minister',
            'period' => '2025/2026',
            'description' => 'Deskripsi baru',
            'sort_order' => 4,
            'is_published' => '1',
            'photo' => $newPhoto,
        ]);

        $response->assertRedirect(route('admin.bem_members.index'));

        $member->refresh();
        $this->assertEquals('Ahmad Fauzi, S.Kom.', $member->name);
        $this->assertEquals('Menteri Koordinator Advokasi', $member->position);
        $this->assertNotEquals($oldPath, $member->photo_path);

        // Foto lama terhapus, foto baru ada
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($member->photo_path);

        $this->assertDatabaseHas('admin_audit_logs', [
            'action' => 'bem_member.update',
            'user_id' => $admin->id,
        ]);
    }

    public function test_admin_can_delete_bem_member_and_storage_file_is_cleaned_up(): void
    {
        Storage::fake('public');
        $admin = $this->createAdminUser(UserRole::Admin);

        $photo = $this->createFakeImage('member.jpg', 25);
        $photoPath = $photo->store('bem-members', 'public');

        $member = BemMember::create([
            'name' => 'Siti Nurhaliza',
            'position' => 'Sekretaris Jenderal',
            'department' => 'Sekretariat Utama',
            'level' => 'bph',
            'period' => '2025/2026',
            'sort_order' => 2,
            'is_published' => true,
            'photo_path' => $photoPath,
        ]);

        Storage::disk('public')->assertExists($photoPath);

        $response = $this->actingAs($admin)->delete(route('admin.bem_members.destroy', $member));

        $response->assertRedirect(route('admin.bem_members.index'));
        $this->assertDatabaseMissing('bem_members', ['id' => $member->id]);
        Storage::disk('public')->assertMissing($photoPath);

        $this->assertDatabaseHas('admin_audit_logs', [
            'action' => 'bem_member.delete',
            'user_id' => $admin->id,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Fitur 4: Halaman Publik Struktur Kepengurusan BEM
    |--------------------------------------------------------------------------
    */

    public function test_public_bem_structure_page_is_accessible(): void
    {
        $response = $this->get(route('bem.structure'));
        $response->assertOk();
        $response->assertSee('Struktur Kepengurusan');
    }

    public function test_public_alias_route_redirects_to_structure(): void
    {
        $response = $this->get('/kepengurusan');
        $response->assertRedirect(route('bem.structure'));
    }

    public function test_public_page_displays_published_members_and_hides_drafts(): void
    {
        $presma = BemMember::create([
            'name' => 'Budi Santoso (Presma)',
            'position' => 'Presiden Mahasiswa',
            'level' => 'presma_wapresma',
            'period' => '2025/2026',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $draftMember = BemMember::create([
            'name' => 'Calon Staf Rahasia',
            'position' => 'Staf Magang',
            'level' => 'staff',
            'period' => '2025/2026',
            'sort_order' => 99,
            'is_published' => false,
        ]);

        $response = $this->get(route('bem.structure'));

        $response->assertOk();
        $response->assertSee('Budi Santoso (Presma)');
        $response->assertDontSee('Calon Staf Rahasia');
    }

    public function test_public_page_uses_placeholder_when_photo_is_missing(): void
    {
        $bph = BemMember::create([
            'name' => 'Dewi Lestari',
            'position' => 'Bendahara Umum',
            'level' => 'bph',
            'period' => '2025/2026',
            'sort_order' => 3,
            'is_published' => true,
            'photo_path' => null,
        ]);

        $this->assertEquals(asset('images/member-placeholder.svg'), $bph->photo_url);

        $response = $this->get(route('bem.structure'));
        $response->assertOk();
        $response->assertSee('Dewi Lestari');
        $response->assertSee('member-placeholder.svg');
    }

    public function test_organizations_page_links_to_bem_structure(): void
    {
        $response = $this->get(route('organizations.index'));
        $response->assertOk();
        $response->assertSee(route('bem.structure'), false);
    }
}
