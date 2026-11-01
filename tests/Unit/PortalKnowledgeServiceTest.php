<?php

namespace Tests\Unit;

use App\Models\Article;
use App\Services\Chat\PortalKnowledgeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortalKnowledgeServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_article_is_found(): void
    {
        Article::create([
            'title' => 'Jadwal UKT 2026',
            'slug' => 'jadwal-ukt-2026',
            'excerpt' => 'Informasi UKT',
            'content' => 'Pembayaran UKT dibuka.',
            'category' => 'Akademik',
            'status' => 'published',
            'published_at' => now()->subDay(),
            'author_name' => 'BEM',
        ]);

        $service = new PortalKnowledgeService;
        $results = $service->search('UKT');

        $this->assertNotEmpty($results);
        $this->assertSame('berita', $results[0]['type']);
        $this->assertStringContainsString('jadwal-ukt-2026', $results[0]['url']);
    }

    public function test_draft_article_is_not_found(): void
    {
        Article::create([
            'title' => 'Draft UKT',
            'slug' => 'draft-ukt',
            'excerpt' => 'Draft',
            'content' => 'Draft',
            'category' => 'Akademik',
            'status' => 'draft',
            'author_name' => 'BEM',
        ]);

        $service = new PortalKnowledgeService;
        $results = $service->search('Draft UKT');

        $this->assertNotContains('Draft UKT', array_column($results, 'title'));
    }

    public function test_website_pages_are_searchable_and_include_static_portal_information(): void
    {
        $results = (new PortalKnowledgeService)->search('Apa visi dan misi Portal BEM?');
        $genericWebsiteResults = (new PortalKnowledgeService)->search('Informasi website');

        $aboutPage = collect($results)->firstWhere('title', 'Tentang, visi, misi, dan nilai Portal BEM');

        $this->assertNotNull($aboutPage);
        $this->assertSame('halaman_website', $aboutPage['type']);
        $this->assertStringContainsString('inklusif', $aboutPage['snippet']);
        $this->assertStringContainsString('/tentang', $aboutPage['url']);
        $this->assertNotEmpty($genericWebsiteResults);
    }

    public function test_multiword_website_question_finds_matching_published_article(): void
    {
        Article::create([
            'title' => 'Bantuan Biaya Pendidikan Mahasiswa',
            'slug' => 'bantuan-biaya-pendidikan',
            'excerpt' => 'Panduan pengajuan bantuan biaya pendidikan.',
            'content' => 'Mahasiswa dapat meminta pendampingan untuk keringanan biaya kuliah melalui posko advokasi.',
            'category' => 'Akademik',
            'status' => 'published',
            'published_at' => now()->subDay(),
            'author_name' => 'BEM',
        ]);

        $results = (new PortalKnowledgeService)->search('Bagaimana cara mendapat bantuan biaya kuliah?');

        $article = collect($results)->firstWhere('title', 'Bantuan Biaya Pendidikan Mahasiswa');

        $this->assertNotNull($article);
        $this->assertSame('berita', $article['type']);
    }

    public function test_contact_page_source_marks_simulated_contact_details(): void
    {
        $results = (new PortalKnowledgeService)->search('Di mana alamat sekretariat BEM dan jam bukanya?');

        $contactPage = collect($results)->firstWhere('title', 'Kontak dan sekretariat BEM');

        $this->assertNotNull($contactPage);
        $this->assertStringContainsString('simulasi pengembangan', $contactPage['snippet']);
        $this->assertStringContainsString('Senin–Jumat', $contactPage['snippet']);
    }

    public function test_empty_query_returns_empty(): void
    {
        $service = new PortalKnowledgeService;
        $this->assertSame([], $service->search('   '));
    }
}
