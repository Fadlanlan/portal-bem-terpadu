<?php

namespace App\Services\Chat;

use App\Models\Article;
use App\Models\BemMember;
use App\Models\ChatFaq;
use App\Models\Event;
use App\Models\Organization;
use App\Models\WorkProgram;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class PortalKnowledgeService
{
    /**
     * @var list<string>
     */
    private const STOP_WORDS = [
        'ada', 'apa', 'apakah', 'bagaimana', 'bagi', 'dan', 'dari', 'di', 'dimana', 'ini',
        'itu', 'jelaskan', 'ke', 'kemana', 'kapan', 'karena', 'kasih', 'kepada', 'mengenai',
        'menurut', 'mohon', 'pada', 'saya', 'sebutkan', 'tentang', 'tolong', 'untuk', 'website',
        'yang', 'web', 'info', 'informasi', 'about', 'and', 'are', 'can', 'do', 'for', 'how',
        'is', 'me', 'of', 'please', 'tell', 'the', 'this', 'what', 'where', 'who', 'you',
    ];

    /**
     * @return list<array{type: string, title: string, snippet: string, url: string, published_at?: string|null}>
     */
    public function search(string $query, int $limit = 8): array
    {
        $query = trim($query);
        if ($query === '') {
            return [];
        }

        $terms = $this->terms($query);
        $sources = array_merge(
            $this->searchWebsitePages($terms),
            $this->searchArticles($terms),
            $this->searchWorkPrograms($terms),
            $this->searchEvents($terms),
            $this->searchOrganizations($terms),
            $this->searchBemMembers($terms),
            $this->searchFaqs($terms),
        );

        $rankedSources = [];
        foreach ($sources as $source) {
            $key = $source['url'].'|'.$source['title'];
            $score = $this->score($query, $terms, $source);

            if (! isset($rankedSources[$key]) || $rankedSources[$key]['_score'] < $score) {
                $source['_score'] = $score;
                $rankedSources[$key] = $source;
            }
        }

        $rankedSources = array_values($rankedSources);
        usort($rankedSources, fn (array $left, array $right): int => $right['_score'] <=> $left['_score']);

        return array_map(function (array $source): array {
            unset($source['_score']);

            return $source;
        }, array_slice($rankedSources, 0, max(0, $limit)));
    }

    /**
     * @return list<string>
     */
    private function terms(string $query): array
    {
        $words = preg_split('/[^\pL\pN]+/u', mb_strtolower($query), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $terms = array_values(array_unique(array_filter(
            $words,
            fn (string $word): bool => mb_strlen($word) > 1 && ! in_array($word, self::STOP_WORDS, true),
        )));

        return $terms === [] ? ['portal'] : $terms;
    }

    /**
     * @param  list<string>  $terms
     * @param  array{title: string, snippet: string}  $source
     */
    private function score(string $query, array $terms, array $source): int
    {
        $title = mb_strtolower($source['title']);
        $snippet = mb_strtolower($source['snippet']);
        $score = str_contains($title.' '.$snippet, mb_strtolower($query)) ? 10 : 0;

        foreach ($terms as $term) {
            if (str_contains($title, $term)) {
                $score += 3;
            } elseif (str_contains($snippet, $term)) {
                $score++;
            }
        }

        return $score;
    }

    /**
     * @param  list<string>  $columns
     * @param  list<string>  $terms
     */
    private function addTermFilters(Builder $query, array $columns, array $terms): void
    {
        $query->where(function (Builder $nestedQuery) use ($columns, $terms): void {
            foreach ($terms as $term) {
                foreach ($columns as $column) {
                    $nestedQuery->orWhere($column, 'like', $this->like($term));
                }
            }
        });
    }

    private function like(string $term): string
    {
        return '%'.str_replace(['%', '_'], ['\\%', '\\_'], $term).'%';
    }

    /**
     * @param  list<string>  $terms
     * @return list<array{type: string, title: string, snippet: string, url: string}>
     */
    private function searchWebsitePages(array $terms): array
    {
        $pages = [
            [
                'type' => 'halaman_website',
                'title' => 'Beranda Portal BEM Terpadu',
                'snippet' => 'Portal BEM Terpadu memuat berita mahasiswa, program kerja, agenda, organisasi kemahasiswaan, struktur pengurus, FAQ, dan kanal aspirasi. Catatan di situs menyatakan data berita, acara, program, nama pengurus, dan kontak operasional tertentu merupakan data simulasi pengembangan, bukan informasi resmi definitif.',
                'url' => route('home'),
            ],
            [
                'type' => 'halaman_website',
                'title' => 'Tentang, visi, misi, dan nilai Portal BEM',
                'snippet' => 'Visi kelembagaan: terwujudnya iklim kemahasiswaan yang kritis, inklusif, transparan, dan berdaya saing melalui tata kelola organisasi modern serta advokasi mahasiswa yang terpercaya dan terukur. Misi portal meliputi kanal aspirasi transparan dan terlacak, transparansi program kerja dan keuangan organisasi, serta kalender bersama BEM, MAM, DPM, HIMA, HMPS, dan UKM. Empat pilar: transparansi dan akuntabilitas; advokasi terukur; sinergi dan kolaborasi; inovasi digital berkelanjutan.',
                'url' => route('about'),
            ],
            [
                'type' => 'halaman_website',
                'title' => 'Kontak dan sekretariat BEM',
                'snippet' => 'Halaman kontak menampilkan lokasi sekretariat di Gedung Pusat Kegiatan Mahasiswa (PKM) lantai 1, Kompleks Perguruan Tinggi Terpadu, dengan jam layanan Senin–Jumat pukul 09.00–16.30 WIB. Email sekretariat@bem.kampus.ac.id ditandai sebagai alamat simulasi pengembangan. Akun Instagram @bemkampus_official dan Kanal Warta BEM juga ditampilkan sebagai kanal informasi situs; konfirmasikan kontak operasional sebelum digunakan.',
                'url' => route('contact'),
            ],
            [
                'type' => 'halaman_website',
                'title' => 'FAQ Portal BEM Terpadu',
                'snippet' => 'FAQ situs menjelaskan tujuan portal sebagai pusat informasi, transparansi program kerja, kalender kegiatan ormawa, dan kanal aspirasi. Aspirasi adalah gagasan/usulan; pengaduan melaporkan kendala faktual; permohonan informasi meminta klarifikasi regulasi. Aspirasi dapat disampaikan anonim, tetapi kasus investigasi lebih mudah ditindaklanjuti dengan identitas terverifikasi. Pelacakan aspirasi menggunakan nomor tiket dan status: diterima, diverifikasi, diproses, menunggu tindak lanjut, lalu selesai atau ditolak; data pribadi tidak ditampilkan ke publik. Pengajuan agenda UKM/HIMA direncanakan melalui akun editor terverifikasi. FAQ menyatakan sebagian data situs merupakan demonstrasi/simulasi dan belum definitif.',
                'url' => route('faq'),
            ],
            [
                'type' => 'halaman_website',
                'title' => 'Panduan kanal aspirasi dan pelacakan',
                'snippet' => 'Situs menyediakan formulir untuk mengirim aspirasi, pengaduan fasilitas atau akademik, dan permohonan informasi. Pelapor dapat memilih identitas anonim atau terbuka. Setelah pengiriman, sistem memberikan nomor tiket untuk melacak status; data nama dan email pelapor tidak ditampilkan secara publik. Untuk kendala pribadi, identitas terverifikasi disarankan agar tim advokasi dapat menindaklanjuti.',
                'url' => route('aspirations.info'),
            ],
            [
                'type' => 'halaman_website',
                'title' => 'Organisasi kemahasiswaan di Portal BEM',
                'snippet' => 'Halaman organisasi Portal BEM mengenalkan BEM, MAM, DPM, HIMA, HMPS, dan UKM. BEM menjalankan fungsi eksekutif dan koordinasi program mahasiswa; MAM dan DPM menjalankan fungsi perwakilan, legislasi, atau pengawasan sesuai penjelasan situs; HIMA dan HMPS mewadahi mahasiswa tingkat fakultas dan program studi; UKM mengembangkan minat, bakat, seni, olahraga, riset, dan pengabdian.',
                'url' => route('organizations.index'),
            ],
        ];

        return array_values(array_filter($pages, function (array $page) use ($terms): bool {
            $haystack = mb_strtolower($page['title'].' '.$page['snippet']);

            foreach ($terms as $term) {
                if (str_contains($haystack, $term)) {
                    return true;
                }
            }

            return false;
        }));
    }

    /**
     * @param  list<string>  $terms
     * @return list<array{type: string, title: string, snippet: string, url: string, published_at: string|null}>
     */
    private function searchArticles(array $terms): array
    {
        $query = Article::published();
        $this->addTermFilters($query, ['title', 'excerpt', 'content'], $terms);
        $items = $query->latest('published_at')->limit(12)->get();

        $result = [];
        foreach ($items as $item) {
            $result[] = [
                'type' => 'berita',
                'title' => $item->title,
                'snippet' => Str::limit(strip_tags(implode(' ', array_filter([
                    (string) $item->excerpt,
                    (string) $item->content,
                    (string) $item->category,
                ]))), 700),
                'url' => route('news.show', ['slug' => $item->slug]),
                'published_at' => optional($item->published_at)->toDateString(),
            ];
        }

        return $result;
    }

    /**
     * @param  list<string>  $terms
     * @return list<array{type: string, title: string, snippet: string, url: string, published_at: string|null}>
     */
    private function searchWorkPrograms(array $terms): array
    {
        $query = WorkProgram::published();
        $this->addTermFilters($query, ['name', 'description', 'objectives', 'department', 'organization'], $terms);
        $items = $query->limit(12)->get();

        $result = [];
        foreach ($items as $item) {
            $result[] = [
                'type' => 'program_kerja',
                'title' => $item->name,
                'snippet' => Str::limit(strip_tags(implode(' ', array_filter([
                    (string) $item->description,
                    (string) $item->objectives,
                    (string) $item->period,
                    (string) $item->status,
                    implode(' ', (array) $item->targets),
                    (string) $item->indicators,
                    (string) $item->timeline,
                    (string) $item->pic,
                ]))), 700),
                'url' => route('programs.show', ['slug' => $item->slug]),
                'published_at' => optional($item->published_at)->toDateString(),
            ];
        }

        return $result;
    }

    /**
     * @param  list<string>  $terms
     * @return list<array{type: string, title: string, snippet: string, url: string, published_at: string|null}>
     */
    private function searchEvents(array $terms): array
    {
        $query = Event::published();
        $this->addTermFilters($query, ['title', 'summary', 'description', 'location'], $terms);
        $items = $query->limit(12)->get();

        $result = [];
        foreach ($items as $item) {
            $result[] = [
                'type' => 'agenda',
                'title' => $item->title,
                'snippet' => Str::limit(strip_tags(implode(' ', array_filter([
                    (string) ($item->summary ?: $item->description),
                    (string) $item->location,
                    optional($item->started_at)->format('d/m/Y H:i'),
                    optional($item->ended_at)->format('d/m/Y H:i'),
                    (string) $item->organizer,
                    $item->is_registration_open ? 'pendaftaran dibuka' : 'pendaftaran tidak dibuka',
                    (string) $item->registration_url,
                    implode(' ', (array) $item->speakers),
                    implode(' ', (array) $item->agenda_points),
                ]))), 700),
                'url' => route('events.show', ['slug' => $item->slug]),
                'published_at' => optional($item->started_at)->toDateString(),
            ];
        }

        return $result;
    }

    /**
     * @param  list<string>  $terms
     * @return list<array{type: string, title: string, snippet: string, url: string}>
     */
    private function searchOrganizations(array $terms): array
    {
        $query = Organization::published();
        $this->addTermFilters($query, ['name', 'description', 'role_type', 'level'], $terms);
        $items = $query->limit(12)->get();

        $result = [];
        foreach ($items as $item) {
            $result[] = [
                'type' => 'organisasi',
                'title' => $item->name,
                'snippet' => Str::limit(strip_tags(implode(' ', array_filter([
                    (string) $item->description,
                    (string) $item->role_type,
                    (string) $item->level,
                    implode(' ', (array) $item->functions),
                    implode(' ', (array) $item->structure_overview),
                ]))), 500),
                'url' => route('organizations.index'),
            ];
        }

        return $result;
    }

    /**
     * @param  list<string>  $terms
     * @return list<array{type: string, title: string, snippet: string, url: string}>
     */
    private function searchBemMembers(array $terms): array
    {
        $query = BemMember::published();
        $this->addTermFilters($query, ['name', 'position', 'department', 'description'], $terms);
        $items = $query->limit(12)->get();

        $result = [];
        foreach ($items as $item) {
            $result[] = [
                'type' => 'kepengurusan',
                'title' => $item->name.' - '.$item->position,
                'snippet' => Str::limit(strip_tags(implode(' ', array_filter([
                    (string) $item->department,
                    (string) $item->description,
                ]))), 500),
                'url' => route('bem.structure'),
            ];
        }

        return $result;
    }

    /**
     * @param  list<string>  $terms
     * @return list<array{type: string, title: string, snippet: string, url: string}>
     */
    private function searchFaqs(array $terms): array
    {
        $query = ChatFaq::published();
        $this->addTermFilters($query, ['question', 'answer'], $terms);
        $items = $query->limit(12)->get();

        $result = [];
        foreach ($items as $item) {
            $result[] = [
                'type' => 'faq',
                'title' => $item->question,
                'snippet' => Str::limit(strip_tags((string) $item->answer), 500),
                'url' => route('faq'),
            ];
        }

        return $result;
    }
}
