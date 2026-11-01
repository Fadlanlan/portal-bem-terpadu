<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class OrganizationController extends Controller
{
    /**
     * Dapatkan daftar lembaga kemahasiswaan demonstrasi.
     *
     * @return array<int, array<string, mixed>>
     */
    private function getOrganizations(): array
    {
        return [
            [
                'code' => 'BEM',
                'name' => 'Badan Eksekutif Mahasiswa (BEM)',
                'level' => 'Tingkat Universitas / Perguruan Tinggi',
                'role_type' => 'Lembaga Eksekutif Tertinggi',
                'description' => 'Lembaga eksekutif mahasiswa yang bertugas mengoordinasikan, merancang, dan mengeksekusi program kerja kemahasiswaan lintas fakultas, mengawal advokasi hak mahasiswa ke pimpinan perguruan tinggi, serta menjalin kemitraan strategis eksternal.',
                'functions' => [
                    'Mengimplementasikan Garis-Garis Besar Haluan Program Kerja hasil musyawarah perwakilan mahasiswa.',
                    'Melakukan advokasi kebijakan finansial, sarana prasarana, dan hak kebebasan akademik mahasiswa.',
                    'Mengoordinasikan agenda berskala universitas bersama HIMA, HMPS, dan UKM.',
                    'Mewakili suara resmi mahasiswa dalam forum kemitraan perguruan tinggi regional dan nasional.',
                ],
                'structure_overview' => [
                    'Presiden & Wakil Presiden Mahasiswa',
                    'Sekretariat Jenderal & Bendahara Umum',
                    'Kementerian Dalam Negeri (Koordinasi Ormawa)',
                    'Kementerian Luar Negeri & Hubungan Antar Lembaga',
                    'Kementerian Sosial, Advokasi, & Kesejahteraan Mahasiswa',
                    'Kementerian Riset, Teknologi, & Pengembangan Keilmuan',
                    'Kementerian Pengabdian Masyarakat & Lingkungan Hidup',
                    'Kementerian Minat, Bakat, & Olahraga',
                    'Biro Komunikasi, Informasi, & Media Kreatif',
                ],
            ],
            [
                'code' => 'MAM',
                'name' => 'Majelis Aspirasi Mahasiswa (MAM)',
                'level' => 'Tingkat Universitas / Perguruan Tinggi',
                'role_type' => 'Lembaga Permusyawaratan & Kedaulatan',
                'description' => 'Lembaga permusyawaratan tertinggi mahasiswa yang memegang kedaulatan mahasiswa melalui sidang pleno, musyawarah besar, penyerapan aspirasi murni dari seluruh lini kampus, serta penetapan ketetapan pokok organisasi.',
                'functions' => [
                    'Menyelenggarakan Musyawarah Besar Mahasiswa dan Sidang Istimewa.',
                    'Menyerap, mengolah, dan merumuskan pokok-pokok aspirasi mahasiswa menjadi rekomendasi kebijakan.',
                    'Menetapkan anggaran dasar dan pedoman pokok keorganisasian mahasiswa.',
                    'Melantik dan mengambil sumpah amanah jajaran pengurus BEM terpilih.',
                ],
                'structure_overview' => [
                    'Presidium Sidang Utama',
                    'Komisi Penyerapan Aspirasi & Hubungan Massa',
                    'Komisi Konstitusi & Tata Tertib Organisasi',
                    'Badan Pengawas Kebijakan Kemahasiswaan',
                ],
            ],
            [
                'code' => 'DPM',
                'name' => 'Dewan Perwakilan Mahasiswa (DPM)',
                'level' => 'Tingkat Universitas / Perguruan Tinggi',
                'role_type' => 'Lembaga Legislatif & Pengawasan',
                'description' => 'Lembaga legislatif yang menjalankan fungsi perancangan regulasi kemahasiswaan (undang-undang ormawa), pengawasan terhadap kinerja anggaran dan program BEM, serta menyalurkan aspirasi konstituen fakultas.',
                'functions' => [
                    'Menyusun dan mengesahkan regulasi kemahasiswaan yang adil dan transparan.',
                    'Mengawasi implementasi program kerja serta serapan anggaran kemahasiswaan BEM.',
                    'Mengadakan rapat dengar pendapat (RDP) dan evaluasi berkala kinerja menteri.',
                    'Menyelenggarakan forum jajak pendapat berkala di tiap fakultas.',
                ],
                'structure_overview' => [
                    'Ketua & Wakil Ketua DPM',
                    'Komisi I: Legislasi & Produk Hukum Mahasiswa',
                    'Komisi II: Anggaran & Pengawasan Keuangan',
                    'Komisi III: Pengawasan Bidang Internal & Kesejahteraan',
                    'Badan Kehormatan Dewan',
                ],
            ],
            [
                'code' => 'HIMA',
                'name' => 'Himpunan Mahasiswa Fakultas (HIMA)',
                'level' => 'Tingkat Fakultas',
                'role_type' => 'Koordinator Kemahasiswaan Fakultas',
                'description' => 'Organisasi kemahasiswaan tingkat fakultas yang mewadahi aspirasi, koordinasi kegiatan keilmuan rumpun fakultas, serta menjembatani komunikasi mahasiswa dengan dekanat.',
                'functions' => [
                    'Mengoordinasikan kegiatan akademik dan non-akademik di tingkat fakultas.',
                    'Mengawal advokasi fasilitas laboratorium, ruang belajar, dan layanan dekanat.',
                    'Membina solidaritas antarmahasiswa program studi dalam satu fakultas.',
                ],
                'structure_overview' => [
                    'Ketua Himpunan Fakultas',
                    'Divisi Pengembangan Akademik & Profesi',
                    'Divisi Advokasi Kesejahteraan Mahasiswa Fakultas',
                    'Divisi Minat, Olahraga, & Seni Fakultas',
                ],
            ],
            [
                'code' => 'HMPS',
                'name' => 'Himpunan Mahasiswa Program Studi (HMPS)',
                'level' => 'Tingkat Program Studi / Jurusan',
                'role_type' => 'Pengembangan Profesi & Spesialisasi Keilmuan',
                'description' => 'Organisasi garis depan di tingkat program studi yang berfokus langsung pada pembinaan kepakaran keilmuan, keprofesian spesifik, pendampingan studi antarmahasiswa, dan pengenalan dunia kerja industri terkait.',
                'functions' => [
                    'Menyelenggarakan seminar kompetensi spesifik prodi, workshop sertifikasi, dan study club.',
                    'Mendampingi mahasiswa baru dalam proses adaptasi kurikulum jurusan.',
                    'Membangun jejaring kolaborasi alumni prodi dan mitra industri.',
                ],
                'structure_overview' => [
                    'BPH Himpunan Program Studi',
                    'Bidang Keilmuan & Riset Prodi',
                    'Bidang Hubungan Alumni & Dunia Kerja',
                    'Bidang Pengembangan Karakter Mahasiswa',
                ],
            ],
            [
                'code' => 'UKM',
                'name' => 'Unit Kegiatan Mahasiswa (UKM)',
                'level' => 'Tingkat Universitas & Terbuka Lintas Fakultas',
                'role_type' => 'Pusat Minat, Bakat, Seni, & Kepeloporan',
                'description' => 'Wadah pembinaan dan aktualisasi diri mahasiswa pada bidang spesifik di luar kurikulum akademik, mulai dari cabang keolahragaan, seni budaya, riset penalaran, hingga pengabdian kemanusiaan dan keagamaan.',
                'functions' => [
                    'Melatih dan mempersiapkan atlet serta delegasi seniman mahasiswa dalam kompetisi.',
                    'Mengembangkan kepedulian sosial melalui korps sukarela dan aksi kemanusiaan.',
                    'Menyediakan ruang ekspresi hobi yang sehat, positif, dan membanggakan almamater.',
                ],
                'structure_overview' => [
                    'Rumpun UKM Olahraga (Futsal, Basket, Badminton, Bela Diri)',
                    'Rumpun UKM Seni & Budaya (Tari, Musik, Teater, Paduan Suara, Fotografi)',
                    'Rumpun UKM Penalaran & Penelitian (Debat Ilmiah, Robotika, Sains)',
                    'Rumpun UKM Kepedulian Khusus (KSR-PMI, Pramuka Racana, Mapala)',
                    'Rumpun UKM Kerohanian & Spiritual Kampus',
                ],
            ],
        ];
    }

    /**
     * Tampilkan halaman struktur organisasi kemahasiswaan.
     */
    public function index(): View
    {
        $hasDb = false;
        try {
            $hasDb = Schema::hasTable('organizations');
        } catch (\Throwable $e) {
            $hasDb = false;
        }

        if ($hasDb) {
            $organizations = Organization::published()->get();

            return view('pages.organizations.index', [
                'organizations' => $organizations,
            ]);
        }

        return view('pages.organizations.index', [
            'organizations' => $this->getOrganizations(),
        ]);
    }
}
