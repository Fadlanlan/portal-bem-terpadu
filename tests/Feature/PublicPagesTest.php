<?php

namespace Tests\Feature;

use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    /**
     * Halaman beranda dapat diakses dan menampilkan identitas utama.
     */
    public function test_home_page_returns_successful_response_and_renders_brand(): void
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('Portal BEM Terpadu');
        $response->assertSee('Satu Pintu Informasi');
        $response->assertSee('Sampaikan Suara Anda');
        $response->assertSee('Alur Aspirasi Terstruktur');
    }

    /**
     * Halaman daftar warta & berita dapat diakses.
     */
    public function test_news_index_returns_successful_response_and_lists_articles(): void
    {
        $response = $this->get(route('news.index'));

        $response->assertStatus(200);
        $response->assertSee('Warta');
        $response->assertSee('Berita Mahasiswa');
        $response->assertSee('Kanal Digital Aspirasi Mahasiswa Resmi Disosialisasikan');
    }

    /**
     * Filter kategori berita berfungsi dengan benar.
     */
    public function test_news_index_filters_by_category(): void
    {
        $response = $this->get(route('news.index', ['kategori' => 'Pelatihan']));

        $response->assertStatus(200);
        $response->assertSee('Lokakarya Tata Kelola Organisasi Mahasiswa Berbasis Digital');
    }

    /**
     * Pencarian berita berfungsi dan menampilkan empty state jika tidak ada hasil.
     */
    public function test_news_index_search_returns_empty_state_when_not_found(): void
    {
        $response = $this->get(route('news.index', ['cari' => 'kata-kunci-yang-pasti-tidak-ada-xyz123']));

        $response->assertStatus(200);
        $response->assertSee('Tidak Ada Berita yang Sesuai');
    }

    /**
     * Halaman detail berita dapat diakses dengan slug valid.
     */
    public function test_news_show_returns_successful_response_for_existing_slug(): void
    {
        $response = $this->get(route('news.show', 'penerimaan-aspirasi-terpadu-periode-2026'));

        $response->assertStatus(200);
        $response->assertSee('Kanal Digital Aspirasi Mahasiswa Resmi Disosialisasikan');
        $response->assertSee('Kembali ke Daftar Berita');
    }

    /**
     * Halaman detail berita mengembalikan 404 untuk slug yang tidak terdaftar.
     */
    public function test_news_show_returns_404_for_unknown_slug(): void
    {
        $response = $this->get(route('news.show', 'slug-berita-palsu-yang-tidak-ada'));

        $response->assertStatus(404);
        $response->assertSee('Halaman Tidak Ditemukan');
    }

    /**
     * Halaman daftar program kerja dapat diakses.
     */
    public function test_programs_index_returns_successful_response(): void
    {
        $response = $this->get(route('programs.index'));

        $response->assertStatus(200);
        $response->assertSee('Program Kerja Mahasiswa');
        $response->assertSee('Advokasi Transparansi');
    }

    /**
     * Filter status program kerja berfungsi.
     */
    public function test_programs_index_filters_by_status(): void
    {
        $response = $this->get(route('programs.index', ['status' => 'Berlangsung']));

        $response->assertStatus(200);
        $response->assertSee('Berlangsung');
    }

    /**
     * Halaman detail program kerja dapat diakses dengan slug valid.
     */
    public function test_programs_show_returns_successful_response_for_existing_slug(): void
    {
        $response = $this->get(route('programs.show', 'advokasi-transparansi-biaya-akademik'));

        $response->assertStatus(200);
        $response->assertSee('Advokasi Transparansi &amp; Bantuan Finansial', false);
        $response->assertSee('Sasaran Kegiatan');
        $response->assertSee('Indikator Kinerja Utama');
    }

    /**
     * Halaman detail program kerja mengembalikan 404 untuk slug yang tidak terdaftar.
     */
    public function test_programs_show_returns_404_for_unknown_slug(): void
    {
        $response = $this->get(route('programs.show', 'slug-program-tidak-ada'));

        $response->assertStatus(404);
    }

    /**
     * Halaman kalender agenda dapat diakses dan menampilkan agenda mendatang & riwayat.
     */
    public function test_events_index_returns_successful_response(): void
    {
        $response = $this->get(route('events.index'));

        $response->assertStatus(200);
        $response->assertSee('Agenda');
        $response->assertSee('Jadwal Kegiatan');
        $response->assertSee('Agenda Mendatang');
        $response->assertSee('Riwayat Agenda yang Telah Terlaksana');
    }

    /**
     * Halaman detail agenda dapat diakses dengan slug valid.
     */
    public function test_events_show_returns_successful_response_for_existing_slug(): void
    {
        $response = $this->get(route('events.show', 'dialog-terbuka-rektorat-mahasiswa-2026'));

        $response->assertStatus(200);
        $response->assertSee('Dialog Terbuka: Refleksi Kebijakan Kampus &amp; Mahasiswa', false);
        $response->assertSee('Auditorium Gedung Utama Lt. 3');
    }

    /**
     * Halaman detail agenda mengembalikan 404 untuk slug yang tidak terdaftar.
     */
    public function test_events_show_returns_404_for_unknown_slug(): void
    {
        $response = $this->get(route('events.show', 'slug-agenda-yang-tidak-ada'));

        $response->assertStatus(404);
    }

    /**
     * Halaman struktur organisasi dapat diakses dan menampilkan entitas BEM, MAM, DPM, dsb.
     */
    public function test_organizations_index_returns_successful_response(): void
    {
        $response = $this->get(route('organizations.index'));

        $response->assertStatus(200);
        $response->assertSee('Struktur Organisasi Mahasiswa');
        $response->assertSee('BEM');
        $response->assertSee('MAM');
        $response->assertSee('DPM');
        $response->assertSee('HIMA');
        $response->assertSee('HMPS');
        $response->assertSee('UKM');
    }

    /**
     * Halaman tentang dapat diakses.
     */
    public function test_about_page_returns_successful_response(): void
    {
        $response = $this->get(route('about'));

        $response->assertStatus(200);
        $response->assertSee('Tentang Portal BEM Terpadu');
        $response->assertSee('Visi Kelembagaan');
        $response->assertSee('Misi Utama');
    }

    /**
     * Halaman kontak dapat diakses.
     */
    public function test_contact_page_returns_successful_response(): void
    {
        $response = $this->get(route('contact'));

        $response->assertStatus(200);
        $response->assertSee('Kontak');
        $response->assertSee('Sekretariat BEM');
        $response->assertSee('Gedung Pusat Kegiatan Mahasiswa');
    }

    /**
     * Halaman FAQ dapat diakses dan memuat pertanyaan utama.
     */
    public function test_faq_page_returns_successful_response(): void
    {
        $response = $this->get(route('faq'));

        $response->assertStatus(200);
        $response->assertSee('Pertanyaan yang Sering Diajukan');
        $response->assertSee('Apa tujuan utama dibangunnya Portal BEM Terpadu?');
    }

    /**
     * Halaman informasi kanal aspirasi dapat diakses dan memuat alur prioritas serta navigasi fitur.
     */
    public function test_aspirations_info_page_returns_successful_response(): void
    {
        $response = $this->get(route('aspirations.info'));

        $response->assertStatus(200);
        $response->assertSee('Kanal Aspirasi Mahasiswa Terpadu');
        $response->assertSee('Sampaikan Aspirasi Baru');
        $response->assertSee('Lacak Status Aspirasi');
    }
}
