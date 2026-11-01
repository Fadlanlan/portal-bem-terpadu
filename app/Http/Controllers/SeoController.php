<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Event;
use App\Models\WorkProgram;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Schema;

/**
 * Pengelolaan metadata perayap (crawler) untuk SEO halaman publik.
 *
 * Catatan keamanan: sitemap hanya menampilkan URL publik dan konten yang
 * sudah dipublikasikan (bukan draf). Area admin, kanal pelacakan aspirasi,
 * dan formulir pengiriman aspirasi sengaja dikecualikan dari indeks.
 */
class SeoController extends Controller
{
    /**
     * Sajikan berkas robots.txt dinamis menggunakan APP_URL yang aktif.
     */
    public function robots(): Response
    {
        $sitemapUrl = rtrim((string) config('app.url', 'http://localhost'), '/') . '/sitemap.xml';

        $lines = [
            'User-agent: *',
            'Disallow: /admin',
            'Disallow: /aspirasi/kirim',
            'Disallow: /aspirasi/lacak',
            'Disallow: /aspirasi/sukses',
            '',
            'Sitemap: ' . $sitemapUrl,
        ];

        return response(implode("\n", $lines), 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Cache-Control' => 'no-cache',
        ]);
    }

    /**
     * Hasilkan sitemap XML yang hanya berisi URL publik yang valid.
     *
     * @return Response
     */
    public function sitemap(): Response
    {
        $urls = [
            ['loc' => route('home'), 'priority' => '1.0', 'changefreq' => 'daily'],
            ['loc' => route('news.index'), 'priority' => '0.9', 'changefreq' => 'daily'],
            ['loc' => route('programs.index'), 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['loc' => route('events.index'), 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['loc' => route('organizations.index'), 'priority' => '0.6', 'changefreq' => 'monthly'],
            ['loc' => route('about'), 'priority' => '0.5', 'changefreq' => 'monthly'],
            ['loc' => route('contact'), 'priority' => '0.5', 'changefreq' => 'monthly'],
            ['loc' => route('faq'), 'priority' => '0.4', 'changefreq' => 'monthly'],
            ['loc' => route('aspirations.info'), 'priority' => '0.7', 'changefreq' => 'monthly'],
        ];

        // URL detail berita yang sudah dipublikasikan (draf dikecualikan oleh scopePublished)
        if (Schema::hasTable('articles')) {
            foreach (Article::published()->orderBy('published_at', 'desc')->get() as $article) {
                $urls[] = [
                    'loc' => route('news.show', $article->slug),
                    'priority' => '0.7',
                    'changefreq' => 'monthly',
                    'lastmod' => $article->published_at?->toDateString(),
                ];
            }
        }

        // URL detail program kerja yang sudah dipublikasikan
        if (Schema::hasTable('work_programs')) {
            foreach (WorkProgram::published()->orderBy('created_at', 'desc')->get() as $program) {
                $urls[] = [
                    'loc' => route('programs.show', $program->slug),
                    'priority' => '0.6',
                    'changefreq' => 'monthly',
                ];
            }
        }

        // URL detail agenda yang sudah dipublikasikan
        if (Schema::hasTable('events')) {
            foreach (Event::published()->orderBy('started_at', 'desc')->get() as $event) {
                $urls[] = [
                    'loc' => route('events.show', $event->slug),
                    'priority' => '0.6',
                    'changefreq' => 'monthly',
                    'lastmod' => $event->started_at?->toDateString(),
                ];
            }
        }

        return response()
            ->view('seo.sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
