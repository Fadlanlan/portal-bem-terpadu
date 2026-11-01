<?php

namespace Database\Seeders;

use App\Models\ChatFaq;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ChatFaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            ['question' => 'Apa tujuan utama dibangunnya Portal BEM Terpadu?', 'answer' => 'Portal BEM Terpadu dibangun sebagai pusat informasi resmi, media transparansi program kerja, kalender kegiatan bersama seluruh organisasi kemahasiswaan, serta kanal penyampaian aspirasi mahasiswa yang aman, terstruktur, dan terdokumentasi dengan baik.'],
            ['question' => 'Apa perbedaan antara aspirasi, pengaduan, dan permohonan informasi?', 'answer' => 'Aspirasi merupakan gagasan, ide konstruktif, atau usulan pembaruan untuk kemajuan bersama. Pengaduan berfokus pada pelaporan kendala faktual. Permohonan informasi adalah klarifikasi regulasi atau pedoman kemahasiswaan.'],
            ['question' => 'Bagaimana sistem pelacakan aspirasi bekerja setelah modul backend selesai?', 'answer' => 'Setiap aspirasi yang dikirimkan akan menghasilkan Nomor Tiket Unik. Pelapor dapat memantau riwayat status secara transparan.'],
            ['question' => 'Apakah mahasiswa dapat menyampaikan aspirasi secara terlindung/anonim?', 'answer' => 'Ya, sistem dirancang untuk mendukung opsi pelaporan dengan perlindungan identitas.'],
            ['question' => 'Bagaimana cara mendaftarkan agenda kegiatan UKM/HIMA ke dalam kalender portal?', 'answer' => 'Perwakilan terverifikasi dari masing-masing UKM dan HIMA akan diberikan akses akun editor untuk mengajukan jadwal kegiatan ke kalender bersama.'],
            ['question' => 'Apakah seluruh data yang tampil saat ini merupakan informasi resmi definitif?', 'answer' => 'Informasi yang tampil pada tahap antarmuka ini merupakan data demonstrasi dan kerangka simulasi arsitektur.'],
        ];

        foreach ($faqs as $index => $faq) {
            ChatFaq::updateOrCreate(
                ['slug' => Str::slug($faq['question'])],
                [
                    'question' => $faq['question'],
                    'answer' => $faq['answer'],
                    'is_published' => true,
                    'sort_order' => $index + 1,
                ]
            );
        }
    }
}
