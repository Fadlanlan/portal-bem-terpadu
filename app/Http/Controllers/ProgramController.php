<?php

namespace App\Http\Controllers;

use App\Models\WorkProgram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class ProgramController extends Controller
{
    /**
     * Dapatkan daftar program kerja demonstrasi.
     *
     * @return array<int, array<string, mixed>>
     */
    private function getPrograms(): array
    {
        return [
            [
                'slug' => 'advokasi-transparansi-biaya-akademik',
                'name' => 'Advokasi Transparansi & Bantuan Finansial',
                'organization' => 'Kementerian Sosial & Advokasi BEM',
                'status' => 'Berlangsung',
                'period' => 'Semester Ganjil 2026/2027',
                'department' => 'Sosial & Advokasi',
                'description' => 'Pendampingan langsung bagi mahasiswa yang membutuhkan keringanan UKT, beasiswa kemitraan, serta pengawalan transparansi penggunaan dana kemahasiswaan.',
                'objectives' => 'Mencegah angka putus kuliah akibat masalah ekonomi serta memastikan hak pendidikan setiap mahasiswa terlindungi dengan mekanisme pendampingan yang akuntabel.',
                'targets' => [
                    'Terselenggaranya posko pendampingan berkas UKT di setiap awal semester.',
                    'Penyusunan rekomendasi faktual kondisi ekonomi mahasiswa pemohon ke biro rektorat.',
                    'Penyebaran informasi beasiswa kemitraan swasta maupun pemerintah secara terverifikasi.',
                ],
                'indicators' => 'Minimal 80% mahasiswa pemohon tertangani berkasnya dan memperoleh kepastian klarifikasi sebelum tenggat pembayaran.',
                'timeline' => 'Agustus 2026 - Januari 2027',
                'pic' => 'Biro Bantuan Hukum & Advokasi Mahasiswa',
            ],
            [
                'slug' => 'inkubator-kreativitas-riset-mahasiswa',
                'name' => 'Inkubator Kreativitas & Riset Ilmiah Mahasiswa',
                'organization' => 'Kementerian Riset & Keilmuan BEM',
                'status' => 'Rencana',
                'period' => 'Semester Genap 2026/2027',
                'department' => 'Riset & Keilmuan',
                'description' => 'Program pembinaan, mentoring penulisan proposal Program Kreativitas Mahasiswa (PKM), fasilitasi seed funding, dan workshop sitasi ilmiah bereputasi.',
                'objectives' => 'Menumbuhkan budaya riset yang aplikatif, meningkatkan rasio partisipasi proposal ilmiah kampus di tingkat kompetisi nasional, dan mendorong publikasi mahasiswa.',
                'targets' => [
                    'Workshop penulisan gagasan kreatif dan metodologi ilmiah bertaraf nasional.',
                    'Klinik bedah proposal lintas disiplin ilmu dengan dosen pembimbing berpengalaman.',
                    'Simulasi presentasi dan penilaian setara standar juri nasional.',
                ],
                'indicators' => 'Terhimpunnya minimal 40 draf proposal siap unggah pada sistem seleksi perguruan tinggi.',
                'timeline' => 'Februari 2027 - Mei 2027',
                'pic' => 'Divisi Pengembangan Talenta Riset',
            ],
            [
                'slug' => 'bem-mengabdi-pelosok-daerah',
                'name' => 'BEM Mengabdi: Desa Binaan Berkelanjutan',
                'organization' => 'Kementerian Pengabdian Masyarakat BEM',
                'status' => 'Berlangsung',
                'period' => 'Oktober - Desember 2026',
                'department' => 'Pengabdian Masyarakat',
                'description' => 'Aksi pengabdian masyarakat terintegrasi yang menyasar peningkatan literasi baca anak, pendampingan pemasaran digital bagi kelompok tani lokal, dan sanitasi lingkungan.',
                'objectives' => 'Mewujudkan peran aktif mahasiswa sebagai agen perubahan (agent of change) di tengah masyarakat desa secara konkret dan berkelanjutan.',
                'targets' => [
                    'Pembangunan instalasi pojok baca ramah anak di balai desa mitra.',
                    'Pelatihan foto produk dan pembukaan toko daring bagi 25 pelaku UMKM desa.',
                    'Pemeriksaan kesehatan dasar gratis bekerja sama dengan relawan fakultas kesehatan.',
                ],
                'indicators' => 'Terlaksananya 4 siklus kunjungan lapangan berkala dan berdirinya pojok baca yang mandiri.',
                'timeline' => '10 Oktober 2026 - 20 Desember 2026',
                'pic' => 'Koordinator Relawan Pengabdian BEM',
            ],
            [
                'slug' => 'digitalisasi-layanan-ormawa-kampus',
                'name' => 'Digitalisasi Layanan Ormawa & Pengarsipan Terpadu',
                'organization' => 'Kementerian Komunikasi & Informasi BEM',
                'status' => 'Berlangsung',
                'period' => 'Tahun Akademik 2026/2027',
                'department' => 'Komunikasi & Informasi',
                'description' => 'Pengembangan portal terpusat untuk peminjaman ruangan, perizinan acara kemahasiswaan terpadu, dan publikasi agenda resmi seluruh UKM dan HIMA.',
                'objectives' => 'Mengurangi birokrasi manual yang berbelit, mempercepat persetujuan proposal kegiatan, dan mencegah benturan jadwal antar-ormawa.',
                'targets' => [
                    'Portal BEM Terpadu dengan modul publikasi dan kanal aspirasi aktif.',
                    'Standarisasi kalender bersama bagi seluruh ormawa kampus.',
                    'Panduan digital pedoman administrasi persuratan mahasiswa.',
                ],
                'indicators' => 'Seluruh perizinan terarsip digital dengan waktu validasi rata-rata maksimal 3 hari kerja.',
                'timeline' => 'September 2026 - Juli 2027',
                'pic' => 'Tim Pengembang Sistem Informasi BEM',
            ],
            [
                'slug' => 'turnamen-olahraga-dan-seni-kampus-2026',
                'name' => 'Pekan Olahraga & Seni Mahasiswa Terpadu',
                'organization' => 'Kementerian Minat & Bakat BEM',
                'status' => 'Selesai',
                'period' => 'Semester Genap 2025/2026',
                'department' => 'Minat & Bakat',
                'description' => 'Kompetisi persahabatan antarfakultas di bidang futsal, basket, bulu tangkis, fotografi, debat bahasa, dan vokal grup guna mempererat solidaritas mahasiswa.',
                'objectives' => 'Menjaring bibit atlet dan seniman muda mahasiswa untuk pembinaan delegasi kejuaraan tingkat wilayah maupun nasional.',
                'targets' => [
                    'Pertandingan 6 cabang olahraga dan 4 cabang seni budaya.',
                    'Penyelenggaraan pameran karya seni rupa mahasiswa selama 1 pekan penuh.',
                    'Malam penganugerahan insan berprestasi kampus.',
                ],
                'indicators' => 'Partisipasi seluruh delegasi fakultas tanpa catatan pelanggaran sportivitas.',
                'timeline' => 'Mei 2026 - Juni 2026',
                'pic' => 'Badan Pembina Olahraga & Seni Kampus',
            ],
            [
                'slug' => 'sekolah-kepemimpinan-kebangsaan-mahasiswa',
                'name' => 'Sekolah Kepemimpinan & Etika Publik Mahasiswa',
                'organization' => 'Kementerian Pengembangan Sumber Daya Mahasiswa',
                'status' => 'Rencana',
                'period' => 'Januari - Maret 2027',
                'department' => 'PSDM',
                'description' => 'Jenjang kaderisasi kepemimpinan tingkat menengah yang mendalami pemikiran kebangsaan, retorika diplomasi, tata kelola sidang, dan etika kepemimpinan publik.',
                'objectives' => 'Mempersiapkan regenerasi pengurus ormawa yang berintegritas, visioner, serta memiliki daya analisis sosial yang kuat.',
                'targets' => [
                    'Penyaringan 60 calon kader terbaik melalui uji esai dan wawancara komitmen.',
                    'Sesi kuliah tamu dari tokoh pemikir, aktivis sosial, dan alumni berprestasi.',
                    'Praktek simulasi penyusunan kebijakan publik kemahasiswaan.',
                ],
                'indicators' => 'Tingkat kelulusan peserta minimal 90% dengan menghasilkan dokumen karya akhir resolusi kampus.',
                'timeline' => '15 Januari 2027 - 10 Maret 2027',
                'pic' => 'Divisi Kaderisasi PSDM BEM',
            ],
        ];
    }

    /**
     * Tampilkan daftar seluruh program kerja.
     */
    public function index(Request $request): View
    {
        $hasDb = false;
        try {
            $hasDb = Schema::hasTable('work_programs');
        } catch (\Throwable $e) {
            $hasDb = false;
        }

        $departments = [
            'Semua',
            'Sosial & Advokasi',
            'Riset & Keilmuan',
            'Pengabdian Masyarakat',
            'Komunikasi & Informasi',
            'Minat & Bakat',
            'PSDM',
        ];
        $statuses = ['Semua', 'Berlangsung', 'Rencana', 'Selesai'];

        $currentDept = $request->query('departemen', 'Semua');
        $currentStatus = $request->query('status', 'Semua');

        if ($hasDb) {
            $query = WorkProgram::published()->latest('created_at');

            if ($currentDept !== 'Semua') {
                $query->where('department', $currentDept);
            }

            if ($currentStatus !== 'Semua') {
                $query->where('status', $currentStatus);
            }

            // Paginasi 9 entri per halaman; withQueryString() mempertahankan filter
            // bidang dan status saat berpindah halaman.
            $programs = $query->paginate(9)->withQueryString();

            return view('pages.programs.index', [
                'programs' => $programs,
                'departments' => $departments,
                'statuses' => $statuses,
                'currentDept' => $currentDept,
                'currentStatus' => $currentStatus,
                'totalCount' => $programs->total(),
            ]);
        }

        $allPrograms = $this->getPrograms();

        $filtered = array_filter($allPrograms, function (array $item) use ($currentDept, $currentStatus): bool {
            $matchDept = ($currentDept === 'Semua' || $item['department'] === $currentDept);
            $matchStatus = ($currentStatus === 'Semua' || $item['status'] === $currentStatus);

            return $matchDept && $matchStatus;
        });

        return view('pages.programs.index', [
            'programs' => array_values($filtered),
            'departments' => $departments,
            'statuses' => $statuses,
            'currentDept' => $currentDept,
            'currentStatus' => $currentStatus,
            'totalCount' => count($filtered),
        ]);
    }

    /**
     * Tampilkan detail program kerja.
     */
    public function show(string $slug): View
    {
        $hasDb = false;
        try {
            $hasDb = Schema::hasTable('work_programs');
        } catch (\Throwable $e) {
            $hasDb = false;
        }

        if ($hasDb) {
            $program = WorkProgram::published()->where('slug', $slug)->first();

            if (! $program) {
                abort(404, 'Program kerja tidak ditemukan.');
            }

            $otherPrograms = WorkProgram::published()
                ->where('slug', '!=', $program->slug)
                ->take(3)
                ->get();

            return view('pages.programs.show', [
                'program' => $program,
                'otherPrograms' => $otherPrograms,
            ]);
        }

        $allPrograms = $this->getPrograms();
        $program = null;

        foreach ($allPrograms as $item) {
            if ($item['slug'] === $slug) {
                $program = $item;
                break;
            }
        }

        if (! $program) {
            abort(404, 'Program kerja tidak ditemukan.');
        }

        $otherPrograms = array_filter($allPrograms, function (array $item) use ($program): bool {
            return $item['slug'] !== $program['slug'];
        });

        return view('pages.programs.show', [
            'program' => $program,
            'otherPrograms' => array_slice(array_values($otherPrograms), 0, 3),
        ]);
    }
}
