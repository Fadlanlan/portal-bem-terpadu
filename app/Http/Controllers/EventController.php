<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class EventController extends Controller
{
    /**
     * Dapatkan daftar kegiatan/agenda demonstrasi.
     *
     * @return array<int, array<string, mixed>>
     */
    private function getEvents(): array
    {
        return [
            [
                'slug' => 'dialog-terbuka-rektorat-mahasiswa-2026',
                'title' => 'Dialog Terbuka: Refleksi Kebijakan Kampus & Mahasiswa',
                'category' => 'Audiensi & Kebijakan',
                'date_raw' => '2026-11-07',
                'date_display' => 'Sabtu, 07 November 2026',
                'time_display' => '09:00 - 12:30 WIB',
                'location' => 'Auditorium Gedung Utama Lt. 3',
                'organizer' => 'BEM & Dewan Perwakilan Mahasiswa',
                'is_registration_open' => true,
                'is_upcoming' => true,
                'description' => "Forum dialog terbuka tahunan antara perwakilan sivitas akademika mahasiswa dan jajaran pimpinan rektorat. Forum ini mengulas capaian fasilitas kampus, arah pengembangan kurikulum, serta kebijakan pendanaan kegiatan mahasiswa.\n\nPeserta terbuka bagi seluruh mahasiswa aktif yang telah melakukan konfirmasi kehadiran melalui tautan yang disediakan.",
                'speakers' => [
                    'Jajaran Wakil Rektor Bidang Akademik & Kemahasiswaan',
                    'Ketua BEM Terpilih Periode 2026/2027',
                    'Ketua Presidium MAM',
                ],
                'agenda_points' => [
                    'Penyampaian naskah aspirasi hasil konsolidasi mahasiswa.',
                    'Tanggapan resmi jajaran rektorat terkait sarana prasarana.',
                    'Sesi tanya jawab terbuka delegasi fakultas.',
                    'Penandatanganan nota kesepahaman komitmen bersama.',
                ],
                'registration_note' => 'Pendaftaran perwakilan delegasi dibuka hingga 05 November 2026 pukul 23:59 WIB.',
            ],
            [
                'slug' => 'sidang-pleno-mahasiswa-semester-ganjil',
                'title' => 'Sidang Pleno Evaluasi Paruh Periode Organisasi',
                'category' => 'Permusyawaratan',
                'date_raw' => '2026-11-14',
                'date_display' => 'Sabtu, 14 November 2026',
                'time_display' => '13:00 - 17:00 WIB',
                'location' => 'Ruang Sidang Paripurna MAM',
                'organizer' => 'Majelis Aspirasi Mahasiswa (MAM)',
                'is_registration_open' => false,
                'is_upcoming' => true,
                'description' => "Sidang resmi permusyawaratan mahasiswa guna mendengarkan laporan progres paruh semester pelaksanaan program kerja BEM serta penetapan rekomendasi korektif legislatif.\n\nSidang dihadiri oleh utusan sah dari seluruh Himpunan Mahasiswa Jurusan/Prodi dan Unit Kegiatan Mahasiswa.",
                'speakers' => [
                    'Presidium Sidang Majelis Aspirasi Mahasiswa',
                    'Badan Pengurus Harian BEM',
                ],
                'agenda_points' => [
                    'Pembacaan tata tertib persidangan pleno.',
                    'Pemaparan capaian indikator kerja kementerian BEM.',
                    'Pandangan umum fraksi HIMA & UKM.',
                    'Pengesahan ketetapan sidang pleno tengah periode.',
                ],
                'registration_note' => 'Khusus delegasi resmi ormawa yang telah terverifikasi mandat resminya.',
            ],
            [
                'slug' => 'festival-kebangsaan-dan-budaya-nusantara',
                'title' => 'Pekan Seni & Pameran Inovasi Ormawa 2026',
                'category' => 'Seni & Budaya',
                'date_raw' => '2026-11-28',
                'date_display' => 'Sabtu, 28 November 2026',
                'time_display' => '08:00 - 21:00 WIB',
                'location' => 'Plaza Kampus Terpadu & Lapangan Terbuka',
                'organizer' => 'BEM & Seluruh Unit Kegiatan Mahasiswa',
                'is_registration_open' => true,
                'is_upcoming' => true,
                'description' => "Pesta kebudayaan dan unjuk karya inovasi terbesar di lingkungan kampus. Menampilkan kolaborasi pertunjukan tari nusantara, teater kampus, stan pameran prototipe karya teknologi mahasiswa, dan festival kuliner daerah.\n\nAcara terbuka untuk seluruh keluarga besar kampus dan masyarakat umum pecinta seni budaya.",
                'speakers' => [
                    'Kurator Budaya & Pegiat Seni Kampus',
                    'Tokoh Muda Inspiratif Nasional',
                ],
                'agenda_points' => [
                    'Pawai parade kostum ormawa nusantara.',
                    'Panggung ekspresi musik & sastra mahasiswa.',
                    'Eksibisi prototipe teknologi dan poster riset.',
                    'Malam apresiasi talenta muda berprestasi.',
                ],
                'registration_note' => 'Terbuka gratis. Pengunjung diharapkan mengenakan busana bernuansa etnik nusantara.',
            ],
            [
                'slug' => 'kuliah-umum-kepemimpinan-integritas-bangsa',
                'title' => 'Kuliah Umum: Kepemimpinan Etis di Era Disrupsi Digital',
                'category' => 'Kuliah Umum & Webinar',
                'date_raw' => '2026-12-10',
                'date_display' => 'Kamis, 10 Desember 2026',
                'time_display' => '10:00 - 12:30 WIB',
                'location' => 'Hall Serbaguna & Live Streaming YouTube Kampus',
                'organizer' => 'Kementerian PSDM BEM',
                'is_registration_open' => true,
                'is_upcoming' => true,
                'description' => "Sesi pencerahan pemikiran mengenai tantangan integritas kepemimpinan bagi generasi muda di tengah maraknya polarisasi informasi dan percepatan kecerdasan buatan.",
                'speakers' => [
                    'Akademisi Etika Kebijakan Publik',
                    'Alumni Inspiratif Pegiat Tata Kelola Pemerintahan',
                ],
                'agenda_points' => [
                    'Pemaparan studi kasus kepemimpinan publik modern.',
                    'Sesi bedah gagasan interaktif bersama peserta.',
                    'Peluncuran buku antologi esai mahasiswa.',
                ],
                'registration_note' => 'Kuota luring terbatas untuk 300 pendaftar pertama bersertifikat digital.',
            ],
            [
                'slug' => 'sidang-istimewa-pelantikan-pengurus-2026',
                'title' => 'Sidang Istimewa Pengukuhan & Pelantikan Pengurus BEM',
                'category' => 'Protokoler',
                'date_raw' => '2026-09-01',
                'date_display' => 'Selasa, 01 September 2026',
                'time_display' => '09:00 - 12:00 WIB',
                'location' => 'Gedung Pertemuan Graha Wiyata',
                'organizer' => 'Majelis Aspirasi Mahasiswa',
                'is_registration_open' => false,
                'is_upcoming' => false,
                'description' => 'Upacara khidmat pelantikan jajaran pengurus Badan Eksekutif Mahasiswa terpilih periode 2026/2027 beserta pembacaan pakta integritas amanah kepengurusan.',
                'speakers' => [
                    'Rektor Universitas',
                    'Presiden Mahasiswa Terpilih',
                ],
                'agenda_points' => [
                    'Pengambilan sumpah jabatan pengurus.',
                    'Pidato perdana Presiden Mahasiswa.',
                    'Penyerahan naskah memori kepengurusan demisioner.',
                ],
                'registration_note' => 'Kegiatan telah terlaksana secara resmi.',
            ],
            [
                'slug' => 'orientasi-keorganisasian-mahasiswa-baru-2026',
                'title' => 'Pekan Pengenalan Lembaga Kemahasiswaan (Ormawa Expo)',
                'category' => 'Pengenalan Ormawa',
                'date_raw' => '2026-09-15',
                'date_display' => 'Selasa - Kamis, 15 - 17 September 2026',
                'time_display' => '08:00 - 16:30 WIB',
                'location' => 'Seluruh Koridor Gedung Kampus',
                'organizer' => 'Panitia Bersama BEM & UKM',
                'is_registration_open' => false,
                'is_upcoming' => false,
                'description' => 'Ajang pengenalan profil seluruh Unit Kegiatan Mahasiswa, Himpunan Mahasiswa, dan lembaga legislatif kepada ribuan mahasiswa baru angkatan 2026.',
                'speakers' => [
                    'Ketua Tiap-Tiap Unit Kegiatan Mahasiswa',
                    'Koordinator Expo Kemahasiswaan',
                ],
                'agenda_points' => [
                    'Eksibisi pertunjukan tiap divisi UKM.',
                    'Konsultasi peminatan bakat mahasiswa baru.',
                    'Pendaftaran terbuka anggota baru organisasi.',
                ],
                'registration_note' => 'Kegiatan telah selesai dilaksanakan.',
            ],
        ];
    }

    /**
     * Tampilkan agenda mendatang dan riwayat agenda.
     */
    public function index(): View
    {
        $hasDb = false;
        try {
            $hasDb = Schema::hasTable('events');
        } catch (\Throwable $e) {
            $hasDb = false;
        }

        if ($hasDb) {
            $upcomingEvents = Event::published()
                ->where('started_at', '>=', now()->startOfDay())
                ->orderBy('started_at', 'asc')
                ->get();

            $pastEvents = Event::published()
                ->where('started_at', '<', now()->startOfDay())
                ->orderBy('started_at', 'desc')
                ->get();

            return view('pages.events.index', [
                'upcomingEvents' => $upcomingEvents,
                'pastEvents' => $pastEvents,
            ]);
        }

        $allEvents = $this->getEvents();

        $upcomingEvents = array_filter($allEvents, fn (array $e): bool => (bool) $e['is_upcoming']);
        $pastEvents = array_filter($allEvents, fn (array $e): bool => ! ((bool) $e['is_upcoming']));

        return view('pages.events.index', [
            'upcomingEvents' => array_values($upcomingEvents),
            'pastEvents' => array_values($pastEvents),
        ]);
    }

    /**
     * Tampilkan detail acara/agenda berdasarkan slug.
     */
    public function show(string $slug): View
    {
        $hasDb = false;
        try {
            $hasDb = Schema::hasTable('events');
        } catch (\Throwable $e) {
            $hasDb = false;
        }

        if ($hasDb) {
            $event = Event::published()->where('slug', $slug)->first();

            if (! $event) {
                abort(404, 'Agenda kegiatan tidak ditemukan.');
            }

            $otherEvents = Event::published()
                ->where('slug', '!=', $event->slug)
                ->where('started_at', $event->is_upcoming ? '>=' : '<', now()->startOfDay())
                ->take(2)
                ->get();

            return view('pages.events.show', [
                'event' => $event,
                'otherEvents' => $otherEvents,
            ]);
        }

        $allEvents = $this->getEvents();
        $event = null;

        foreach ($allEvents as $item) {
            if ($item['slug'] === $slug) {
                $event = $item;
                break;
            }
        }

        if (! $event) {
            abort(404, 'Agenda kegiatan tidak ditemukan.');
        }

        $otherEvents = array_filter($allEvents, function (array $item) use ($event): bool {
            return $item['slug'] !== $event['slug'] && $item['is_upcoming'] === $event['is_upcoming'];
        });

        return view('pages.events.show', [
            'event' => $event,
            'otherEvents' => array_slice(array_values($otherEvents), 0, 2),
        ]);
    }
}
