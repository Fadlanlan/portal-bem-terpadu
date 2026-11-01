<?php

namespace Tests\Feature;

use App\Enums\AspirationCategory;
use App\Enums\AspirationStatus;
use App\Models\Aspiration;
use App\Models\AspirationStatusHistory;
use App\Services\AspirationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class AspirationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Halaman formulir aspirasi dapat diakses dan menampilkan komponen utama.
     */
    public function test_create_aspiration_form_page_is_accessible(): void
    {
        $response = $this->get(route('aspirations.create'));

        $response->assertStatus(200);
        $response->assertSee('Sampaikan Aspirasi Mahasiswa');
        $response->assertSee('Fasilitas Kampus');
        $response->assertSee('Pengaturan Privasi Pelapor');
        $response->assertSee('Kirim Aspirasi Sekarang');
    }

    /**
     * Pengiriman aspirasi valid (terbuka/non-anonim) berhasil disimpan dan diarahkan ke tanda terima.
     */
    public function test_valid_aspiration_submission_creates_record_and_redirects_to_success(): void
    {
        $payload = [
            'category' => AspirationCategory::Facility->value,
            'subject' => 'Kerusakan Pendingin Ruangan di Gedung C',
            'message' => 'Pendingin ruangan di lantai 3 Gedung C tidak berfungsi optimal selama dua pekan terakhir.',
            'is_anonymous' => 0,
            'reporter_name' => 'Ahmad Fauzi',
            'reporter_email' => 'ahmad.fauzi@student.univ.ac.id',
            'reporter_student_id' => '2023010045',
            'privacy_consent' => 1,
        ];

        $response = $this->post(route('aspirations.store'), $payload);

        $response->assertRedirect(route('aspirations.success'));
        $response->assertSessionHas('ticket_number');
        $response->assertSessionHas('tracking_code');

        $this->assertDatabaseHas('aspirations', [
            'category' => AspirationCategory::Facility->value,
            'subject' => 'Kerusakan Pendingin Ruangan di Gedung C',
            'reporter_name' => 'Ahmad Fauzi',
            'reporter_email' => 'ahmad.fauzi@student.univ.ac.id',
            'reporter_student_id' => '2023010045',
            'is_anonymous' => false,
            'status' => AspirationStatus::Submitted->value,
        ]);
    }

    /**
     * Setiap pengiriman aspirasi menghasilkan nomor tiket yang unik.
     */
    public function test_ticket_number_is_unique_for_each_submission(): void
    {
        $payload = [
            'category' => AspirationCategory::Academic->value,
            'subject' => 'Jadwal Ujian Akhir Semester Bentrok',
            'message' => 'Terdapat bentrok jadwal ujian mata kuliah wajib semester empat pada hari Kamis.',
            'is_anonymous' => 1,
            'privacy_consent' => 1,
        ];

        $response1 = $this->post(route('aspirations.store'), $payload);
        $ticket1 = session('ticket_number');

        $response2 = $this->post(route('aspirations.store'), $payload);
        $ticket2 = session('ticket_number');

        $this->assertNotEmpty($ticket1);
        $this->assertNotEmpty($ticket2);
        $this->assertNotEquals($ticket1, $ticket2);
        $this->assertStringStartsWith('BEM-' . date('Y') . '-', $ticket1);
        $this->assertStringStartsWith('BEM-' . date('Y') . '-', $ticket2);
    }

    /**
     * Riwayat status awal (submitted) otomatis dibuat saat aspirasi tersimpan.
     */
    public function test_initial_status_history_is_created_with_submitted_status(): void
    {
        $service = app(AspirationService::class);
        $result = $service->createAspiration([
            'category' => AspirationCategory::Academic->value,
            'subject' => 'Permohonan Bantuan Banding UKT',
            'message' => 'Mohon pendampingan advokasi untuk proses banding UKT semester gasal mendatang.',
            'is_anonymous' => true,
        ]);

        /** @var Aspiration $aspiration */
        $aspiration = $result['aspiration'];

        $this->assertCount(1, $aspiration->statusHistories);
        $history = $aspiration->statusHistories->first();

        $this->assertNull($history->old_status);
        $this->assertEquals(AspirationStatus::Submitted, $history->new_status);
        $this->assertStringContainsString('diterima oleh sistem', $history->note);
    }

    /**
     * Pengiriman dengan kategori yang tidak terdaftar ditolak oleh validasi.
     */
    public function test_submission_with_invalid_category_is_rejected(): void
    {
        $response = $this->post(route('aspirations.store'), [
            'category' => 'kategori_fiktif_tidak_ada',
            'subject' => 'Judul aspirasi mahasiswa valid',
            'message' => 'Uraian isi aspirasi yang cukup panjang dan memenuhi batas minimal karakter.',
            'is_anonymous' => 1,
            'privacy_consent' => 1,
        ]);

        $response->assertSessionHasErrors('category');
        $this->assertEquals(0, Aspiration::count());
    }

    /**
     * Pengiriman dengan subjek atau pesan kosong ditolak.
     */
    public function test_submission_with_empty_subject_and_message_is_rejected(): void
    {
        $response = $this->post(route('aspirations.store'), [
            'category' => AspirationCategory::Welfare->value,
            'subject' => '',
            'message' => '',
            'is_anonymous' => 1,
            'privacy_consent' => 1,
        ]);

        $response->assertSessionHasErrors(['subject', 'message']);
        $this->assertEquals(0, Aspiration::count());
    }

    /**
     * Pengiriman dengan subjek atau pesan kurang dari panjang minimum ditolak.
     */
    public function test_submission_with_too_short_subject_and_message_is_rejected(): void
    {
        $response = $this->post(route('aspirations.store'), [
            'category' => AspirationCategory::Welfare->value,
            'subject' => 'Halo', // min: 5
            'message' => 'Kurang panjang', // min: 15
            'is_anonymous' => 1,
            'privacy_consent' => 1,
        ]);

        $response->assertSessionHasErrors(['subject', 'message']);
        $this->assertEquals(0, Aspiration::count());
    }

    /**
     * Pengiriman tanpa persetujuan privasi ditolak.
     */
    public function test_submission_without_privacy_consent_is_rejected(): void
    {
        $response = $this->post(route('aspirations.store'), [
            'category' => AspirationCategory::Feedback->value,
            'subject' => 'Saran Perbaikan Sistem Presensi',
            'message' => 'Saran penyesuaian waktu toleransi keterlambatan pada sistem presensi online.',
            'is_anonymous' => 1,
            // privacy_consent diabaikan
        ]);

        $response->assertSessionHasErrors('privacy_consent');
        $this->assertEquals(0, Aspiration::count());
    }

    /**
     * Mode non-anonim mewajibkan pengisian nama dan email yang valid.
     */
    public function test_non_anonymous_mode_requires_name_and_valid_email(): void
    {
        $response = $this->post(route('aspirations.store'), [
            'category' => AspirationCategory::Feedback->value,
            'subject' => 'Saran Perbaikan Sarana Parkir Kampus',
            'message' => 'Penataan rambu parkir kendaraan roda dua di gerbang barat perlu diperjelas.',
            'is_anonymous' => 0,
            'reporter_name' => '',
            'reporter_email' => 'format-email-salah',
            'privacy_consent' => 1,
        ]);

        $response->assertSessionHasErrors(['reporter_name', 'reporter_email']);
        $this->assertEquals(0, Aspiration::count());
    }

    /**
     * Mode anonim membersihkan identitas pelapor dan menyimpannya sebagai NULL di basis data.
     */
    public function test_anonymous_mode_sanitizes_identity_and_stores_null(): void
    {
        $payload = [
            'category' => AspirationCategory::Administration->value,
            'subject' => 'Transparansi Rincian Biaya Praktikum Laboratorium',
            'message' => 'Mohon transparansi alokasi rincian biaya praktikum pada semester genap.',
            'is_anonymous' => 1,
            'reporter_name' => 'Data Nama Yang Terkirim Tak Sengaja',
            'reporter_email' => 'surel.rahasia@univ.ac.id',
            'reporter_student_id' => '199901001',
            'privacy_consent' => 1,
        ];

        $response = $this->post(route('aspirations.store'), $payload);

        $response->assertRedirect(route('aspirations.success'));

        $aspiration = Aspiration::first();
        $this->assertNotNull($aspiration);
        $this->assertTrue($aspiration->is_anonymous);
        $this->assertNull($aspiration->reporter_name);
        $this->assertNull($aspiration->reporter_email);
        $this->assertNull($aspiration->reporter_student_id);
    }

    /**
     * Pengguna tidak dapat memanipulasi status atau balasan admin melalui payload form publik.
     */
    public function test_submission_cannot_tamper_with_status_or_admin_response(): void
    {
        $payload = [
            'category' => AspirationCategory::Academic->value,
            'subject' => 'Permintaan Akses Perpanjangan Peminjaman Buku',
            'message' => 'Mohon durasi peminjaman buku perpustakaan untuk skripsi dapat diperpanjang.',
            'is_anonymous' => 1,
            'privacy_consent' => 1,
            'status' => 'resolved',
            'admin_response' => 'Disetujui secara otomatis oleh pelapor fiktif.',
        ];

        $response = $this->post(route('aspirations.store'), $payload);

        $response->assertRedirect(route('aspirations.success'));

        $aspiration = Aspiration::first();
        $this->assertEquals(AspirationStatus::Submitted, $aspiration->status);
        $this->assertNull($aspiration->admin_response);
    }

    /**
     * Halaman tanda terima menampilkan nomor tiket dan kode rahasia jika sesi tersedia.
     */
    public function test_success_page_displays_credentials_when_session_present(): void
    {
        $response = $this->withSession([
            'ticket_number' => 'BEM-2026-X99999',
            'tracking_code' => 'TRK-AAAA-BBBB-CCCC',
        ])->get(route('aspirations.success'));

        $response->assertStatus(200);
        $response->assertSee('Aspirasi Anda Telah Diterima');
        $response->assertSee('BEM-2026-X99999');
        $response->assertSee('TRK-AAAA-BBBB-CCCC');
        $response->assertSee('Lacak Status Tiket Ini');
    }

    /**
     * Halaman tanda terima diarahkan kembali ke halaman informasi jika diakses tanpa sesi tiket.
     */
    public function test_success_page_redirects_when_session_is_missing(): void
    {
        $response = $this->get(route('aspirations.success'));

        $response->assertRedirect(route('aspirations.info'));
        $response->assertSessionHas('status_notice');
    }

    /**
     * Halaman formulir pelacakan status aspirasi dapat diakses.
     */
    public function test_tracking_form_page_is_accessible(): void
    {
        $response = $this->get(route('aspirations.tracking.form'));

        $response->assertStatus(200);
        $response->assertSee('Lacak Status Aspirasi');
        $response->assertSee('Nomor Tiket Pelacakan');
        $response->assertSee('Kode Pelacakan Rahasia');
        $response->assertSee('Periksa Status Laporan');
    }

    /**
     * Pelacakan dengan nomor tiket dan kode rahasia yang sah berhasil menampilkan detail aspirasi.
     */
    public function test_valid_tracking_displays_aspiration_details(): void
    {
        $service = app(AspirationService::class);
        $created = $service->createAspiration([
            'category' => AspirationCategory::Activity->value,
            'subject' => 'Dukungan Pendanaan Delegasi Lomba Debat Nasional',
            'message' => 'Permohonan bantuan akomodasi dan transportasi tim debat mahasiswa ke tingkat nasional.',
            'is_anonymous' => 1,
        ]);

        $response = $this->post(route('aspirations.tracking.show'), [
            'ticket_number' => $created['ticket_number'],
            'tracking_code' => $created['tracking_code'],
        ]);

        $response->assertStatus(200);
        $response->assertSee($created['ticket_number']);
        $response->assertSee('Dukungan Pendanaan Delegasi Lomba Debat Nasional');
        $response->assertSee('Permohonan bantuan akomodasi dan transportasi tim debat mahasiswa ke tingkat nasional.');
        $response->assertSee('Diterima');
        $response->assertSee('Riwayat Jejak Penanganan Laporan');
    }

    /**
     * Halaman pelacakan status tidak pernah menampilkan identitas nama dan email pelapor.
     */
    public function test_tracking_result_never_exposes_reporter_identity(): void
    {
        $service = app(AspirationService::class);
        $created = $service->createAspiration([
            'category' => AspirationCategory::Activity->value,
            'subject' => 'Laporan Permasalahan Nilai Akademik Terlambat Terbit',
            'message' => 'Nilai mata kuliah belum keluar melewati batas akhir pengisian kartu rencana studi.',
            'is_anonymous' => 0,
            'reporter_name' => 'Budi Raharja Mahasiswa',
            'reporter_email' => 'budi.raharja@univ.ac.id',
            'reporter_student_id' => '1122334455',
        ]);

        $response = $this->post(route('aspirations.tracking.show'), [
            'ticket_number' => $created['ticket_number'],
            'tracking_code' => $created['tracking_code'],
        ]);

        $response->assertStatus(200);
        $response->assertSee('Laporan Permasalahan Nilai Akademik Terlambat Terbit');
        $response->assertDontSee('Budi Raharja Mahasiswa');
        $response->assertDontSee('budi.raharja@univ.ac.id');
        $response->assertDontSee('1122334455');
    }

    /**
     * Pelacakan dengan kode rahasia yang salah menghasilkan pesan kesalahan generik dan tidak membocorkan data.
     */
    public function test_tracking_with_invalid_code_returns_generic_error(): void
    {
        $service = app(AspirationService::class);
        $created = $service->createAspiration([
            'category' => AspirationCategory::Facility->value,
            'subject' => 'Lampu Lapangan Olahraga Mati',
            'message' => 'Penerangan lapangan futsal outdoor mati menyebabkan kegiatan sore terganggu.',
            'is_anonymous' => 1,
        ]);

        $response = $this->from(route('aspirations.tracking.form'))->post(route('aspirations.tracking.show'), [
            'ticket_number' => $created['ticket_number'],
            'tracking_code' => 'TRK-WRON-GCOD-E999',
        ]);

        $response->assertRedirect(route('aspirations.tracking.form'));
        $response->assertSessionHasErrors('tracking_error');
        $response->assertDontSee('Lampu Lapangan Olahraga Mati');
    }

    /**
     * Pelacakan dengan nomor tiket fiktif menghasilkan pesan kesalahan generik yang seragam.
     */
    public function test_tracking_with_non_existent_ticket_returns_same_generic_error(): void
    {
        $response = $this->from(route('aspirations.tracking.form'))->post(route('aspirations.tracking.show'), [
            'ticket_number' => 'BEM-2026-FIKTIF',
            'tracking_code' => 'TRK-ABCD-EFGH-IJKL',
        ]);

        $response->assertRedirect(route('aspirations.tracking.form'));
        $response->assertSessionHasErrors([
            'tracking_error' => 'Kombinasi nomor tiket dan kode pelacakan rahasia tidak ditemukan atau tidak cocok.',
        ]);
    }

    /**
     * Pelacakan menampilkan respons resmi pengelola dan linimasa riwayat status multi-tahap.
     */
    public function test_tracking_displays_admin_response_and_audit_timeline(): void
    {
        $service = app(AspirationService::class);
        $created = $service->createAspiration([
            'category' => AspirationCategory::Facility->value,
            'subject' => 'Usulan Penambahan Stop Kontak di Gazebo Kampus',
            'message' => 'Banyak mahasiswa belajar kelompok di gazebo tetapi stop kontak listrik sangat minim.',
            'is_anonymous' => 1,
        ]);

        /** @var Aspiration $aspiration */
        $aspiration = $created['aspiration'];

        // Simulasi tindak lanjut pengelola
        $aspiration->update([
            'status' => AspirationStatus::InProgress,
            'admin_response' => 'Kementerian Advokasi BEM telah mengoordinasikan pengadaan kabel dan colokan dengan Biro Sarana Prasarana.',
        ]);

        AspirationStatusHistory::create([
            'aspiration_id' => $aspiration->id,
            'old_status' => AspirationStatus::Submitted,
            'new_status' => AspirationStatus::InProgress,
            'note' => 'Berkas laporan telah diverifikasi dan masuk proses pengerjaan lapangan bersama tim teknis kampus.',
            'changed_by' => 1,
        ]);

        $response = $this->post(route('aspirations.tracking.show'), [
            'ticket_number' => $created['ticket_number'],
            'tracking_code' => $created['tracking_code'],
        ]);

        $response->assertStatus(200);
        $response->assertSee('Sedang Ditindaklanjuti');
        $response->assertSee('Kementerian Advokasi BEM telah mengoordinasikan pengadaan kabel dan colokan dengan Biro Sarana Prasarana.');
        $response->assertSee('Berkas laporan telah diverifikasi dan masuk proses pengerjaan lapangan bersama tim teknis kampus.');
    }

    /**
     * Rate limiter membatasi pengiriman aspirasi yang melebihi batas (5 kali/menit per IP).
     */
    public function test_submission_rate_limiter_throttles_excessive_requests(): void
    {
        $ip = '192.168.10.55';
        RateLimiter::clear('aspiration-submission:' . $ip);

        $payload = [
            'category' => AspirationCategory::Academic->value,
            'subject' => 'Uji Coba Pembatasan Frekuensi Kirim',
            'message' => 'Pesan pengujian sistem pembatasan laju pengiriman aspirasi mahasiswa terpadu.',
            'is_anonymous' => 1,
            'privacy_consent' => 1,
        ];

        // 5 pengiriman pertama diizinkan
        for ($i = 0; $i < 5; $i++) {
            $response = $this->withServerVariables(['REMOTE_ADDR' => $ip])
                ->post(route('aspirations.store'), $payload);
            $response->assertRedirect(route('aspirations.success'));
        }

        // Pengiriman ke-6 diblokir (429 Too Many Requests)
        $response6 = $this->withServerVariables(['REMOTE_ADDR' => $ip])
            ->post(route('aspirations.store'), $payload);
        $response6->assertStatus(429);
    }

    /**
     * Rate limiter membatasi pelacakan aspirasi yang melebihi batas (10 kali/menit per IP).
     */
    public function test_tracking_rate_limiter_throttles_excessive_requests(): void
    {
        $ip = '192.168.10.77';
        RateLimiter::clear('aspiration-tracking:' . $ip);

        // 10 pengecekan pertama diizinkan (gagal validasi kredensial fiktif tetapi tidak kena 429)
        for ($i = 0; $i < 10; $i++) {
            $response = $this->withServerVariables(['REMOTE_ADDR' => $ip])
                ->post(route('aspirations.tracking.show'), [
                    'ticket_number' => 'BEM-2026-FIKTIF',
                    'tracking_code' => 'TRK-ABCD-EFGH-IJKL',
                ]);
            $response->assertStatus(302);
        }

        // Percobaan ke-11 diblokir (429 Too Many Requests)
        $response11 = $this->withServerVariables(['REMOTE_ADDR' => $ip])
            ->post(route('aspirations.tracking.show'), [
                'ticket_number' => 'BEM-2026-FIKTIF',
                'tracking_code' => 'TRK-ABCD-EFGH-IJKL',
            ]);
        $response11->assertStatus(429);
    }
}
