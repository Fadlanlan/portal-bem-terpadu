<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Event;
use App\Models\WorkProgram;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Tampilkan halaman utama (Beranda) Portal BEM Terpadu.
     */
    public function index(): View
    {
        $featuredNews = [
            [
                'slug' => 'penerimaan-aspirasi-terpadu-periode-2026',
                'title' => 'Kanal Digital Aspirasi Mahasiswa Resmi Disosialisasikan',
                'excerpt' => 'BEM meluncurkan inisiatif kanal terpadu untuk memastikan setiap suara, kritik konstruktif, dan saran mahasiswa terkelola secara transparan dan akuntabel.',
                'category' => 'Advokasi & Aspirasi',
                'date' => '24 Okt 2026',
                'author' => 'Biro Komunikasi & Informasi',
                'read_time' => '3 mnt',
            ],
            [
                'slug' => 'pembukaan-pendaftaran-delegasi-studi-banding',
                'title' => 'Pendaftaran Delegasi Forum Mahasiswa Nasional Telah Dibuka',
                'excerpt' => 'Kesempatan bagi perwakilan organisasi kemahasiswaan untuk berpartisipasi dalam pertukaran gagasan kepemimpinan di tingkat perguruan tinggi se-Indonesia.',
                'category' => 'Pengumuman',
                'date' => '22 Okt 2026',
                'author' => 'Kementerian Luar Negeri BEM',
                'read_time' => '4 mnt',
            ],
            [
                'slug' => 'lokakarya-manajemen-keorganisasian-2026',
                'title' => 'Lokakarya Tata Kelola Organisasi Mahasiswa Berbasis Digital',
                'excerpt' => 'Pelatihan intensif bagi jajaran pengurus UKM, HIMA, dan HMPS guna memperkuat transparansi administrasi serta sinergi kegiatan antarkampus.',
                'category' => 'Pelatihan',
                'date' => '19 Okt 2026',
                'author' => 'Kementerian Dalam Negeri BEM',
                'read_time' => '5 mnt',
            ],
        ];

        $featuredPrograms = [
            [
                'slug' => 'advokasi-transparansi-biaya-akademik',
                'name' => 'Advokasi Transparansi & Bantuan Finansial',
                'organization' => 'Kementerian Sosial & Advokasi BEM',
                'status' => 'Berlangsung',
                'period' => 'Semester Ganjil 2026/2027',
                'description' => 'Pendampingan langsung bagi mahasiswa yang membutuhkan keringanan UKT, beasiswa kemitraan, serta transparansi fasilitas kampus.',
            ],
            [
                'slug' => 'inkubator-kreativitas-riset-mahasiswa',
                'name' => 'Inkubator Kreativitas & Riset Ilmiah Mahasiswa',
                'organization' => 'Kementerian Riset & Keilmuan BEM',
                'status' => 'Rencana',
                'period' => 'Semester Genap 2026/2027',
                'description' => 'Wadah mentoring penulisan proposal PKM, pendanaan riset independen, dan fasilitasi publikasi ilmiah skala nasional.',
            ],
            [
                'slug' => 'bem-mengabdi-pelosok-daerah',
                'name' => 'BEM Mengabdi: Desa Binaan Berkelanjutan',
                'organization' => 'Kementerian Pengabdian Masyarakat BEM',
                'status' => 'Berlangsung',
                'period' => 'Oktober - Desember 2026',
                'description' => 'Program pemberdayaan literasi dan digitalisasi pemasaran UMKM di wilayah desa mitra binaan universitas.',
            ],
        ];

        $upcomingEvents = [
            [
                'slug' => 'dialog-terbuka-rektorat-mahasiswa-2026',
                'title' => 'Dialog Terbuka: Refleksi Kebijakan Kampus & Mahasiswa',
                'date' => '07 Nov 2026',
                'time' => '09:00 - 12:30 WIB',
                'location' => 'Auditorium Gedung Utama Lt. 3',
                'organizer' => 'BEM & DPM',
                'is_registration_open' => true,
            ],
            [
                'slug' => 'sidang-pleno-mahasiswa-semester-ganjil',
                'title' => 'Sidang Pleno Evaluasi Paruh Periode Organisasi',
                'date' => '14 Nov 2026',
                'time' => '13:00 - 17:00 WIB',
                'location' => 'Ruang Sidang Paripurna MAM',
                'organizer' => 'Majelis Aspirasi Mahasiswa',
                'is_registration_open' => false,
            ],
            [
                'slug' => 'festival-kebangsaan-dan-budaya-nusantara',
                'title' => 'Pekan Seni & Pameran Inovasi Ormawa 2026',
                'date' => '28 Nov 2026',
                'time' => '08:00 - 21:00 WIB',
                'location' => 'Plaza Kampus Terpadu',
                'organizer' => 'BEM & Unit Kegiatan Mahasiswa',
                'is_registration_open' => true,
            ],
        ];

        $organizationsOverview = [
            [
                'name' => 'BEM (Badan Eksekutif Mahasiswa)',
                'badge' => 'Eksekutif',
                'desc' => 'Lembaga eksekutif tingkat perguruan tinggi yang bertugas mengoordinasikan implementasi program kemahasiswaan dan advokasi kebijakan.',
            ],
            [
                'name' => 'MAM & DPM (Lembaga Perwakilan)',
                'badge' => 'Legislatif & Aspiratif',
                'desc' => 'Badan perwakilan dan permusyawaratan mahasiswa yang menjalankan fungsi legislasi, pengawasan anggaran, serta penyerapan aspirasi.',
            ],
            [
                'name' => 'HIMA & HMPS (Tingkat Fakultas/Prodi)',
                'badge' => 'Fakultas & Keilmuan',
                'desc' => 'Himpunan mahasiswa pada level fakultas dan program studi yang berfokus pada pengembangan profesi, akademik, dan solidaritas jurusan.',
            ],
            [
                'name' => 'UKM (Unit Kegiatan Mahasiswa)',
                'badge' => 'Minat & Bakat',
                'desc' => 'Wadah pembinaan potensi mahasiswa di bidang penalaran, seni budaya, keolahragaan, kepramukaan, dan keagamaan.',
            ],
        ];

        // Jika basis data memiliki tabel dan telah dimigrasi, utamakan data resmi dari database
        try {
            if (Schema::hasTable('articles')) {
                $featuredNews = Article::published()->latest('published_at')->take(3)->get();
            }
            if (Schema::hasTable('work_programs')) {
                $featuredPrograms = WorkProgram::published()->latest('created_at')->take(3)->get();
            }
            if (Schema::hasTable('events')) {
                $upcomingEvents = Event::published()->where('started_at', '>=', now()->startOfDay())->orderBy('started_at', 'asc')->take(3)->get();
            }
        } catch (\Throwable $e) {
            // Gunakan fallback array jika tabel belum siap
        }

        return view('pages.home', compact(
            'featuredNews',
            'featuredPrograms',
            'upcomingEvents',
            'organizationsOverview'
        ));
    }
}
