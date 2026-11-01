<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PageController extends Controller
{
    /**
     * Tampilkan halaman Tentang Portal & Organisasi.
     */
    public function about(): View
    {
        $pillars = [
            [
                'title' => 'Transparansi & Akuntabilitas',
                'description' => 'Mewujudkan tata kelola organisasi kemahasiswaan yang terbuka dalam publikasi agenda, alokasi program kerja, dan pelaporan capaian.',
            ],
            [
                'title' => 'Advokasi yang Terukur',
                'description' => 'Menjadi jembatan komunikasi aktif yang menyuarakan hak, kesejahteraan finansial, dan aspirasi akademik mahasiswa secara santun namun tegas.',
            ],
            [
                'title' => 'Sinergi & Kolaborasi Terpadu',
                'description' => 'Mengintegrasikan seluruh simpul organisasi mahasiswa (BEM, MAM, DPM, HIMA, HMPS, UKM) dalam satu ekosistem informasi bersama.',
            ],
            [
                'title' => 'Inovasi Digital Berkelanjutan',
                'description' => 'Memanfaatkan teknologi informasi guna memangkas birokrasi, mendokumentasikan arsip kelembagaan, dan memudahkan akses layanan kemahasiswaan.',
            ],
        ];

        return view('pages.about', compact('pillars'));
    }

    /**
     * Tampilkan halaman Kontak & Sekretariat.
     */
    public function contact(): View
    {
        return view('pages.contact');
    }

    /**
     * Tampilkan halaman Pertanyaan yang Sering Diajukan (FAQ).
     */
    public function faq(): View
    {
        $faqs = [
            [
                'id' => 'faq-1',
                'question' => 'Apa tujuan utama dibangunnya Portal BEM Terpadu?',
                'answer' => 'Portal BEM Terpadu dibangun sebagai pusat informasi resmi, media transparansi program kerja, kalender kegiatan bersama seluruh organisasi kemahasiswaan, serta kanal penyampaian aspirasi mahasiswa yang aman, terstruktur, dan terdokumentasi dengan baik.',
            ],
            [
                'id' => 'faq-2',
                'question' => 'Apa perbedaan antara aspirasi, pengaduan, dan permohonan informasi?',
                'answer' => 'Aspirasi merupakan gagasan, ide konstruktif, atau usulan pembaruan untuk kemajuan bersama. Pengaduan berfokus pada pelaporan kendala faktual (misalnya fasilitas rusak atau hambatan administrasi). Permohonan informasi adalah klarifikasi regulasi atau pedoman kemahasiswaan. Semua dapat disalurkan melalui kategori formulir yang tepat pada modul aspirasi.',
            ],
            [
                'id' => 'faq-3',
                'question' => 'Bagaimana sistem pelacakan aspirasi bekerja setelah modul backend selesai?',
                'answer' => 'Setiap aspirasi yang dikirimkan akan menghasilkan Nomor Tiket Unik (misalnya BEM-2026-XXXX). Pelapor dapat memantau riwayat status: Diterima → Diverifikasi → Diproses → Menunggu Tindak Lanjut → Selesai/Ditolak secara transparan tanpa membuka privasi data pribadi ke pihak publik.',
            ],
            [
                'id' => 'faq-4',
                'question' => 'Apakah mahasiswa dapat menyampaikan aspirasi secara terlindung/anonim?',
                'answer' => 'Ya, sistem dirancang untuk mendukung opsi pelaporan dengan perlindungan identitas. Namun, untuk laporan yang membutuhkan investigasi kasus perseorangan (seperti sengketa beasiswa tertentu), identitas yang terverifikasi secara internal sangat disarankan agar tim advokasi dapat menindaklanjuti secara langsung.',
            ],
            [
                'id' => 'faq-5',
                'question' => 'Bagaimana cara mendaftarkan agenda kegiatan UKM/HIMA ke dalam kalender portal?',
                'answer' => 'Pada tahap rilis panel pengelolaan mendatang, perwakilan terverifikasi dari masing-masing UKM dan HIMA akan diberikan akses akun editor untuk mengajukan jadwal kegiatan ke kalender bersama guna menghindari benturan fasilitas ruangan dan waktu.',
            ],
            [
                'id' => 'faq-6',
                'question' => 'Apakah seluruh data yang tampil saat ini merupakan informasi resmi definitif?',
                'answer' => 'Informasi yang tampil pada tahap antarmuka ini merupakan data demonstrasi dan kerangka simulasi arsitektur untuk memvalidasi tata letak, fungsi navigasi, dan kesiapan sistem sebelum integrasi database resmi dilakukan pada tahap selanjutnya.',
            ],
        ];

        return view('pages.faq', compact('faqs'));
    }

    /**
     * Tampilkan halaman pengenalan sistem aspirasi (penjelasan alur sebelum modul backend aktif).
     */
    public function aspirationInfo(): View
    {
        return view('pages.aspiration-info');
    }
}
