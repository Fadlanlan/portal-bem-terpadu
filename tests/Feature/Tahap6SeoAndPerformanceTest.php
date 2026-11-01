<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Mail\NewAspirationAdminMail;
use App\Models\Article;
use App\Models\Aspiration;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Pengujian regresi Tahap 6: SEO halaman publik, paginasi daftar konten,
 * perbaikan tautan email notifikasi admin, dan halaman error konsisten.
 */
class Tahap6SeoAndPerformanceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Helper membuat pengguna pengelola.
     */
    private function createUser(UserRole $role = UserRole::Editor): User
    {
        return User::create([
            'name' => 'Pengguna ' . $role->value,
            'email' => $role->value . '_' . uniqid() . '@bem.kampus.ac.id',
            'role' => $role,
            'password' => Hash::make('Password123!'),
            'is_active' => true,
        ]);
    }

    /**
     * Sitemap XML hanya memuat URL publik dan mengecualikan draf serta area admin.
     */
    public function test_sitemap_xml_contains_public_urls_and_excludes_admin_and_drafts(): void
    {
        $published = Article::create([
            'title' => 'Warta Publik untuk Sitemap',
            'slug' => 'warta-publik-untuk-sitemap',
            'category' => 'Umum',
            'excerpt' => 'Ringkasan warta publik',
            'content' => 'Konten warta publik',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $draft = Article::create([
            'title' => 'Draf Rahasia untuk Sitemap',
            'slug' => 'draf-rahasia-untuk-sitemap',
            'category' => 'Internal',
            'excerpt' => 'Ringkasan draf',
            'content' => 'Konten draf',
            'status' => 'draft',
            'published_at' => null,
        ]);

        $response = $this->get(route('sitemap'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
        $response->assertSee('<urlset', false);
        $response->assertSee(route('home'), false);
        $response->assertSee(route('news.show', $published->slug), false);
        $response->assertDontSee(route('news.show', $draft->slug), false);
        $response->assertDontSee('/admin', false);
    }

    /**
     * robots.txt memblokir area sensitif (admin, kanal aspirasi) dan menyebut sitemap.
     */
    public function test_robots_txt_disallows_admin_and_sensitive_aspiration_routes(): void
    {
        $response = $this->get(route('robots'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
        $response->assertSee('User-agent: *', false);
        $response->assertSee('Disallow: /admin', false);
        $response->assertSee('Disallow: /aspirasi/kirim', false);
        $response->assertSee('Disallow: /aspirasi/lacak', false);
        $response->assertSee('Disallow: /aspirasi/sukses', false);
        $response->assertSee('Sitemap:', false);
    }

    /**
     * Halaman publik menyertakan canonical, Open Graph, dan Twitter card.
     */
    public function test_public_pages_include_canonical_and_open_graph_metadata(): void
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('<link rel="canonical"', false);
        $response->assertSee('og:title', false);
        $response->assertSee('og:description', false);
        $response->assertSee('og:locale', false);
        $response->assertSee('twitter:card', false);
        $response->assertSee('name="robots"', false);
    }

    /**
     * Detail berita menyertakan Open Graph image dari gambar sampul.
     */
    public function test_news_detail_includes_open_graph_image(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('articles/sampul-sitemap.jpg', 'isi-sampul');

        $article = Article::create([
            'title' => 'Warta Berita Uji Open Graph',
            'slug' => 'warta-berita-uji-open-graph',
            'category' => 'Umum',
            'excerpt' => 'Ringkasan warta berita uji open graph',
            'content' => 'Konten warta berita uji open graph',
            'status' => 'published',
            'published_at' => now(),
            'image' => 'articles/sampul-sitemap.jpg',
        ]);

        $response = $this->get(route('news.show', $article->slug));

        $response->assertStatus(200);
        $response->assertSee('og:image', false);
        $response->assertSee($article->image_url, false);
        $response->assertSee('og:type', false);
    }

    /**
     * Daftar berita memuat tautan paginasi dan membagi konten antar halaman.
     */
    public function test_news_list_pagination_renders_links_across_pages(): void
    {
        for ($i = 1; $i <= 12; $i++) {
            Article::create([
                'title' => 'Berita Paginasi ' . str_pad((string) $i, 2, '0', STR_PAD_LEFT),
                'slug' => 'berita-paginasi-' . str_pad((string) $i, 2, '0', STR_PAD_LEFT),
                'category' => 'Umum',
                'excerpt' => 'Ringkasan paginasi ' . $i,
                'content' => 'Konten paginasi ' . $i,
                'status' => 'published',
                'published_at' => now()->subMinutes($i),
            ]);
        }

        // Halaman pertama menampilkan artikel terbaru dan tautan ke halaman kedua.
        $first = $this->get(route('news.index'));
        $first->assertStatus(200);
        $first->assertSee('page=2', false);
        $first->assertSee('Berita Paginasi 01', false);

        // Halaman kedua menampilkan artikel paling lama.
        $second = $this->get(route('news.index', ['page' => 2]));
        $second->assertStatus(200);
        $second->assertSee('Berita Paginasi 12', false);
    }

    /**
     * Paginasi mempertahankan filter kategori pada tautan antar halaman.
     */
    public function test_news_list_pagination_persists_category_filter_in_links(): void
    {
        for ($i = 1; $i <= 11; $i++) {
            Article::create([
                'title' => 'Pelatihan Paginasi ' . str_pad((string) $i, 2, '0', STR_PAD_LEFT),
                'slug' => 'pelatihan-paginasi-' . str_pad((string) $i, 2, '0', STR_PAD_LEFT),
                'category' => 'Pelatihan',
                'excerpt' => 'Ringkasan pelatihan ' . $i,
                'content' => 'Konten pelatihan ' . $i,
                'status' => 'published',
                'published_at' => now()->subMinutes($i),
            ]);
        }

        Article::create([
            'title' => 'Berita Kategori Umum',
            'slug' => 'berita-kategori-umum',
            'category' => 'Umum',
            'excerpt' => 'Ringkasan umum',
            'content' => 'Konten umum',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $response = $this->get(route('news.index', ['kategori' => 'Pelatihan']));

        $response->assertStatus(200);
        $response->assertSee('page=2', false);
        $response->assertSee('kategori=Pelatihan', false);
        // Artikel kategori lain terfilter keluar.
        $response->assertDontSee('Berita Kategori Umum', false);
    }

    /**
     * Halaman dashboard admin menyertakan meta robots noindex.
     */
    public function test_admin_dashboard_has_noindex_robots_meta(): void
    {
        $admin = $this->createUser(UserRole::Admin);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('noindex, nofollow', false);
    }

    /**
     * Tautan email notifikasi admin mengarah ke rute show (binding ID), bukan nomor tiket.
     */
    public function test_admin_aspiration_notification_email_uses_show_route(): void
    {
        $aspiration = Aspiration::create([
            'ticket_number' => 'BEM-2026-LINK1',
            'tracking_code_hash' => Hash::make('TRK-AAAA-BBBB-CCCC'),
            'category' => 'akademik',
            'subject' => 'Subjek uji tautan email notifikasi admin',
            'message' => 'Isi pesan aspirasi untuk pengujian tautan email notifikasi admin.',
            'is_anonymous' => false,
            'reporter_name' => 'Pengujian Tautan',
            'reporter_email' => 'pengujian@bem.kampus.ac.id',
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $rendered = (new NewAspirationAdminMail($aspiration))->render();

        $this->assertStringContainsString(route('admin.aspirations.show', $aspiration), $rendered);
        // Format tautan lama (berbasis nomor tiket) tidak boleh muncul lagi.
        $this->assertStringNotContainsString('/admin/aspirasi/' . $aspiration->ticket_number, $rendered);
    }

    /**
     * Halaman daftar agenda menampilkan empty state ketika tidak ada riwayat agenda terlaksana.
     */
    public function test_past_events_empty_state_renders_when_no_history(): void
    {
        Event::create([
            'title' => 'Agenda Mendatang Uji Empty State',
            'slug' => 'agenda-mendatang-uji-empty-state',
            'category' => 'Webinar',
            'description' => 'Deskripsi agenda mendatang untuk pengujian empty state riwayat.',
            'location' => 'Auditorium Kampus',
            'started_at' => now()->addDay(),
            'ended_at' => now()->addDay()->addHour(),
            'organizer' => 'BEM Universitas',
            'status' => 'published',
        ]);

        $response = $this->get(route('events.index'));

        $response->assertStatus(200);
        $response->assertSee('Belum Ada Riwayat Agenda');
        $response->assertSee('Agenda Mendatang Uji Empty State');
    }

    /**
     * Halaman 500 konsisten dengan identitas visual portal dan tidak diindeks.
     */
    public function test_server_error_page_renders_consistent_identity(): void
    {
        Route::get('/__tahap6_server_error', fn () => abort(500))->middleware('web');

        $response = $this->get('/__tahap6_server_error');

        $response->assertStatus(500);
        $response->assertSee('Terjadi Kesalahan Sistem');
        $response->assertSee('noindex', false);
        $response->assertSee('Kembali ke Beranda');
    }

    /**
     * Halaman 404 konsisten dengan identitas visual portal dan tidak diindeks.
     */
    public function test_custom_404_page_renders_consistent_identity(): void
    {
        $response = $this->get('/halaman-rute-pasti-tidak-ditemukan-999');

        $response->assertStatus(404);
        $response->assertSee('Halaman Tidak Ditemukan');
        $response->assertSee('noindex', false);
        $response->assertSee('Kembali ke Beranda');
    }

    /**
     * Program kerja mendukung paginasi dan filter status serta departemen.
     */
    public function test_work_programs_list_pagination_and_filters(): void
    {
        for ($i = 1; $i <= 11; $i++) {
            \App\Models\WorkProgram::create([
                'name' => 'Program Advokasi ' . str_pad((string) $i, 2, '0', STR_PAD_LEFT),
                'slug' => 'program-advokasi-' . str_pad((string) $i, 2, '0', STR_PAD_LEFT),
                'organization' => 'Kementerian Sosial & Advokasi BEM',
                'department' => 'Sosial & Advokasi',
                'status' => 'Berlangsung',
                'publication_status' => 'published',
                'period' => '2026/2027',
                'description' => 'Deskripsi program advokasi ' . $i,
            ]);
        }

        \App\Models\WorkProgram::create([
            'name' => 'Program Riset Selesai',
            'slug' => 'program-riset-selesai',
            'organization' => 'Kementerian Riset & Keilmuan BEM',
            'department' => 'Riset & Keilmuan',
            'status' => 'Selesai',
            'publication_status' => 'published',
            'period' => '2026/2027',
            'description' => 'Deskripsi program riset selesai',
        ]);

        // Cek halaman 1 ada tautan paginasi
        $response = $this->get(route('programs.index'));
        $response->assertStatus(200);
        $response->assertSee('page=2', false);

        // Cek filter departemen
        $filteredResponse = $this->get(route('programs.index', ['departemen' => 'Sosial & Advokasi']));
        $filteredResponse->assertStatus(200);
        $filteredResponse->assertSee('Program Advokasi 01');
        $filteredResponse->assertDontSee('Program Riset Selesai');
    }
}
