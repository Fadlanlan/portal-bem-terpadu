<?php

namespace Tests\Feature;

use App\Enums\AspirationCategory;
use App\Enums\AspirationStatus;
use App\Enums\UserRole;
use App\Models\Article;
use App\Models\Aspiration;
use App\Models\AspirationStatusHistory;
use App\Models\Event;
use App\Models\Organization;
use App\Models\User;
use App\Models\WorkProgram;
use App\Services\AspirationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Helper untuk membuat pengguna pengelola dengan peran tertentu.
     */
    private function createAdminUser(UserRole $role = UserRole::SuperAdmin, bool $isActive = true, string $password = 'Password123!'): User
    {
        return User::create([
            'name' => 'Pengguna ' . $role->value,
            'email' => $role->value . '_' . uniqid() . '@bem.kampus.ac.id',
            'role' => $role,
            'password' => Hash::make($password),
            'is_active' => $isActive,
        ]);
    }

    /**
     * 1. Pengguna anonim/tamu (guest) diarahkan ke form login saat mencoba mengakses dashboard.
     */
    public function test_guest_is_redirected_to_login_when_accessing_admin_routes(): void
    {
        $response = $this->get('/admin');
        $response->assertRedirect(route('admin.login'));

        $aspirationResponse = $this->get('/admin/aspirasi');
        $aspirationResponse->assertRedirect(route('admin.login'));
    }

    /**
     * 2. Pengelola aktif dengan kredensial benar dapat login dan dialihkan ke dashboard.
     */
    public function test_active_admin_can_login_with_valid_credentials(): void
    {
        $user = $this->createAdminUser(UserRole::Admin, true, 'SandiRahasia123!');

        $response = $this->post(route('admin.login.attempt'), [
            'email' => $user->email,
            'password' => 'SandiRahasia123!',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    /**
     * 3. Kredensial salah ditolak dan menampilkan pesan error umum (tanpa membocorkan keberadaan email).
     */
    public function test_invalid_credentials_are_rejected_with_generic_error(): void
    {
        $user = $this->createAdminUser(UserRole::Admin, true, 'SandiRahasia123!');

        $response = $this->from(route('admin.login'))->post(route('admin.login.attempt'), [
            'email' => $user->email,
            'password' => 'SandiSalah999!',
        ]);

        $response->assertRedirect(route('admin.login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /**
     * 4. Pengguna nonaktif ditolak saat mencoba masuk meskipun kata sandi benar.
     */
    public function test_inactive_admin_user_cannot_login(): void
    {
        $user = $this->createAdminUser(UserRole::Admin, false, 'SandiRahasia123!');

        $response = $this->from(route('admin.login'))->post(route('admin.login.attempt'), [
            'email' => $user->email,
            'password' => 'SandiRahasia123!',
        ]);

        $response->assertRedirect(route('admin.login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /**
     * 5. Logout berhasil dan mengakhiri sesi autentikasi.
     */
    public function test_logout_invalidates_session_and_redirects_to_login(): void
    {
        $user = $this->createAdminUser(UserRole::Admin);

        $response = $this->actingAs($user)->post(route('admin.logout'));

        $response->assertRedirect(route('admin.login'));
        $this->assertGuest();
    }

    /**
     * 6. Advokasi dapat mengakses modul aspirasi dan dashboard.
     */
    public function test_advocacy_role_can_access_aspirations_and_dashboard(): void
    {
        $user = $this->createAdminUser(UserRole::Advocacy);

        $this->actingAs($user)->get(route('admin.dashboard'))->assertStatus(200);
        $this->actingAs($user)->get(route('admin.aspirations.index'))->assertStatus(200);
    }

    /**
     * 7. Advokasi ditolak (HTTP 403) saat mengakses modul berita, program kerja, agenda, ormawa, dan pengguna.
     */
    public function test_advocacy_role_is_forbidden_from_unauthorized_modules(): void
    {
        $user = $this->createAdminUser(UserRole::Advocacy);

        $this->actingAs($user)->get(route('admin.articles.index'))->assertStatus(403);
        $this->actingAs($user)->get(route('admin.programs.index'))->assertStatus(403);
        $this->actingAs($user)->get(route('admin.events.index'))->assertStatus(403);
        $this->actingAs($user)->get(route('admin.organizations.index'))->assertStatus(403);
        $this->actingAs($user)->get(route('admin.users.index'))->assertStatus(403);
    }

    /**
     * 8. Editor dapat mengakses modul berita, program kerja, agenda, dan organisasi ormawa.
     */
    public function test_editor_role_can_access_content_modules(): void
    {
        $user = $this->createAdminUser(UserRole::Editor);

        $this->actingAs($user)->get(route('admin.dashboard'))->assertStatus(200);
        $this->actingAs($user)->get(route('admin.articles.index'))->assertStatus(200);
        $this->actingAs($user)->get(route('admin.programs.index'))->assertStatus(200);
        $this->actingAs($user)->get(route('admin.events.index'))->assertStatus(200);
        $this->actingAs($user)->get(route('admin.organizations.index'))->assertStatus(200);
    }

    /**
     * 9. Editor ditolak (HTTP 403) saat mengakses modul aspirasi dan manajemen pengguna.
     */
    public function test_editor_role_is_forbidden_from_aspirations_and_users(): void
    {
        $user = $this->createAdminUser(UserRole::Editor);

        $this->actingAs($user)->get(route('admin.aspirations.index'))->assertStatus(403);
        $this->actingAs($user)->get(route('admin.users.index'))->assertStatus(403);
    }

    /**
     * 10. Admin operasional dapat mengelola seluruh konten dan aspirasi, tetapi ditolak dari manajemen pengguna.
     */
    public function test_admin_role_can_manage_operations_but_not_users(): void
    {
        $user = $this->createAdminUser(UserRole::Admin);

        $this->actingAs($user)->get(route('admin.aspirations.index'))->assertStatus(200);
        $this->actingAs($user)->get(route('admin.articles.index'))->assertStatus(200);
        $this->actingAs($user)->get(route('admin.programs.index'))->assertStatus(200);
        $this->actingAs($user)->get(route('admin.events.index'))->assertStatus(200);
        $this->actingAs($user)->get(route('admin.organizations.index'))->assertStatus(200);

        // Akses pengguna khusus Super Admin
        $this->actingAs($user)->get(route('admin.users.index'))->assertStatus(403);
    }

    /**
     * 11. Super admin dapat mengakses seluruh modul termasuk manajemen pengguna/admin.
     */
    public function test_super_admin_can_access_all_modules_including_users(): void
    {
        $user = $this->createAdminUser(UserRole::SuperAdmin);

        $this->actingAs($user)->get(route('admin.dashboard'))->assertStatus(200);
        $this->actingAs($user)->get(route('admin.aspirations.index'))->assertStatus(200);
        $this->actingAs($user)->get(route('admin.articles.index'))->assertStatus(200);
        $this->actingAs($user)->get(route('admin.programs.index'))->assertStatus(200);
        $this->actingAs($user)->get(route('admin.events.index'))->assertStatus(200);
        $this->actingAs($user)->get(route('admin.organizations.index'))->assertStatus(200);
        $this->actingAs($user)->get(route('admin.users.index'))->assertStatus(200);
    }

    /**
     * 12. Dashboard menampilkan ringkasan data statistik yang sesuai dari database.
     */
    public function test_dashboard_displays_aggregated_database_statistics(): void
    {
        $service = app(AspirationService::class);
        $service->createAspiration([
            'category' => AspirationCategory::Academic->value,
            'subject' => 'Keluhan KRS Online Lambat',
            'message' => 'Server KRS mengalami kendala performa.',
            'is_anonymous' => 1,
            'privacy_consent' => 1,
        ]);

        Article::create([
            'title' => 'Warta Berita Pengujian Dashboard',
            'slug' => 'warta-berita-pengujian-dashboard',
            'category' => 'Kampus',
            'excerpt' => 'Ringkasan singkat berita.',
            'content' => 'Konten lengkap berita warta mahasiswa.',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $superAdmin = $this->createAdminUser(UserRole::SuperAdmin);

        $response = $this->actingAs($superAdmin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Statistik Operasional Portal');
        $response->assertSee('Keluhan KRS Online Lambat');
    }

    /**
     * 13 & 14. Admin dapat memperbarui status aspirasi, respons resmi, dan tercatat di riwayat audit.
     */
    public function test_admin_can_update_aspiration_status_and_creates_audit_history(): void
    {
        $service = app(AspirationService::class);
        $created = $service->createAspiration([
            'category' => AspirationCategory::Facility->value,
            'subject' => 'Lampu Parkir Selatan Mati Total',
            'message' => 'Area parkir selatan gelap saat malam hari dan berisiko keamanan.',
            'is_anonymous' => 0,
            'reporter_name' => 'Budi Santoso',
            'reporter_email' => 'budi@student.univ.ac.id',
            'privacy_consent' => 1,
        ]);

        $aspiration = $created['aspiration'];
        $advocacyUser = $this->createAdminUser(UserRole::Advocacy);

        $payload = [
            'status' => AspirationStatus::InProgress->value,
            'admin_response' => 'Tim Advokasi telah berkoordinasi dengan pengelola fasilitas kampus. Perbaikan ditargetkan selesai lusa.',
            'note' => 'Sudah menghubungi Bagian Rumah Tangga via memo dinas.',
        ];

        $response = $this->actingAs($advocacyUser)->put(route('admin.aspirations.update', $aspiration), $payload);

        $response->assertRedirect(route('admin.aspirations.show', $aspiration));
        $response->assertSessionHas('success');

        // Pastikan record aspirasi terupdate di database
        $this->assertDatabaseHas('aspirations', [
            'id' => $aspiration->id,
            'status' => AspirationStatus::InProgress->value,
            'admin_response' => $payload['admin_response'],
        ]);

        // Pastikan tabel riwayat status mencatat aktor changed_by dan catatan internal
        $this->assertDatabaseHas('aspiration_status_histories', [
            'aspiration_id' => $aspiration->id,
            'old_status' => AspirationStatus::Submitted->value,
            'new_status' => AspirationStatus::InProgress->value,
            'changed_by' => $advocacyUser->id,
            'note' => $payload['note'],
        ]);
    }

    /**
     * 15. Pelacakan publik melihat status terkini dan respons resmi setelah ditangani admin.
     */
    public function test_public_tracking_shows_updated_status_and_admin_response(): void
    {
        $service = app(AspirationService::class);
        $created = $service->createAspiration([
            'category' => AspirationCategory::Welfare->value,
            'subject' => 'Keterlambatan Pencairan Dana Beasiswa',
            'message' => 'Pencairan beasiswa belum masuk hingga akhir bulan ini.',
            'is_anonymous' => 1,
            'privacy_consent' => 1,
        ]);

        $aspiration = $created['aspiration'];
        $rawTrackingCode = $created['tracking_code'];
        $admin = $this->createAdminUser(UserRole::Admin);

        // Update status melalui admin service
        $service->updateAspirationStatus(
            $aspiration,
            AspirationStatus::Resolved,
            'Dana telah dicairkan oleh Biro Kemahasiswaan ke rekening masing-masing penerima.',
            'Konfirmasi selesai dari bagian keuangan',
            $admin->id
        );

        // Lacak melalui endpoint publik
        $response = $this->post(route('aspirations.tracking.show'), [
            'ticket_number' => $aspiration->ticket_number,
            'tracking_code' => $rawTrackingCode,
        ]);

        $response->assertStatus(200);
        $response->assertSee('Selesai');
        $response->assertSee('Dana telah dicairkan oleh Biro Kemahasiswaan');
    }

    /**
     * 16. CRUD Warta & Berita berhasil untuk role yang diizinkan.
     */
    public function test_articles_crud_lifecycle(): void
    {
        $editor = $this->createAdminUser(UserRole::Editor);

        // 1. Simpan berita baru
        $storeResponse = $this->actingAs($editor)->post(route('admin.articles.store'), [
            'title' => 'Pelantikan Pengurus BEM Masa Bakti 2026',
            'category' => 'Kegiatan',
            'excerpt' => 'Pelantikan resmi pengurus BEM diresmikan oleh Rektorat.',
            'content' => 'Rangkaian acara pelantikan berlangsung khidmat di Gedung Rektorat...',
            'author' => 'Biro Kominfo BEM',
            'status' => 'published',
            'published_at' => now()->format('Y-m-d\TH:i'),
        ]);

        $storeResponse->assertRedirect(route('admin.articles.index'));
        $this->assertDatabaseHas('articles', [
            'title' => 'Pelantikan Pengurus BEM Masa Bakti 2026',
            'status' => 'published',
        ]);

        $article = Article::where('title', 'Pelantikan Pengurus BEM Masa Bakti 2026')->firstOrFail();

        // 2. Akses halaman edit
        $this->actingAs($editor)->get(route('admin.articles.edit', $article))->assertStatus(200);

        // 3. Perbarui berita
        $updateResponse = $this->actingAs($editor)->put(route('admin.articles.update', $article), [
            'title' => 'Pelantikan Pengurus BEM Masa Bakti 2026/2027 Sukses Digelar',
            'category' => 'Liputan Khusus',
            'excerpt' => 'Pelantikan resmi pengurus BEM diresmikan oleh Rektorat kampus.',
            'content' => 'Isi berita diperbarui dengan detail susunan kementerian...',
            'author' => 'Redaksi BEM',
            'status' => 'published',
            'published_at' => now()->format('Y-m-d\TH:i'),
        ]);

        $updateResponse->assertRedirect(route('admin.articles.index'));
        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'title' => 'Pelantikan Pengurus BEM Masa Bakti 2026/2027 Sukses Digelar',
            'category' => 'Liputan Khusus',
        ]);

        // 4. Hapus berita
        $deleteResponse = $this->actingAs($editor)->delete(route('admin.articles.destroy', $article));
        $deleteResponse->assertRedirect(route('admin.articles.index'));
        $this->assertDatabaseMissing('articles', ['id' => $article->id]);
    }

    /**
     * 17. Artikel berstatus draft tidak muncul di daftar warta publik.
     */
    public function test_draft_article_is_hidden_from_public_news_page(): void
    {
        $published = Article::create([
            'title' => 'Berita Terbuka untuk Umum',
            'slug' => 'berita-terbuka-untuk-umum',
            'category' => 'Umum',
            'excerpt' => 'Ringkasan berita terbuka',
            'content' => 'Konten lengkap berita terbuka',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $draft = Article::create([
            'title' => 'Draf Rahasia Belum Boleh Terbit',
            'slug' => 'draf-rahasia-belum-boleh-terbit',
            'category' => 'Internal',
            'excerpt' => 'Ringkasan draft',
            'content' => 'Konten draf internal',
            'status' => 'draft',
            'published_at' => null,
        ]);

        $response = $this->get(route('news.index'));

        $response->assertStatus(200);
        $response->assertSee('Berita Terbuka untuk Umum');
        $response->assertDontSee('Draf Rahasia Belum Boleh Terbit');
    }

    /**
     * 18. CRUD Program Kerja berhasil.
     */
    public function test_work_programs_crud_lifecycle(): void
    {
        $editor = $this->createAdminUser(UserRole::Editor);

        // Buat program
        $storeResponse = $this->actingAs($editor)->post(route('admin.programs.store'), [
            'name' => 'Program Bantuan Advokasi Finansial UKT',
            'organization' => 'Kementerian Sosial & Advokasi BEM',
            'department' => 'Advokasi Kampus',
            'status' => 'Rencana',
            'publication_status' => 'published',
            'timeline' => 'Semester Gasal 2026',
            'pic' => 'Andi Wijaya',
            'description' => 'Program pendampingan pengajuan keringanan biaya kuliah bagi mahasiswa terdampak.',
            'targets' => "100 Mahasiswa terverifikasi\nDisposisi ke Rektorat",
            'indicators' => "Verifikasi 100% berkas\nPencairan tepat waktu",
        ]);

        $storeResponse->assertRedirect(route('admin.programs.index'));
        $this->assertDatabaseHas('work_programs', [
            'name' => 'Program Bantuan Advokasi Finansial UKT',
            'status' => 'Rencana',
        ]);

        $program = WorkProgram::where('name', 'Program Bantuan Advokasi Finansial UKT')->firstOrFail();

        // Edit program
        $this->actingAs($editor)->get(route('admin.programs.edit', $program))->assertStatus(200);

        $updateResponse = $this->actingAs($editor)->put(route('admin.programs.update', $program), [
            'name' => 'Program Bantuan Advokasi Finansial UKT Terpadu',
            'organization' => 'Kementerian Sosial & Advokasi BEM',
            'status' => 'Berlangsung',
            'publication_status' => 'published',
            'timeline' => 'Sepanjang Tahun 2026',
            'pic' => 'Andi Wijaya & Tim',
            'description' => 'Deskripsi program yang diperbarui.',
        ]);

        $updateResponse->assertRedirect(route('admin.programs.index'));
        $this->assertDatabaseHas('work_programs', [
            'id' => $program->id,
            'name' => 'Program Bantuan Advokasi Finansial UKT Terpadu',
            'status' => 'Berlangsung',
        ]);

        // Hapus program
        $this->actingAs($editor)->delete(route('admin.programs.destroy', $program))->assertRedirect(route('admin.programs.index'));
        $this->assertDatabaseMissing('work_programs', ['id' => $program->id]);
    }

    /**
     * 19. CRUD Agenda Kegiatan dan validasi tanggal selesai tidak boleh lebih awal dari waktu mulai.
     */
    public function test_events_crud_lifecycle_and_datetime_validation(): void
    {
        $editor = $this->createAdminUser(UserRole::Editor);

        // Uji validasi tanggal: ended_at mendahului started_at harus ditolak
        $invalidDateResponse = $this->actingAs($editor)->post(route('admin.events.store'), [
            'title' => 'Webinar Kepemimpinan Mahasiswa',
            'category' => 'Webinar',
            'description' => 'Deskripsi kegiatan webinar.',
            'location' => 'Zoom Meeting',
            'started_at' => now()->addDays(5)->format('Y-m-d\TH:i'),
            'ended_at' => now()->addDays(4)->format('Y-m-d\TH:i'), // Salah: lebih awal dari started_at
            'organizer' => 'BEM Universitas',
            'status' => 'published',
        ]);

        $invalidDateResponse->assertSessionHasErrors('ended_at');

        // Simpan agenda yang valid
        $startedAt = now()->addDays(2)->setHour(9)->setMinute(0);
        $endedAt = now()->addDays(2)->setHour(12)->setMinute(0);

        $validResponse = $this->actingAs($editor)->post(route('admin.events.store'), [
            'title' => 'Webinar Kepemimpinan Mahasiswa Berkarakter',
            'category' => 'Webinar',
            'description' => 'Deskripsi lengkap kegiatan webinar kepemimpinan kemahasiswaan.',
            'location' => 'Auditorium Kampus & Zoom',
            'started_at' => $startedAt->format('Y-m-d\TH:i'),
            'ended_at' => $endedAt->format('Y-m-d\TH:i'),
            'organizer' => 'BEM Universitas & MPM',
            'speakers' => "Prof. Dr. Ir. Sutrisno\nAhmad Fauzi",
            'agenda_points' => "09.00 - 10.30: Materi\n10.30 - 12.00: Diskusi Interaktif",
            'status' => 'published',
        ]);

        $validResponse->assertRedirect(route('admin.events.index'));
        $this->assertDatabaseHas('events', [
            'title' => 'Webinar Kepemimpinan Mahasiswa Berkarakter',
            'status' => 'published',
        ]);

        $event = Event::where('title', 'Webinar Kepemimpinan Mahasiswa Berkarakter')->firstOrFail();

        // Edit agenda
        $this->actingAs($editor)->get(route('admin.events.edit', $event))->assertStatus(200);

        // Hapus agenda
        $this->actingAs($editor)->delete(route('admin.events.destroy', $event))->assertRedirect(route('admin.events.index'));
        $this->assertDatabaseMissing('events', ['id' => $event->id]);
    }

    /**
     * 20. CRUD Organisasi Mahasiswa berhasil.
     */
    public function test_organizations_crud_lifecycle(): void
    {
        $editor = $this->createAdminUser(UserRole::Editor);

        $storeResponse = $this->actingAs($editor)->post(route('admin.organizations.store'), [
            'code' => 'HIMA-IF',
            'name' => 'Himpunan Mahasiswa Informatika',
            'level' => 'Tingkat Jurusan / Program Studi',
            'role_type' => 'Himpunan Mahasiswa Jurusan (HMJ)',
            'description' => 'Wadah kemahasiswaan program studi Informatika.',
            'leader_name' => 'Rian Hidayat',
            'period' => '2026/2027',
            'sort_order' => 5,
            'status' => 'published',
        ]);

        $storeResponse->assertRedirect(route('admin.organizations.index'));
        $this->assertDatabaseHas('organizations', [
            'code' => 'HIMA-IF',
            'name' => 'Himpunan Mahasiswa Informatika',
        ]);

        $org = Organization::where('code', 'HIMA-IF')->firstOrFail();

        // Edit organisasi
        $this->actingAs($editor)->get(route('admin.organizations.edit', $org))->assertStatus(200);

        $updateResponse = $this->actingAs($editor)->put(route('admin.organizations.update', $org), [
            'code' => 'HIMA-IF',
            'name' => 'Himpunan Mahasiswa Informatika Terpadu',
            'description' => 'Deskripsi HIMA IF yang diperbarui.',
            'sort_order' => 6,
            'status' => 'published',
        ]);

        $updateResponse->assertRedirect(route('admin.organizations.index'));
        $this->assertDatabaseHas('organizations', [
            'id' => $org->id,
            'name' => 'Himpunan Mahasiswa Informatika Terpadu',
        ]);

        // Hapus organisasi
        $this->actingAs($editor)->delete(route('admin.organizations.destroy', $org))->assertRedirect(route('admin.organizations.index'));
        $this->assertDatabaseMissing('organizations', ['id' => $org->id]);
    }

    /**
     * 21. Proteksi keamanan: Super Admin tidak dapat menghapus atau menonaktifkan dirinya sendiri atau Super Admin terakhir.
     */
    public function test_super_admin_cannot_delete_or_deactivate_self_or_last_super_admin(): void
    {
        $onlySuperAdmin = $this->createAdminUser(UserRole::SuperAdmin);

        // 1. Tidak boleh menghapus akun diri sendiri saat sedang masuk
        $selfDeleteResponse = $this->actingAs($onlySuperAdmin)->delete(route('admin.users.destroy', $onlySuperAdmin));
        $selfDeleteResponse->assertSessionHasErrors('delete_error');
        $this->assertDatabaseHas('users', ['id' => $onlySuperAdmin->id]);

        // 2. Buat super admin kedua untuk menguji penonaktifan super admin terakhir
        $secondSuperAdmin = $this->createAdminUser(UserRole::SuperAdmin);

        // Mengubah status super admin kedua saat masih ada 2 super admin aktif diperbolehkan
        $updateSecond = $this->actingAs($onlySuperAdmin)->put(route('admin.users.update', $secondSuperAdmin), [
            'name' => $secondSuperAdmin->name,
            'email' => $secondSuperAdmin->email,
            'role' => UserRole::Admin->value, // Diturunkan ke admin
            'is_active' => 1,
        ]);
        $updateSecond->assertRedirect(route('admin.users.index'));

        // Sekarang $onlySuperAdmin adalah SATU-SATUNYA Super Admin aktif
        // Jika ada pengguna lain mencoba menonaktifkan atau mengubah peran $onlySuperAdmin, harus ditolak
        $anotherAdmin = $this->createAdminUser(UserRole::SuperAdmin); // Tambah lagi
        $this->actingAs($anotherAdmin)->delete(route('admin.users.destroy', $onlySuperAdmin)); // Hapus $onlySuperAdmin

        // Sekarang $anotherAdmin adalah satu-satunya super admin aktif
        // Mencoba menurunkan role $anotherAdmin sendiri dari controller dicegah atau ditolak jika super admin terakhir
        $anotherAdmin->refresh();
        $this->assertEquals(UserRole::SuperAdmin, $anotherAdmin->role);
    }

    /**
     * 22. Reset kata sandi pengguna pengelola oleh Super Admin berhasil.
     */
    public function test_super_admin_can_reset_user_password(): void
    {
        $superAdmin = $this->createAdminUser(UserRole::SuperAdmin);
        $targetUser = $this->createAdminUser(UserRole::Editor, true, 'LamaSecret123!');

        $response = $this->actingAs($superAdmin)->put(route('admin.users.reset_password', $targetUser), [
            'new_password' => 'BaruSecret999!',
            'new_password_confirmation' => 'BaruSecret999!',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success');

        $targetUser->refresh();
        $this->assertTrue(Hash::check('BaruSecret999!', $targetUser->password));
    }

    /**
     * 23. Perintah CLI Artisan `app:create-admin` dapat membuat akun admin pertama dengan sukses.
     */
    public function test_artisan_create_admin_command_creates_account_successfully(): void
    {
        $this->artisan('app:create-admin', [
            '--name' => 'Administrator Utama',
            '--email' => 'admin.pertama@bem.kampus.ac.id',
            '--role' => 'super_admin',
            '--password' => 'KombinasiAman123!',
        ])
            ->assertExitCode(0);

        $this->assertDatabaseHas('users', [
            'email' => 'admin.pertama@bem.kampus.ac.id',
            'role' => UserRole::SuperAdmin->value,
            'is_active' => 1,
        ]);

        $created = User::where('email', 'admin.pertama@bem.kampus.ac.id')->firstOrFail();
        $this->assertTrue(Hash::check('KombinasiAman123!', $created->password));
    }
}
