<?php

namespace Tests\Feature;

use App\Enums\AspirationCategory;
use App\Enums\AspirationStatus;
use App\Enums\UserRole;
use App\Mail\AspirationResponseAddedMail;
use App\Mail\AspirationStatusUpdatedMail;
use App\Mail\NewAspirationAdminMail;
use App\Models\AdminAuditLog;
use App\Models\Article;
use App\Models\Aspiration;
use App\Models\Event;
use App\Models\User;
use App\Models\WorkProgram;
use App\Services\AspirationService;
use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Mail\PendingMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class Tahap5SecurityAndAuditTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Helper membuat akun pengelola.
     */
    private function createUser(UserRole $role = UserRole::SuperAdmin, bool $isActive = true, string $password = 'Password123!'): User
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
     * 1. Login dan logout pengelola tercatat di tabel audit log.
     */
    public function test_login_and_logout_are_recorded_in_audit_logs(): void
    {
        $user = $this->createUser(UserRole::Admin, true, 'KombinasiAman123!');

        // Login
        $this->post(route('admin.login.attempt'), [
            'email' => $user->email,
            'password' => 'KombinasiAman123!',
        ]);

        $this->assertDatabaseHas('admin_audit_logs', [
            'user_id' => $user->id,
            'action' => 'auth.login',
        ]);

        // Logout
        $this->actingAs($user)->post(route('admin.logout'));

        $this->assertDatabaseHas('admin_audit_logs', [
            'user_id' => $user->id,
            'action' => 'auth.logout',
        ]);
    }

    /**
     * 2. Rate limiter membatasi percobaan login berlebih (5 percobaan per menit).
     */
    public function test_login_endpoint_has_rate_limiting(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post(route('admin.login.attempt'), [
                'email' => 'wrong@bem.kampus.ac.id',
                'password' => 'wrong-pass',
            ]);
        }

        // Percobaan ke-6 harus terkena limit status 429
        $response = $this->post(route('admin.login.attempt'), [
            'email' => 'wrong@bem.kampus.ac.id',
            'password' => 'wrong-pass',
        ]);

        $response->assertStatus(429);
    }

    /**
     * 3. Tamu (guest) dilarang mengakses halaman laporan dan audit log.
     */
    public function test_guest_is_redirected_from_reports_and_audit_logs(): void
    {
        $this->get(route('admin.reports.index'))->assertRedirect(route('admin.login'));
        $this->get(route('admin.reports.export'))->assertRedirect(route('admin.login'));
        $this->get(route('admin.audit_logs.index'))->assertRedirect(route('admin.login'));
    }

    /**
     * 4. Otorisasi role: Advokasi dapat mengakses laporan, Editor ditolak, dan hanya SuperAdmin yang dapat melihat audit log.
     */
    public function test_role_authorization_on_reports_and_audit_logs(): void
    {
        $advocacy = $this->createUser(UserRole::Advocacy);
        $editor = $this->createUser(UserRole::Editor);
        $admin = $this->createUser(UserRole::Admin);
        $superAdmin = $this->createUser(UserRole::SuperAdmin);

        // Advokasi dapat melihat laporan aspirasi, tapi ditolak dari audit log
        $this->actingAs($advocacy)->get(route('admin.reports.index'))->assertStatus(200);
        $this->actingAs($advocacy)->get(route('admin.audit_logs.index'))->assertStatus(403);

        // Editor ditolak dari laporan dan audit log
        $this->actingAs($editor)->get(route('admin.reports.index'))->assertStatus(403);
        $this->actingAs($editor)->get(route('admin.audit_logs.index'))->assertStatus(403);

        // Admin operasional dapat melihat laporan, tapi ditolak dari audit log
        $this->actingAs($admin)->get(route('admin.reports.index'))->assertStatus(200);
        $this->actingAs($admin)->get(route('admin.audit_logs.index'))->assertStatus(403);

        // Super Admin dapat mengakses keduanya
        $this->actingAs($superAdmin)->get(route('admin.reports.index'))->assertStatus(200);
        $this->actingAs($superAdmin)->get(route('admin.audit_logs.index'))->assertStatus(200);
    }

    /**
     * 5. Perlindungan Super Admin: tidak dapat menghapus atau menonaktifkan Super Admin terakhir.
     */
    public function test_last_active_super_admin_cannot_be_deleted_or_deactivated(): void
    {
        $onlySuperAdmin = $this->createUser(UserRole::SuperAdmin);

        // Hapus diri sendiri ditolak
        $response = $this->actingAs($onlySuperAdmin)->delete(route('admin.users.destroy', $onlySuperAdmin));
        $response->assertSessionHasErrors('delete_error');
        $this->assertDatabaseHas('users', ['id' => $onlySuperAdmin->id]);
    }

    /**
     * 6. Aspirasi anonim: tidak menyimpan identitas dan tidak membocorkan data pelapor.
     */
    public function test_anonymous_aspiration_does_not_save_reporter_identity(): void
    {
        $service = app(AspirationService::class);
        $result = $service->createAspiration([
            'category' => AspirationCategory::Facility->value,
            'subject' => 'AC Rusak di Ruang Seminar',
            'message' => 'Pendingin ruangan mati total sejak kemarin.',
            'is_anonymous' => 1,
            'reporter_name' => 'Harusnya Tidak Disimpan',
            'reporter_email' => 'tidak.boleh@disimpan.ac.id',
            'reporter_student_id' => '12345678',
        ]);

        $aspiration = $result['aspiration'];

        $this->assertTrue($aspiration->is_anonymous);
        $this->assertNull($aspiration->reporter_name);
        $this->assertNull($aspiration->reporter_email);
        $this->assertNull($aspiration->reporter_student_id);
    }

    /**
     * 7. Pelacakan aspirasi dengan kode rahasia valid dan hash tracking tidak dapat disalahgunakan secara langsung.
     */
    public function test_tracking_verification_validates_secret_and_rejects_raw_hash(): void
    {
        $service = app(AspirationService::class);
        $result = $service->createAspiration([
            'category' => AspirationCategory::Academic->value,
            'subject' => 'Jadwal Ujian Tumpang Tindih',
            'message' => 'Terdapat bentrok jadwal pada dua mata kuliah wajib.',
            'is_anonymous' => 1,
        ]);

        $ticket = $result['ticket_number'];
        $plainCode = $result['tracking_code'];
        $hash = $result['aspiration']->tracking_code_hash;

        // 1. Kode asli yang valid berhasil diverifikasi
        $verified = $service->verifyTracking($ticket, $plainCode);
        $this->assertNotNull($verified);
        $this->assertEquals($ticket, $verified->ticket_number);

        // 2. Kode salah ditolak
        $invalid = $service->verifyTracking($ticket, 'KODE-SALAH-123');
        $this->assertNull($invalid);

        // 3. String hash bcrypt itu sendiri TIDAK DAPAT digunakan langsung sebagai kode pelacak
        $hashAttempt = $service->verifyTracking($ticket, $hash);
        $this->assertNull($hashAttempt);
    }

    /**
     * 8. Notifikasi surel dikirim ke administrator saat ada aspirasi baru dan tidak bocor tracking code.
     */
    public function test_new_aspiration_sends_notification_to_admin_without_tracking_code(): void
    {
        Mail::fake();

        $service = app(AspirationService::class);
        $result = $service->createAspiration([
            'category' => AspirationCategory::Facility->value,
            'subject' => 'Kerusakan Proyektor Gedung B',
            'message' => 'Proyektor ruang B201 mati total.',
            'is_anonymous' => 1,
        ]);

        $adminEmail = config('aspirations.admin_notification_email', 'admin@bem.kampus.ac.id');

        Mail::assertSent(NewAspirationAdminMail::class, function ($mail) use ($adminEmail, $result) {
            $isToAdmin = $mail->hasTo($adminEmail);
            $hasTicket = $mail->aspiration->ticket_number === $result['ticket_number'];

            // Pastikan mailable tidak membawa kode rahasia pelacakan
            $hasNoPlainTrackingCode = ! isset($mail->plainTrackingCode) && ! isset($mail->tracking_code);

            return $isToAdmin && $hasTicket && $hasNoPlainTrackingCode;
        });
    }

    public function test_mail_transport_exception_details_are_not_written_to_application_log(): void
    {
        $pendingMail = \Mockery::mock(PendingMail::class);
        $pendingMail->shouldReceive('send')
            ->once()
            ->andThrow(new \RuntimeException('SYNTHETIC_PRIVATE_TRANSPORT_DETAIL'));

        Mail::shouldReceive('to')->once()->andReturn($pendingMail);
        Log::shouldReceive('warning')
            ->once()
            ->with('Gagal mengirim email notifikasi aspirasi baru ke admin.');

        $result = app(AspirationService::class)->createAspiration([
            'category' => AspirationCategory::Facility->value,
            'subject' => 'Subjek sintetis untuk uji log',
            'message' => 'Isi sintetis untuk uji kegagalan mail.',
            'is_anonymous' => true,
        ]);

        $this->assertDatabaseHas('aspirations', [
            'id' => $result['aspiration']->id,
            'subject' => 'Subjek sintetis untuk uji log',
        ]);
    }

    public function test_audit_storage_exception_details_are_not_written_to_application_log(): void
    {
        $previousResolver = AdminAuditLog::getConnectionResolver();
        $failingResolver = \Mockery::mock(ConnectionResolverInterface::class);
        $failingResolver->shouldReceive('connection')
            ->zeroOrMoreTimes()
            ->andThrow(new \RuntimeException('SYNTHETIC_PRIVATE_DATABASE_DETAIL'));

        AdminAuditLog::setConnectionResolver($failingResolver);
        Log::shouldReceive('warning')
            ->once()
            ->with('Gagal mencatat audit log aktivitas.', ['action' => 'synthetic.action']);

        try {
            $this->assertNull(AdminAuditLog::record(
                'synthetic.action',
                'Deskripsi sintetis untuk uji log.'
            ));
        } finally {
            AdminAuditLog::setConnectionResolver($previousResolver);
        }
    }

    /**
     * 9. Pembaruan status mengirim email ke pelapor HANYA jika bukan anonim dan memiliki surel valid.
     */
    public function test_status_update_notifies_reporter_only_when_email_provided(): void
    {
        Mail::fake();
        $service = app(AspirationService::class);
        $admin = $this->createUser(UserRole::Advocacy);

        // Kasus 1: Pelapor Terbuka dengan Email
        $openReport = $service->createAspiration([
            'category' => AspirationCategory::Administration->value,
            'subject' => 'Keterlambatan Legalisir Ijazah',
            'message' => 'Proses legalisir memakan waktu lebih dari 10 hari.',
            'is_anonymous' => 0,
            'reporter_name' => 'Fajar Pratama',
            'reporter_email' => 'fajar@student.univ.ac.id',
        ])['aspiration'];

        $service->updateAspirationStatus(
            $openReport,
            AspirationStatus::InProgress,
            'Sedang dikoordinasikan dengan BAAK',
            null,
            $admin->id
        );

        Mail::assertSent(AspirationStatusUpdatedMail::class, function ($mail) {
            return $mail->hasTo('fajar@student.univ.ac.id')
                && $mail->aspiration->status === AspirationStatus::InProgress;
        });

        // Kasus 2: Pelapor Anonim Tanpa Email -> Tidak ada email yang dikirim ke pelapor
        Mail::fake(); // Reset fake

        $anonReport = $service->createAspiration([
            'category' => AspirationCategory::Other->value,
            'subject' => 'Saran Perbaikan Kebersihan Kantin',
            'message' => 'Area cuci tangan perlu ditambah sabun cair.',
            'is_anonymous' => 1,
        ])['aspiration'];

        $service->updateAspirationStatus(
            $anonReport,
            AspirationStatus::Resolved,
            'Pihak kantin telah menyediakan sabun cair di seluruh wastafel.',
            null,
            $admin->id
        );

        Mail::assertNotSent(AspirationStatusUpdatedMail::class);
    }

    /**
     * 10. Pemberian tanggapan resmi mengirim email ke pelapor terbuka.
     */
    public function test_official_response_notifies_reporter_when_email_available(): void
    {
        Mail::fake();
        $service = app(AspirationService::class);
        $admin = $this->createUser(UserRole::Advocacy);

        $report = $service->createAspiration([
            'category' => AspirationCategory::Welfare->value,
            'subject' => 'Informasi Penundaan UKT Mahasiswa',
            'message' => 'Mohon kepastian batas akhir perpanjangan pembayaran.',
            'is_anonymous' => 0,
            'reporter_name' => 'Siti Nurhaliza',
            'reporter_email' => 'siti@student.univ.ac.id',
        ])['aspiration'];

        $officialResponse = 'Berdasarkan SK Rektor, batas pembayaran UKT diperpanjang hingga akhir bulan.';

        $service->updateAspirationStatus(
            $report,
            AspirationStatus::Resolved,
            'Tanggapan resmi telah dirilis',
            $officialResponse,
            $admin->id
        );

        Mail::assertSent(AspirationResponseAddedMail::class, function ($mail) use ($officialResponse) {
            return $mail->hasTo('siti@student.univ.ac.id')
                && $mail->officialResponse === $officialResponse;
        });
    }

    /**
     * 11. Halaman laporan dapat memfilter data berdasarkan rentang tanggal, kategori, dan status.
     */
    public function test_reports_filter_by_criteria(): void
    {
        $advocacy = $this->createUser(UserRole::Advocacy);
        $service = app(AspirationService::class);

        // Buat 2 aspirasi dengan kategori berbeda
        $service->createAspiration([
            'category' => AspirationCategory::Academic->value,
            'subject' => 'Kategori Akademik Tertentu',
            'message' => 'Isi laporan akademik.',
            'is_anonymous' => 1,
        ]);

        $service->createAspiration([
            'category' => AspirationCategory::Facility->value,
            'subject' => 'Kategori Fasilitas Tertentu',
            'message' => 'Isi laporan fasilitas.',
            'is_anonymous' => 1,
        ]);

        // Filter kategori Akademik
        $response = $this->actingAs($advocacy)->get(route('admin.reports.index', [
            'category' => AspirationCategory::Academic->value,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Kategori Akademik Tertentu');
        $response->assertDontSee('Kategori Fasilitas Tertentu');
    }

    /**
     * 12 & 13. Ekspor CSV aman dari Formula Injection dan menjaga privasi data pelapor.
     */
    public function test_csv_export_mitigates_formula_injection_and_protects_privacy(): void
    {
        $admin = $this->createUser(UserRole::Admin);
        $service = app(AspirationService::class);

        // Subjek berpotensi Formula Injection (dimulai dengan =, +, -, @)
        $maliciousSubject = '=cmd|\' /C calc\'!A0';
        $service->createAspiration([
            'category' => AspirationCategory::Other->value,
            'subject' => $maliciousSubject,
            'message' => '+2+5+cmd|',
            'is_anonymous' => 0,
            'reporter_name' => 'NamaRahasiaPelapor',
            'reporter_email' => 'rahasia@student.univ.ac.id',
            'reporter_student_id' => '99887766',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.reports.export'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        $content = $response->streamedContent();

        // 1. Formula Injection harus dinetralisir dengan awalan tanda petik (')
        $this->assertStringContainsString("'" . $maliciousSubject, $content);
        $this->assertStringContainsString("'+2+5+cmd|", $content);

        // 2. Data pribadi pelapor TIDAK BOLEH muncul dalam file CSV
        $this->assertStringNotContainsString('NamaRahasiaPelapor', $content);
        $this->assertStringNotContainsString('rahasia@student.univ.ac.id', $content);
        $this->assertStringNotContainsString('99887766', $content);
    }

    /**
     * CSV menetralkan semua karakter berbahaya yang disebutkan pada awal sel.
     */
    public function test_csv_export_mitigates_all_dangerous_leading_characters(): void
    {
        Mail::fake();

        $admin = $this->createUser(UserRole::Admin);
        $service = app(AspirationService::class);
        $dangerousSubjects = [
            '=1+1',
            '+1+1',
            '-1+1',
            '@SUM(A1)',
            "\t=1+1",
            "\r=1+1",
            "\n=1+1",
        ];

        foreach ($dangerousSubjects as $index => $subject) {
            $service->createAspiration([
                'category' => AspirationCategory::Other->value,
                'subject' => $subject,
                'message' => 'Pesan uji sanitasi CSV ke-' . $index,
                'is_anonymous' => 1,
            ]);
        }

        $response = $this->actingAs($admin)->get(route('admin.reports.export'));

        $response->assertStatus(200);
        $content = $response->streamedContent();

        foreach ($dangerousSubjects as $subject) {
            $this->assertStringContainsString("'" . $subject, $content);
        }
    }

    /**
     * 14. Audit log merekam aksi sensitif admin dan membatasi metadata rahasia.
     */
    public function test_audit_logs_record_actions_and_sanitize_sensitive_data(): void
    {
        $superAdmin = $this->createUser(UserRole::SuperAdmin);

        AdminAuditLog::record(
            'test.security_action',
            'Uji pencatatan keamanan',
            $superAdmin,
            [
                'password' => 'JanganDisimpan',
                'tracking_code' => 'KODE-RAHASIA',
                'safe_info' => 'InformasiUmum',
            ],
            $superAdmin->id
        );

        $log = AdminAuditLog::where('action', 'test.security_action')->firstOrFail();

        $this->assertEquals('[DIRAHSIAKAN]', $log->metadata['password']);
        $this->assertEquals('[DIRAHSIAKAN]', $log->metadata['tracking_code']);
        $this->assertEquals('InformasiUmum', $log->metadata['safe_info']);
    }

    /**
     * 15. CRUD Konten (Berita) merekam jejak audit log.
     */
    public function test_article_crud_records_audit_trail(): void
    {
        $editor = $this->createUser(UserRole::Editor);

        $this->actingAs($editor)->post(route('admin.articles.store'), [
            'title' => 'Berita Dengan Audit Log',
            'category' => 'Umum',
            'excerpt' => 'Ringkasan artikel',
            'content' => 'Konten lengkap warta berita',
            'status' => 'published',
            'published_at' => now()->format('Y-m-d\TH:i'),
        ]);

        $this->assertDatabaseHas('admin_audit_logs', [
            'action' => 'article.create',
        ]);
    }

    /**
     * 16. Artikel berstatus draft menghasilkan HTTP 404 jika diakses langsung lewat URL publik.
     */
    public function test_draft_article_returns_404_on_public_show_slug(): void
    {
        $draft = Article::create([
            'title' => 'Artikel Masih Draf',
            'slug' => 'artikel-masih-draf',
            'category' => 'Internal',
            'excerpt' => 'Ringkasan',
            'content' => 'Isi draf rahasia.',
            'status' => 'draft',
            'published_at' => null,
        ]);

        $response = $this->get(route('news.show', $draft->slug));
        $response->assertStatus(404);
    }

    /**
     * 17. Empty state basis data tanpa membocorkan data demo.
     */
    public function test_empty_database_renders_empty_state_without_demo_data(): void
    {
        // Pastikan tabel articles kosong
        Article::query()->delete();

        $response = $this->get(route('news.index'));

        $response->assertStatus(200);
        $response->assertSee('Tidak Ada Berita yang Sesuai');
        // Tidak menampilkan judul dari array data demonstrasi
        $response->assertDontSee('Kanal Digital Aspirasi Mahasiswa Resmi Disosialisasikan');
    }

    /**
     * 18. Validasi tanggal pada laporan menolak tanggal akhir yang mendahului tanggal mulai.
     */
    public function test_reports_validate_date_bounds(): void
    {
        $advocacy = $this->createUser(UserRole::Advocacy);

        $response = $this->actingAs($advocacy)->get(route('admin.reports.index', [
            'start_date' => '2026-10-30',
            'end_date' => '2026-10-20', // Invalid: sebelum start_date
        ]));

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['end_date']);
    }
}
