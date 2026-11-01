<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class NewsController extends Controller
{
    /**
     * Dapatkan daftar berita demonstrasi terstruktur.
     *
     * @return array<int, array<string, mixed>>
     */
    private function getArticles(): array
    {
        return [
            [
                'slug' => 'penerimaan-aspirasi-terpadu-periode-2026',
                'title' => 'Kanal Digital Aspirasi Mahasiswa Resmi Disosialisasikan',
                'excerpt' => 'BEM meluncurkan inisiatif kanal terpadu untuk memastikan setiap suara, kritik konstruktif, dan saran mahasiswa terkelola secara transparan dan akuntabel.',
                'content' => "Dalam upaya memperkuat budaya demokrasi dan keterbukaan di lingkungan kampus, Badan Eksekutif Mahasiswa (BEM) secara resmi menyosialisasikan kerangka kerja sistem kanal aspirasi digital terpadu.\n\nSistem ini dirancang untuk menjawab tantangan komunikasi antara mahasiswa, organisasi kemahasiswaan, dan pimpinan perguruan tinggi. Melalui sistem tiket terlacak, setiap laporan, pertanyaan kebijakan akademik, maupun usulan fasilitas akan mendapatkan alur verifikasi resmi tanpa kekhawatiran atas kebocoran identitas bagi mahasiswa yang memilih opsi laporan terjaga.\n\n\"Kanal aspirasi bukan sekadar kotak saran digital, melainkan komitmen institusional agar setiap masukan didengar dan ditindaklanjuti secara terukur,\" ungkap narahubung divisi advokasi dalam forum sosialisasi.",
                'category' => 'Advokasi & Aspirasi',
                'date' => '24 Oktober 2026',
                'author' => 'Biro Komunikasi & Informasi',
                'read_time' => '3 menit',
                'tags' => ['Aspirasi', 'Digitalisasi', 'Transparansi'],
            ],
            [
                'slug' => 'pembukaan-pendaftaran-delegasi-studi-banding',
                'title' => 'Pendaftaran Delegasi Forum Mahasiswa Nasional Telah Dibuka',
                'excerpt' => 'Kesempatan bagi perwakilan organisasi kemahasiswaan untuk berpartisipasi dalam pertukaran gagasan kepemimpinan di tingkat perguruan tinggi se-Indonesia.',
                'content' => "Kementerian Luar Negeri BEM membuka pendaftaran seleksi delegasi mahasiswa untuk menghadiri Temu Wicara Nasional dan Konferensi Tahunan Mahasiswa Nusantara yang akan dihelat bulan depan.\n\nProgram ini bertujuan memperluas wawasan kebangsaan, jejaring kolaborasi antarkampus, serta merumuskan resolusi bersama mengenai masa depan pendidikan tinggi berbasis teknologi berkelanjutan.\n\nPersyaratan meliputi komitmen keaktifan organisasi, kepemilikan gagasan inovasi sosial, dan kesiapan memaparkan kajian komparatif dalam forum panel diskusi.",
                'category' => 'Pengumuman',
                'date' => '22 Oktober 2026',
                'author' => 'Kementerian Luar Negeri BEM',
                'read_time' => '4 menit',
                'tags' => ['Delegasi', 'Nasional', 'Kepemimpinan'],
            ],
            [
                'slug' => 'lokakarya-manajemen-keorganisasian-2026',
                'title' => 'Lokakarya Tata Kelola Organisasi Mahasiswa Berbasis Digital',
                'excerpt' => 'Pelatihan intensif bagi jajaran pengurus UKM, HIMA, dan HMPS guna memperkuat transparansi administrasi serta sinergi kegiatan antarkampus.',
                'content' => "Tata kelola organisasi yang akuntabel merupakan fondasi penting bagi kematangan lembaga mahasiswa. Untuk merealisasikannya, BEM menginisiasi Lokakarya Tata Kelola Organisasi Mahasiswa 2026.\n\nAgenda ini berfokus pada standardisasi penyusunan laporan pertanggungjawaban (LPJ), pengelolaan arsip digital, manajemen risiko kegiatan lapangan, serta etika publikasi informasi organisasi di ruang siber.\n\nSeluruh perwakilan HIMA, HMPS, dan UKM didorong mengirimkan penanggung jawab administrasi dan keuangan guna memastikan keselarasan regulasi internal kampus.",
                'category' => 'Pelatihan',
                'date' => '19 Oktober 2026',
                'author' => 'Kementerian Dalam Negeri BEM',
                'read_time' => '5 menit',
                'tags' => ['Tata Kelola', 'Workshop', 'Ormawa'],
            ],
            [
                'slug' => 'pembentukan-posko-keringanan-ukt-semester-ganjil',
                'title' => 'Posko Advokasi Keringanan Biaya Pendidikan Resmi Beroperasi',
                'excerpt' => 'BEM membuka layanan pendampingan pengajuan penyesuaian UKT, pencarian sponsor beasiswa, dan verifikasi berkas bagi mahasiswa terdampak kendala finansial.',
                'content' => "Menanggapi dinamika ekonomi dan kebutuhan penyesuaian biaya kuliah, BEM membentuk Posko Bersama Advokasi Finansial Mahasiswa.\n\nPosko ini bertugas memverifikasi kelengkapan berkas pemohon, menyusun rekomendasi faktual ke pihak biro keuangan rektorat, dan memastikan tidak ada mahasiswa yang putus kuliah karena kendala finansial yang belum tertangani.\n\nPelayanan dilaksanakan secara luring di sekretariat BEM setiap hari kerja pukul 09.00 - 15.00 WIB serta pendampingan berkas daring.",
                'category' => 'Advokasi & Aspirasi',
                'date' => '15 Oktober 2026',
                'author' => 'Kementerian Sosial & Advokasi BEM',
                'read_time' => '4 menit',
                'tags' => ['Advokasi', 'UKT', 'Kesejahteraan'],
            ],
            [
                'slug' => 'penutupan-bulan-bakti-sosial-kampus',
                'title' => 'Hasil Bulan Bakti Mahasiswa: Revitalisasi 3 Titik Ruang Belajar Komunitas',
                'excerpt' => 'Rangkaian kegiatan bakti sosial terpadu berhasil diselesaikan dengan keterlibatan lebih dari 150 relawan mahasiswa lintas fakultas.',
                'content' => "Program tahunan Bulan Bakti Sosial Mahasiswa ditutup dengan penyerahan fasilitas ruang belajar ramah anak di wilayah pinggiran kota.\n\nSelama 4 pekan pelaksanaan, mahasiswa lintas program studi berkontribusi dalam perbaikan sarana fisik, penyediaan pojok baca digital, dan pelatihan sains interaktif untuk siswa sekolah dasar sekitar.\n\nKeberhasilan ini membuktikan sinergi nyata kepedulian sivitas akademika dalam mewujudkan tridarma perguruan tinggi, khususnya aspek pengabdian masyarakat.",
                'category' => 'Kegiatan',
                'date' => '11 Oktober 2026',
                'author' => 'Kementerian Pengabdian Masyarakat BEM',
                'read_time' => '3 menit',
                'tags' => ['Pengabdian', 'Sosial', 'Relawan'],
            ],
            [
                'slug' => 'konsolidasi-akbar-evaluasi-kebijakan-akademik',
                'title' => 'Konsolidasi Terbuka Pembahasan Kurikulum Merdeka & Fasilitas Riset',
                'excerpt' => 'Forum diskusi perwakilan kelas membahas efektivitas implementasi kurikulum, akses laboratorium, dan ketersediaan jurnal referensi ilmiah.',
                'content' => "Menjelang evaluasi tengah semester, perwakilan mahasiswa dari berbagai himpunan prodi berkumpul dalam forum konsolidasi terbuka.\n\nDiskusi menyoroti ketersediaan sarana riset mandiri, akses langganan pangkalan data jurnal bereputasi, serta mekanisme konversi kredit kegiatan kemahasiswaan ke dalam portofolio akademik.\n\nSeluruh poin catatan kritis dihimpun dalam naskah memorandum resmi yang akan diserahkan pada audiensi pimpinan dekanat mendatang.",
                'category' => 'Akademik',
                'date' => '05 Oktober 2026',
                'author' => 'Kementerian Riset & Keilmuan BEM',
                'read_time' => '5 menit',
                'tags' => ['Kurikulum', 'Riset', 'Evaluasi'],
            ],
        ];
    }

    /**
     * Tampilkan daftar berita dengan filter kategori dan pencarian.
     */
    public function index(Request $request): View
    {
        $hasDb = false;
        try {
            $hasDb = Schema::hasTable('articles');
        } catch (\Throwable $e) {
            $hasDb = false;
        }

        $categories = [
            'Semua',
            'Advokasi & Aspirasi',
            'Pengumuman',
            'Pelatihan',
            'Kegiatan',
            'Akademik',
        ];

        $currentCategory = $request->query('kategori', 'Semua');
        $searchQuery = trim((string) $request->query('cari', ''));

        if ($hasDb) {
            $query = Article::published()->latest('published_at');

            if ($currentCategory !== 'Semua') {
                $query->where('category', $currentCategory);
            }

            if ($searchQuery !== '') {
                $query->where(function ($q) use ($searchQuery) {
                    $q->where('title', 'like', "%{$searchQuery}%")
                        ->orWhere('excerpt', 'like', "%{$searchQuery}%")
                        ->orWhere('content', 'like', "%{$searchQuery}%");
                });
            }

            // Paginasi 9 entri per halaman; withQueryString() mempertahankan filter
            // kategori dan kata kunci pencarian saat berpindah halaman.
            $articles = $query->paginate(9)->withQueryString();

            return view('pages.news.index', [
                'articles' => $articles,
                'categories' => $categories,
                'currentCategory' => $currentCategory,
                'searchQuery' => $searchQuery,
                'totalCount' => $articles->total(),
            ]);
        }

        $allArticles = $this->getArticles();

        $filtered = array_filter($allArticles, function (array $item) use ($currentCategory, $searchQuery): bool {
            $matchCategory = ($currentCategory === 'Semua' || $item['category'] === $currentCategory);
            $matchSearch = true;

            if ($searchQuery !== '') {
                $haystack = mb_strtolower($item['title'] . ' ' . $item['excerpt'] . ' ' . $item['content']);
                $needle = mb_strtolower($searchQuery);
                $matchSearch = str_contains($haystack, $needle);
            }

            return $matchCategory && $matchSearch;
        });

        return view('pages.news.index', [
            'articles' => array_values($filtered),
            'categories' => $categories,
            'currentCategory' => $currentCategory,
            'searchQuery' => $searchQuery,
            'totalCount' => count($filtered),
        ]);
    }

    /**
     * Tampilkan rincian berita berdasarkan slug.
     */
    public function show(string $slug): View
    {
        $hasDb = false;
        try {
            $hasDb = Schema::hasTable('articles');
        } catch (\Throwable $e) {
            $hasDb = false;
        }

        if ($hasDb) {
            $article = Article::published()->where('slug', $slug)->first();

            if (! $article) {
                abort(404, 'Artikel berita tidak ditemukan.');
            }

            $relatedArticles = Article::published()
                ->where('slug', '!=', $article->slug)
                ->where('category', $article->category)
                ->latest('published_at')
                ->take(2)
                ->get();

            return view('pages.news.show', [
                'article' => $article,
                'relatedArticles' => $relatedArticles,
            ]);
        }

        $allArticles = $this->getArticles();
        $article = null;

        foreach ($allArticles as $item) {
            if ($item['slug'] === $slug) {
                $article = $item;
                break;
            }
        }

        if (! $article) {
            abort(404, 'Artikel berita tidak ditemukan.');
        }

        // Cari berita terkait berdasarkan kategori yang sama
        $relatedArticles = array_filter($allArticles, function (array $item) use ($article): bool {
            return $item['slug'] !== $article['slug'] && $item['category'] === $article['category'];
        });

        // Ambil maksimal 2 artikel terkait
        $relatedArticles = array_slice(array_values($relatedArticles), 0, 2);

        return view('pages.news.show', [
            'article' => $article,
            'relatedArticles' => $relatedArticles,
        ]);
    }
}
