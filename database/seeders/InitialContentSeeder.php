<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Event;
use App\Models\Organization;
use App\Models\WorkProgram;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class InitialContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Articles / Berita
        $articles = [
            [
                'slug' => 'penerimaan-aspirasi-terpadu-periode-2026',
                'title' => 'Kanal Digital Aspirasi Mahasiswa Resmi Disosialisasikan',
                'excerpt' => 'BEM meluncurkan inisiatif kanal terpadu untuk memastikan setiap suara, kritik konstruktif, dan saran mahasiswa terkelola secara transparan dan akuntabel.',
                'content' => "Dalam upaya memperkuat budaya demokrasi dan keterbukaan di lingkungan kampus, Badan Eksekutif Mahasiswa (BEM) secara resmi menyosialisasikan kerangka kerja sistem kanal aspirasi digital terpadu.\n\nSistem ini dirancang untuk menjawab tantangan komunikasi antara mahasiswa, organisasi kemahasiswaan, dan pimpinan perguruan tinggi. Melalui sistem tiket terlacak, setiap laporan, pertanyaan kebijakan akademik, maupun usulan fasilitas akan mendapatkan alur verifikasi resmi tanpa kekhawatiran atas kebocoran identitas bagi mahasiswa yang memilih opsi laporan terjaga.\n\n\"Kanal aspirasi bukan sekadar kotak saran digital, melainkan komitmen institusional agar setiap masukan didengar dan ditindaklanjuti secara terukur,\" ungkap narahubung divisi advokasi dalam forum sosialisasi.",
                'category' => 'Advokasi & Aspirasi',
                'author_name' => 'Biro Komunikasi & Informasi',
                'status' => 'published',
                'published_at' => Carbon::parse('2026-10-24 09:00:00'),
                'read_time' => '3 menit',
            ],
            [
                'slug' => 'pembukaan-pendaftaran-delegasi-studi-banding',
                'title' => 'Pendaftaran Delegasi Forum Mahasiswa Nasional Telah Dibuka',
                'excerpt' => 'Kesempatan bagi perwakilan organisasi kemahasiswaan untuk berpartisipasi dalam pertukaran gagasan kepemimpinan di tingkat perguruan tinggi se-Indonesia.',
                'content' => "Kementerian Luar Negeri BEM membuka pendaftaran seleksi delegasi mahasiswa untuk menghadiri Temu Wicara Nasional dan Konferensi Tahunan Mahasiswa Nusantara yang akan dihelat bulan depan.\n\nProgram ini bertujuan memperluas wawasan kebangsaan, jejaring kolaborasi antarkampus, serta merumuskan resolusi bersama mengenai masa depan pendidikan tinggi berbasis teknologi berkelanjutan.\n\nPersyaratan meliputi komitmen keaktifan organisasi, kepemilikan gagasan inovasi sosial, dan kesiapan memaparkan kajian komparatif dalam forum panel diskusi.",
                'category' => 'Pengumuman',
                'author_name' => 'Kementerian Luar Negeri BEM',
                'status' => 'published',
                'published_at' => Carbon::parse('2026-10-22 10:30:00'),
                'read_time' => '4 menit',
            ],
            [
                'slug' => 'lokakarya-manajemen-keorganisasian-2026',
                'title' => 'Lokakarya Tata Kelola Organisasi Mahasiswa Berbasis Digital',
                'excerpt' => 'Pelatihan intensif bagi jajaran pengurus UKM, HIMA, dan HMPS guna memperkuat transparansi administrasi serta sinergi kegiatan antarkampus.',
                'content' => "Tata kelola organisasi yang akuntabel merupakan fondasi penting bagi kematangan lembaga mahasiswa. Untuk merealisasikannya, BEM menginisiasi Lokakarya Tata Kelola Organisasi Mahasiswa 2026.\n\nAgenda ini berfokus pada standardisasi penyusunan laporan pertanggungjawaban (LPJ), pengelolaan arsip digital, manajemen risiko kegiatan lapangan, serta etika publikasi informasi organisasi di ruang siber.\n\nSeluruh perwakilan HIMA, HMPS, dan UKM didorong mengirimkan penanggung jawab administrasi dan keuangan guna memastikan keselarasan regulasi internal kampus.",
                'category' => 'Pelatihan',
                'author_name' => 'Kementerian Dalam Negeri BEM',
                'status' => 'published',
                'published_at' => Carbon::parse('2026-10-19 14:00:00'),
                'read_time' => '5 menit',
            ],
            [
                'slug' => 'pembentukan-posko-keringanan-ukt-semester-ganjil',
                'title' => 'Posko Advokasi Keringanan Biaya Pendidikan Resmi Beroperasi',
                'excerpt' => 'BEM membuka layanan pendampingan pengajuan penyesuaian UKT, pencarian sponsor beasiswa, dan verifikasi berkas bagi mahasiswa terdampak kendala finansial.',
                'content' => "Menanggapi dinamika ekonomi dan kebutuhan penyesuaian biaya kuliah, BEM membentuk Posko Bersama Advokasi Finansial Mahasiswa.\n\nPosko ini bertugas memverifikasi kelengkapan berkas pemohon, menyusun rekomendasi faktual ke pihak biro keuangan rektorat, dan memastikan tidak ada mahasiswa yang putus kuliah karena kendala finansial yang belum tertangani.\n\nPelayanan dilaksanakan secara luring di sekretariat BEM setiap hari kerja pukul 09.00 - 15.00 WIB serta pendampingan berkas daring.",
                'category' => 'Advokasi & Aspirasi',
                'author_name' => 'Kementerian Sosial & Advokasi BEM',
                'status' => 'published',
                'published_at' => Carbon::parse('2026-10-15 08:30:00'),
                'read_time' => '4 menit',
            ],
            [
                'slug' => 'penutupan-bulan-bakti-sosial-kampus',
                'title' => 'Hasil Bulan Bakti Mahasiswa: Revitalisasi 3 Titik Ruang Belajar Komunitas',
                'excerpt' => 'Rangkaian kegiatan bakti sosial terpadu berhasil diselesaikan dengan keterlibatan lebih dari 150 relawan mahasiswa lintas fakultas.',
                'content' => "Program tahunan Bulan Bakti Sosial Mahasiswa ditutup dengan penyerahan fasilitas ruang belajar ramah anak di wilayah pinggiran kota.\n\nSelama 4 pekan pelaksanaan, mahasiswa lintas program studi berkontribusi dalam perbaikan sarana fisik, penyediaan pojok baca digital, dan pelatihan sains interaktif untuk siswa sekolah dasar sekitar.\n\nKeberhasilan ini membuktikan sinergi nyata kepedulian sivitas akademika dalam mewujudkan tridarma perguruan tinggi, khususnya aspek pengabdian masyarakat.",
                'category' => 'Kegiatan',
                'author_name' => 'Kementerian Pengabdian Masyarakat BEM',
                'status' => 'published',
                'published_at' => Carbon::parse('2026-10-11 11:00:00'),
                'read_time' => '3 menit',
            ],
            [
                'slug' => 'konsolidasi-akbar-evaluasi-kebijakan-akademik',
                'title' => 'Konsolidasi Terbuka Pembahasan Kurikulum Merdeka & Fasilitas Riset',
                'excerpt' => 'Forum diskusi perwakilan kelas membahas efektivitas implementasi kurikulum, akses laboratorium, dan ketersediaan jurnal referensi ilmiah.',
                'content' => "Menjelang evaluasi tengah semester, perwakilan mahasiswa dari berbagai himpunan prodi berkumpul dalam forum konsolidasi terbuka.\n\nDiskusi menyoroti ketersediaan sarana riset mandiri, akses langganan pangkalan data jurnal bereputasi, serta mekanisme konversi kredit kegiatan kemahasiswaan ke dalam portofolio akademik.\n\nSeluruh poin catatan kritis dihimpun dalam naskah memorandum resmi yang akan diserahkan pada audiensi pimpinan dekanat mendatang.",
                'category' => 'Akademik',
                'author_name' => 'Kementerian Riset & Keilmuan BEM',
                'status' => 'published',
                'published_at' => Carbon::parse('2026-10-05 13:00:00'),
                'read_time' => '5 menit',
            ],
        ];

        foreach ($articles as $art) {
            Article::updateOrCreate(['slug' => $art['slug']], $art);
        }

        // 2. Work Programs / Program Kerja
        $programs = [
            [
                'slug' => 'advokasi-transparansi-biaya-akademik',
                'name' => 'Advokasi Transparansi & Bantuan Finansial',
                'organization' => 'Kementerian Sosial & Advokasi BEM',
                'department' => 'Sosial & Advokasi',
                'status' => 'Berlangsung',
                'publication_status' => 'published',
                'period' => 'Semester Ganjil 2026/2027',
                'description' => 'Pendampingan langsung bagi mahasiswa yang membutuhkan keringanan UKT, beasiswa kemitraan, serta pengawalan transparansi penggunaan dana kemahasiswaan.',
                'objectives' => 'Mencegah angka putus kuliah akibat masalah ekonomi serta memastikan hak pendidikan setiap mahasiswa terlindungi dengan mekanisme pendampingan yang akuntabel.',
                'targets' => json_encode([
                    'Terselenggaranya posko pendampingan berkas UKT di setiap awal semester.',
                    'Penyusunan rekomendasi faktual kondisi ekonomi mahasiswa pemohon ke biro rektorat.',
                    'Penyebaran informasi beasiswa kemitraan swasta maupun pemerintah secara terverifikasi.',
                ]),
                'indicators' => 'Minimal 80% mahasiswa pemohon tertangani berkasnya dan memperoleh kepastian klarifikasi sebelum tenggat pembayaran.',
                'timeline' => 'Agustus 2026 - Januari 2027',
                'pic' => 'Biro Bantuan Hukum & Advokasi Mahasiswa',
                'published_at' => now(),
            ],
            [
                'slug' => 'inkubator-kreativitas-riset-mahasiswa',
                'name' => 'Inkubator Kreativitas & Riset Ilmiah Mahasiswa',
                'organization' => 'Kementerian Riset & Keilmuan BEM',
                'department' => 'Riset & Keilmuan',
                'status' => 'Rencana',
                'publication_status' => 'published',
                'period' => 'Semester Genap 2026/2027',
                'description' => 'Program pembinaan, mentoring penulisan proposal Program Kreativitas Mahasiswa (PKM), fasilitasi seed funding, dan workshop sitasi ilmiah bereputasi.',
                'objectives' => 'Menumbuhkan budaya riset yang aplikatif, meningkatkan rasio partisipasi proposal ilmiah kampus di tingkat kompetisi nasional, dan mendorong publikasi mahasiswa.',
                'targets' => json_encode([
                    'Workshop penulisan gagasan kreatif dan metodologi ilmiah bertaraf nasional.',
                    'Klinik bedah proposal lintas disiplin ilmu dengan dosen pembimbing berpengalaman.',
                    'Simulasi presentasi dan penilaian setara standar juri nasional.',
                ]),
                'indicators' => 'Terhimpunnya minimal 40 draf proposal siap unggah pada sistem seleksi perguruan tinggi.',
                'timeline' => 'Februari 2027 - Mei 2027',
                'pic' => 'Divisi Pengembangan Talenta Riset',
                'published_at' => now(),
            ],
            [
                'slug' => 'bem-mengabdi-pelosok-daerah',
                'name' => 'BEM Mengabdi: Desa Binaan Berkelanjutan',
                'organization' => 'Kementerian Pengabdian Masyarakat BEM',
                'department' => 'Pengabdian Masyarakat',
                'status' => 'Berlangsung',
                'publication_status' => 'published',
                'period' => 'Oktober - Desember 2026',
                'description' => 'Aksi pengabdian masyarakat terintegrasi yang menyasar peningkatan literasi baca anak, pendampingan pemasaran digital bagi kelompok tani lokal, dan sanitasi lingkungan.',
                'objectives' => 'Mewujudkan peran aktif mahasiswa sebagai agen perubahan (agent of change) di tengah masyarakat desa secara konkret dan berkelanjutan.',
                'targets' => json_encode([
                    'Pembangunan instalasi pojok baca ramah anak di balai desa mitra.',
                    'Pelatihan foto produk dan pembukaan toko daring bagi 25 pelaku UMKM desa.',
                    'Pemeriksaan kesehatan dasar gratis bekerja sama dengan relawan fakultas kesehatan.',
                ]),
                'indicators' => 'Terlaksananya 4 siklus kunjungan lapangan berkala dan berdirinya pojok baca yang mandiri.',
                'timeline' => '10 Oktober 2026 - 20 Desember 2026',
                'pic' => 'Koordinator Relawan Pengabdian BEM',
                'published_at' => now(),
            ],
            [
                'slug' => 'digitalisasi-layanan-ormawa-kampus',
                'name' => 'Digitalisasi Layanan Ormawa & Pengarsipan Terpadu',
                'organization' => 'Kementerian Komunikasi & Informasi BEM',
                'department' => 'Komunikasi & Informasi',
                'status' => 'Berlangsung',
                'publication_status' => 'published',
                'period' => 'Tahun Akademik 2026/2027',
                'description' => 'Pengembangan portal terpusat untuk peminjaman ruangan, perizinan acara kemahasiswaan terpadu, dan publikasi agenda resmi seluruh UKM dan HIMA.',
                'objectives' => 'Mengurangi birokrasi manual yang berbelit, mempercepat persetujuan proposal kegiatan, dan mencegah benturan jadwal antar-ormawa.',
                'targets' => json_encode([
                    'Portal BEM Terpadu dengan modul publikasi dan kanal aspirasi aktif.',
                    'Standarisasi kalender bersama bagi seluruh ormawa kampus.',
                    'Panduan digital pedoman administrasi persuratan mahasiswa.',
                ]),
                'indicators' => 'Seluruh perizinan terarsip digital dengan waktu validasi rata-rata maksimal 3 hari kerja.',
                'timeline' => 'September 2026 - Juli 2027',
                'pic' => 'Tim Pengembang Sistem Informasi BEM',
                'published_at' => now(),
            ],
            [
                'slug' => 'turnamen-olahraga-dan-seni-kampus-2026',
                'name' => 'Pekan Olahraga & Seni Mahasiswa Terpadu',
                'organization' => 'Kementerian Minat & Bakat BEM',
                'department' => 'Minat & Bakat',
                'status' => 'Selesai',
                'publication_status' => 'published',
                'period' => 'Semester Genap 2025/2026',
                'description' => 'Kompetisi persahabatan antarfakultas di bidang futsal, basket, bulu tangkis, fotografi, debat bahasa, dan vokal grup guna mempererat solidaritas mahasiswa.',
                'objectives' => 'Menjaring bibit atlet dan seniman muda mahasiswa untuk pembinaan delegasi kejuaraan tingkat wilayah maupun nasional.',
                'targets' => json_encode([
                    'Pertandingan 6 cabang olahraga dan 4 cabang seni budaya.',
                    'Penyelenggaraan pameran karya seni rupa mahasiswa selama 1 pekan penuh.',
                    'Malam penganugerahan insan berprestasi kampus.',
                ]),
                'indicators' => 'Partisipasi seluruh delegasi fakultas tanpa catatan pelanggaran sportivitas.',
                'timeline' => 'Mei 2026 - Juni 2026',
                'pic' => 'Badan Pembina Olahraga & Seni Kampus',
                'published_at' => now(),
            ],
            [
                'slug' => 'sekolah-kepemimpinan-kebangsaan-mahasiswa',
                'name' => 'Sekolah Kepemimpinan & Etika Publik Mahasiswa',
                'organization' => 'Kementerian Pengembangan Sumber Daya Mahasiswa',
                'department' => 'PSDM',
                'status' => 'Rencana',
                'publication_status' => 'published',
                'period' => 'Januari - Maret 2027',
                'description' => 'Jenjang kaderisasi kepemimpinan tingkat menengah yang mendalami pemikiran kebangsaan, retorika diplomasi, tata kelola sidang, dan etika kepemimpinan publik.',
                'objectives' => 'Mempersiapkan regenerasi pengurus ormawa yang berintegritas, visioner, serta memiliki daya analisis sosial yang kuat.',
                'targets' => json_encode([
                    'Penyaringan 60 calon kader terbaik melalui uji esai dan wawancara komitmen.',
                    'Sesi kuliah tamu dari tokoh pemikir, aktivis sosial, dan alumni berprestasi.',
                    'Praktek simulasi penyusunan kebijakan publik kemahasiswaan.',
                ]),
                'indicators' => 'Terselenggaranya 8 modul pelatihan intensif dengan tingkat kelulusan minimal 90%.',
                'timeline' => 'Januari 2027 - Maret 2027',
                'pic' => 'Divisi Kaderisasi & Kepemimpinan BEM',
                'published_at' => now(),
            ],
        ];

        foreach ($programs as $prog) {
            WorkProgram::updateOrCreate(['slug' => $prog['slug']], $prog);
        }

        // 3. Events / Agenda
        $events = [
            [
                'slug' => 'dialog-terbuka-rektorat-mahasiswa-2026',
                'title' => 'Dialog Terbuka: Refleksi Kebijakan Kampus & Mahasiswa',
                'category' => 'Audiensi & Kebijakan',
                'summary' => 'Forum dialog tahunan antara perwakilan sivitas akademika mahasiswa dan jajaran pimpinan rektorat membahas sarana prasarana dan pendanaan kegiatan.',
                'description' => "Forum dialog terbuka tahunan antara perwakilan sivitas akademika mahasiswa dan jajaran pimpinan rektorat. Forum ini mengulas capaian fasilitas kampus, arah pengembangan kurikulum, serta kebijakan pendanaan kegiatan mahasiswa.\n\nPeserta terbuka bagi seluruh mahasiswa aktif yang telah melakukan konfirmasi kehadiran melalui tautan yang disediakan.",
                'location' => 'Auditorium Gedung Utama Lt. 3',
                'started_at' => Carbon::parse('2026-11-07 09:00:00'),
                'ended_at' => Carbon::parse('2026-11-07 12:30:00'),
                'organizer' => 'BEM & Dewan Perwakilan Mahasiswa',
                'registration_url' => 'https://kampus.ac.id/dialog-terbuka',
                'is_registration_open' => true,
                'speakers' => json_encode([
                    'Jajaran Wakil Rektor Bidang Akademik & Kemahasiswaan',
                    'Ketua BEM Terpilih Periode 2026/2027',
                    'Ketua Presidium MAM',
                ]),
                'agenda_points' => json_encode([
                    'Penyampaian naskah aspirasi hasil konsolidasi mahasiswa.',
                    'Tanggapan resmi jajaran rektorat terkait sarana prasarana.',
                    'Sesi tanya jawab terbuka delegasi fakultas.',
                    'Penandatanganan nota kesepahaman komitmen bersama.',
                ]),
                'status' => 'published',
            ],
            [
                'slug' => 'sidang-pleno-mahasiswa-semester-ganjil',
                'title' => 'Sidang Pleno Evaluasi Paruh Periode Organisasi',
                'category' => 'Permusyawaratan',
                'summary' => 'Sidang resmi permusyawaratan mahasiswa guna mendengarkan progres pelaksanaan program kerja BEM serta penetapan rekomendasi korektif.',
                'description' => "Sidang resmi permusyawaratan mahasiswa guna mendengarkan laporan progres paruh semester pelaksanaan program kerja BEM serta penetapan rekomendasi korektif legislatif.\n\nSidang dihadiri oleh utusan sah dari seluruh Himpunan Mahasiswa Jurusan/Prodi dan Unit Kegiatan Mahasiswa.",
                'location' => 'Ruang Sidang Paripurna MAM',
                'started_at' => Carbon::parse('2026-11-14 13:00:00'),
                'ended_at' => Carbon::parse('2026-11-14 17:00:00'),
                'organizer' => 'Majelis Aspirasi Mahasiswa (MAM)',
                'registration_url' => null,
                'is_registration_open' => false,
                'speakers' => json_encode([
                    'Presidium Sidang Majelis Aspirasi Mahasiswa',
                    'Badan Pengurus Harian BEM',
                ]),
                'agenda_points' => json_encode([
                    'Pembacaan tata tertib persidangan pleno.',
                    'Pemaparan capaian indikator kerja kementerian BEM.',
                    'Pandangan umum fraksi HIMA & UKM.',
                    'Pengesahan ketetapan sidang pleno tengah periode.',
                ]),
                'status' => 'published',
            ],
            [
                'slug' => 'festival-kebangsaan-dan-budaya-nusantara',
                'title' => 'Pekan Seni & Pameran Inovasi Ormawa 2026',
                'category' => 'Seni & Budaya',
                'summary' => 'Pesta kebudayaan dan unjuk karya inovasi terbesar di kampus menghadirkan pameran prototipe teknologi dan malam apresiasi.',
                'description' => "Pesta kebudayaan dan unjuk karya inovasi terbesar di lingkungan kampus. Menampilkan kolaborasi pertunjukan tari nusantara, teater kampus, stan pameran prototipe karya teknologi mahasiswa, dan festival kuliner daerah.\n\nAcara terbuka untuk seluruh keluarga besar kampus dan masyarakat umum pecinta seni budaya.",
                'location' => 'Plaza Kampus Terpadu & Lapangan Terbuka',
                'started_at' => Carbon::parse('2026-11-28 08:00:00'),
                'ended_at' => Carbon::parse('2026-11-28 21:00:00'),
                'organizer' => 'BEM & Seluruh Unit Kegiatan Mahasiswa',
                'registration_url' => 'https://kampus.ac.id/pekan-seni',
                'is_registration_open' => true,
                'speakers' => json_encode([
                    'Kurator Budaya & Pegiat Seni Kampus',
                    'Tokoh Muda Inspiratif Nasional',
                ]),
                'agenda_points' => json_encode([
                    'Pawai parade kostum ormawa nusantara.',
                    'Panggung ekspresi musik & sastra mahasiswa.',
                    'Eksibisi prototipe teknologi dan poster riset.',
                    'Malam apresiasi talenta muda berprestasi.',
                ]),
                'status' => 'published',
            ],
            [
                'slug' => 'kuliah-umum-kepemimpinan-integritas-bangsa',
                'title' => 'Kuliah Umum: Kepemimpinan Etis di Era Disrupsi Digital',
                'category' => 'Kuliah Umum & Webinar',
                'summary' => 'Webinar berskala nasional mengenai tantangan etika kecerdasan buatan, integritas kepemimpinan organisasi, dan tata kelola transparansi publik.',
                'description' => "Webinar berskala nasional mengenai tantangan etika kecerdasan buatan, integritas kepemimpinan organisasi, dan tata kelola transparansi publik di era digital. Acara ini terbuka bagi mahasiswa sarjana dan pascasarjana.",
                'location' => 'Daring via Zoom & Siaran Langsung YouTube BEM',
                'started_at' => Carbon::parse('2026-12-10 13:30:00'),
                'ended_at' => Carbon::parse('2026-12-10 16:00:00'),
                'organizer' => 'Kementerian PSDM & Kominfo BEM',
                'registration_url' => 'https://kampus.ac.id/kuliah-umum-etis',
                'is_registration_open' => true,
                'speakers' => json_encode([
                    'Pakar Tata Kelola Kebijakan Publik',
                    'Praktisi Transformasi Digital Nasional',
                ]),
                'agenda_points' => json_encode([
                    'Keynote speech tantangan kepemimpinan etis generasi muda.',
                    'Diskusi interaktif studi kasus kebocoran data dan etika siber.',
                    'Sesi tanya jawab peserta.',
                ]),
                'status' => 'published',
            ],
        ];

        foreach ($events as $ev) {
            Event::updateOrCreate(['slug' => $ev['slug']], $ev);
        }

        // 4. Organizations / Organisasi
        $organizations = [
            [
                'code' => 'BEM',
                'name' => 'Badan Eksekutif Mahasiswa (BEM)',
                'slug' => 'badan-eksekutif-mahasiswa',
                'level' => 'Tingkat Universitas / Perguruan Tinggi',
                'role_type' => 'Lembaga Eksekutif Tertinggi',
                'description' => 'Lembaga eksekutif mahasiswa yang bertugas mengoordinasikan, merancang, dan mengeksekusi program kerja kemahasiswaan lintas fakultas, mengawal advokasi hak mahasiswa ke pimpinan perguruan tinggi, serta menjalin kemitraan strategis eksternal.',
                'functions' => json_encode([
                    'Mengimplementasikan Garis-Garis Besar Haluan Program Kerja hasil musyawarah perwakilan mahasiswa.',
                    'Melakukan advokasi kebijakan finansial, sarana prasarana, dan hak kebebasan akademik mahasiswa.',
                    'Mengoordinasikan agenda berskala universitas bersama HIMA, HMPS, dan UKM.',
                    'Mewakili suara resmi mahasiswa dalam forum kemitraan perguruan tinggi regional dan nasional.',
                ]),
                'structure_overview' => json_encode([
                    'Presiden & Wakil Presiden Mahasiswa',
                    'Sekretariat Jenderal & Bendahara Umum',
                    'Kementerian Dalam Negeri (Koordinasi Ormawa)',
                    'Kementerian Luar Negeri & Hubungan Antar Lembaga',
                    'Kementerian Sosial, Advokasi, & Kesejahteraan Mahasiswa',
                    'Kementerian Riset, Teknologi, & Pengembangan Keilmuan',
                    'Kementerian Pengabdian Masyarakat & Lingkungan Hidup',
                    'Kementerian Minat, Bakat, & Olahraga',
                    'Biro Komunikasi, Informasi, & Media Kreatif',
                ]),
                'sort_order' => 1,
                'status' => 'published',
            ],
            [
                'code' => 'MAM',
                'name' => 'Majelis Aspirasi Mahasiswa (MAM)',
                'slug' => 'majelis-aspirasi-mahasiswa',
                'level' => 'Tingkat Universitas / Perguruan Tinggi',
                'role_type' => 'Lembaga Permusyawaratan & Kedaulatan',
                'description' => 'Lembaga permusyawaratan tertinggi mahasiswa yang memegang kedaulatan mahasiswa melalui sidang pleno, musyawarah besar, penyerapan aspirasi murni dari seluruh lini kampus, serta penetapan ketetapan pokok organisasi.',
                'functions' => json_encode([
                    'Menyelenggarakan Musyawarah Besar Mahasiswa dan Sidang Istimewa.',
                    'Menyerap, mengolah, dan merumuskan pokok-pokok aspirasi mahasiswa menjadi rekomendasi kebijakan.',
                    'Menetapkan anggaran dasar dan pedoman pokok keorganisasian mahasiswa.',
                    'Melantik dan mengambil sumpah amanah jajaran pengurus BEM terpilih.',
                ]),
                'structure_overview' => json_encode([
                    'Presidium Sidang Utama',
                    'Komisi Penyerapan Aspirasi & Hubungan Massa',
                    'Komisi Konstitusi & Tata Tertib Organisasi',
                    'Badan Pengawas Kebijakan Kemahasiswaan',
                ]),
                'sort_order' => 2,
                'status' => 'published',
            ],
            [
                'code' => 'DPM',
                'name' => 'Dewan Perwakilan Mahasiswa (DPM)',
                'slug' => 'dewan-perwakilan-mahasiswa',
                'level' => 'Tingkat Universitas / Perguruan Tinggi',
                'role_type' => 'Lembaga Legislatif & Pengawasan',
                'description' => 'Lembaga legislatif yang menjalankan fungsi perancangan regulasi kemahasiswaan (undang-undang ormawa), pengawasan terhadap kinerja anggaran dan program BEM, serta menyalurkan aspirasi konstituen fakultas.',
                'functions' => json_encode([
                    'Menyusun dan mengesahkan regulasi kemahasiswaan yang adil dan transparan.',
                    'Mengawasi implementasi program kerja serta serapan anggaran kemahasiswaan BEM.',
                    'Mengadakan rapat dengar pendapat (RDP) dan evaluasi berkala kinerja menteri.',
                    'Menyelenggarakan forum jajak pendapat berkala di tiap fakultas.',
                ]),
                'structure_overview' => json_encode([
                    'Ketua & Wakil Ketua DPM',
                    'Komisi I: Legislasi & Produk Hukum Mahasiswa',
                    'Komisi II: Anggaran & Pengawasan Keuangan',
                    'Komisi III: Pengawasan Bidang Internal & Kesejahteraan',
                    'Badan Kehormatan Dewan',
                ]),
                'sort_order' => 3,
                'status' => 'published',
            ],
            [
                'code' => 'HIMA',
                'name' => 'Himpunan Mahasiswa Fakultas (HIMA)',
                'slug' => 'himpunan-mahasiswa-fakultas',
                'level' => 'Tingkat Fakultas',
                'role_type' => 'Koordinator Kemahasiswaan Fakultas',
                'description' => 'Organisasi kemahasiswaan tingkat fakultas yang mewadahi aspirasi, koordinasi kegiatan keilmuan rumpun fakultas, serta menjembatani komunikasi mahasiswa dengan dekanat.',
                'functions' => json_encode([
                    'Mengoordinasikan kegiatan akademik dan non-akademik di tingkat fakultas.',
                    'Mengawal advokasi fasilitas laboratorium, ruang belajar, dan layanan dekanat.',
                    'Membina solidaritas antarmahasiswa program studi dalam satu fakultas.',
                ]),
                'structure_overview' => json_encode([
                    'Ketua Himpunan Fakultas',
                    'Divisi Pengembangan Akademik & Profesi',
                    'Divisi Advokasi Kesejahteraan Mahasiswa Fakultas',
                    'Divisi Minat, Olahraga, & Seni Fakultas',
                ]),
                'sort_order' => 4,
                'status' => 'published',
            ],
            [
                'code' => 'HMPS',
                'name' => 'Himpunan Mahasiswa Program Studi (HMPS)',
                'slug' => 'himpunan-mahasiswa-program-studi',
                'level' => 'Tingkat Program Studi / Jurusan',
                'role_type' => 'Lembaga Pengembangan Profesi & Keilmuan Spesifik',
                'description' => 'Organisasi kemahasiswaan di lini program studi yang fokus pada pendalaman keahlian spesifik kurikulum, kelompok belajar riset mahasiswa, dan penyelenggaraan kompetisi bidang keilmuan.',
                'functions' => json_encode([
                    'Menyelenggarakan workshop dan sertifikasi keahlian pendukung karier lulusan.',
                    'Mengelola kelompok studi minat riset mahasiswa jurusan.',
                    'Membangun jejaring kolaborasi dengan alumni dan industri profesional bidang terkait.',
                ]),
                'structure_overview' => json_encode([
                    'Ketua HMPS',
                    'Divisi Riset & Kajian Profesi',
                    'Divisi Pengembangan Bakat & Komunikasi Mahasiswa',
                    'Biro Media Informasi & Hubungan Alumni',
                ]),
                'sort_order' => 5,
                'status' => 'published',
            ],
            [
                'code' => 'UKM',
                'name' => 'Unit Kegiatan Mahasiswa (UKM)',
                'slug' => 'unit-kegiatan-mahasiswa',
                'level' => 'Tingkat Universitas / Perguruan Tinggi',
                'role_type' => 'Wadah Minat, Bakat, Seni, & Penalaran Mahasiswa',
                'description' => 'Organisasi kemahasiswaan otonom yang memfasilitasi minat khusus mahasiswa di bidang olahraga prestasi, seni budaya, kepramukaan/menwa, pers mahasiswa, pecinta alam, dan penalaran ilmiah.',
                'functions' => json_encode([
                    'Membina atlet dan talenta mahasiswa guna berprestasi di kompetisi nasional/internasional.',
                    'Menyelenggarakan latihan rutin terprogram dan sertifikasi keahlian khusus.',
                    'Mengharumkan nama almamater perguruan tinggi melalui unjuk bakat kreatif.',
                ]),
                'structure_overview' => json_encode([
                    'Forum Komunikasi UKM Universitas',
                    'Rumpun UKM Olahraga & Bela Diri',
                    'Rumpun UKM Seni, Musik, & Budaya',
                    'Rumpun UKM Penalaran Ilmiah & Jurnalistik Mahasiswa',
                    'Rumpun UKM Khusus (Pramuka, KSR-PMI, Menwa, Mahapala)',
                ]),
                'sort_order' => 6,
                'status' => 'published',
            ],
        ];

        foreach ($organizations as $org) {
            Organization::updateOrCreate(['slug' => $org['slug']], $org);
        }
    }
}
