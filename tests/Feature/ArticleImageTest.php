<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ArticleImageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Helper membuat pengguna pengelola dengan peran tertentu.
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
     * Helper membuat file fake gambar valid tanpa ketergantungan ekstensi GD.
     */
    private function createFakeImage(string $filename = 'test.png', int $kilobytes = 10): UploadedFile
    {
        // 1x1 valid PNG binary byte stream
        $basePng = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');

        if ($kilobytes * 1024 > strlen($basePng)) {
            $content = $basePng . str_repeat('X', ($kilobytes * 1024) - strlen($basePng));
        } else {
            $content = $basePng;
        }

        return UploadedFile::fake()->createWithContent($filename, $content);
    }

    /**
     * 1. Editor dapat membuat warta berita baru dengan gambar valid.
     */
    public function test_editor_can_create_article_with_valid_image(): void
    {
        Storage::fake('public');
        $editor = $this->createUser(UserRole::Editor);

        $file = $this->createFakeImage('kegiatan-kampus.png', 50);

        $response = $this->actingAs($editor)->post(route('admin.articles.store'), [
            'title' => 'Pelatihan Jurnalistik Mahasiswa 2026',
            'category' => 'Pelatihan',
            'excerpt' => 'Ringkasan kegiatan pelatihan jurnalistik dan media kampus.',
            'content' => 'Isi lengkap dokumentasi pelatihan jurnalistik tingkat universitas.',
            'status' => 'published',
            'image' => $file,
        ]);

        $response->assertRedirect(route('admin.articles.index'));

        $article = Article::where('title', 'Pelatihan Jurnalistik Mahasiswa 2026')->first();
        $this->assertNotNull($article);
        $this->assertNotNull($article->image);

        // Memastikan file tersimpan di disk public dalam direktori articles
        Storage::disk('public')->assertExists($article->image);
        $this->assertStringStartsWith('articles/', $article->image);
    }

    /**
     * 2. Path yang disimpan di database adalah path relatif, bukan path absolut Windows atau konten biner.
     */
    public function test_relative_path_is_stored_in_database(): void
    {
        Storage::fake('public');
        $editor = $this->createUser(UserRole::Editor);

        $file = $this->createFakeImage('warta-resmi.png', 50);

        $this->actingAs($editor)->post(route('admin.articles.store'), [
            'title' => 'Sosialisasi Anggaran Ormawa Terpadu',
            'category' => 'Pengumuman',
            'excerpt' => 'Sosialisasi transparansi alokasi dana kegiatan ormawa.',
            'content' => 'Rincian pemaparan anggaran belanja organisasi mahasiswa.',
            'status' => 'published',
            'image' => $file,
        ]);

        $article = Article::where('title', 'Sosialisasi Anggaran Ormawa Terpadu')->first();
        $this->assertNotNull($article);

        // Path relatif murni, tidak mengandung drive C: atau path absolut storage
        $this->assertStringStartsNotWith('C:', $article->image);
        $this->assertStringStartsNotWith('storage/', $article->image);
        $this->assertStringStartsWith('articles/', $article->image);
        $this->assertDoesNotMatchRegularExpression('/[\r\n\t]/', $article->image);
    }

    /**
     * 3. Sistem menolak berkas yang bukan gambar yang diizinkan (misalnya berkas PDF atau teks).
     */
    public function test_non_image_file_is_rejected(): void
    {
        Storage::fake('public');
        $editor = $this->createUser(UserRole::Editor);

        $invalidFile = UploadedFile::fake()->create('dokumen.pdf', 300, 'application/pdf');

        $response = $this->actingAs($editor)->from(route('admin.articles.create'))->post(route('admin.articles.store'), [
            'title' => 'Judul Artikel Tidak Valid',
            'category' => 'Akademik',
            'excerpt' => 'Ringkasan artikel pengujian ekstensi berkas.',
            'content' => 'Konten artikel pengujian ekstensi berkas yang tidak sesuai.',
            'status' => 'draft',
            'image' => $invalidFile,
        ]);

        $response->assertRedirect(route('admin.articles.create'));
        $response->assertSessionHasErrors(['image']);
        $this->assertDatabaseMissing('articles', ['title' => 'Judul Artikel Tidak Valid']);
    }

    /**
     * 4. Sistem menolak berkas gambar yang melebihi batas ukuran 2MB.
     */
    public function test_oversized_image_is_rejected(): void
    {
        Storage::fake('public');
        $editor = $this->createUser(UserRole::Editor);

        // Buat file fake melebihi 2048 KB (misal 2.6 MB)
        $oversizedFile = $this->createFakeImage('foto-sangat-besar.png', 2600);

        $response = $this->actingAs($editor)->from(route('admin.articles.create'))->post(route('admin.articles.store'), [
            'title' => 'Artikel Gambar Terlalu Besar',
            'category' => 'Kegiatan',
            'excerpt' => 'Ringkasan pengujian batas maksimal ukuran gambar.',
            'content' => 'Konten pengujian batas maksimal ukuran unggahan berkas.',
            'status' => 'draft',
            'image' => $oversizedFile,
        ]);

        $response->assertRedirect(route('admin.articles.create'));
        $response->assertSessionHasErrors(['image']);
        $this->assertDatabaseMissing('articles', ['title' => 'Artikel Gambar Terlalu Besar']);
    }

    /**
     * 5. Mempertahankan gambar lama saat artikel diedit tanpa mengunggah gambar baru.
     */
    public function test_old_image_is_retained_when_updating_without_new_image(): void
    {
        Storage::fake('public');
        $editor = $this->createUser(UserRole::Editor);

        $originalPath = 'articles/gambar-lama.jpg';
        Storage::disk('public')->put($originalPath, 'isi-gambar-lama');

        $article = Article::create([
            'title' => 'Judul Awal Artikel',
            'slug' => 'judul-awal-artikel',
            'excerpt' => 'Ringkasan awal artikel.',
            'content' => 'Konten awal artikel sebelum disunting.',
            'category' => 'Kegiatan',
            'status' => 'published',
            'published_at' => now(),
            'image' => $originalPath,
        ]);

        $response = $this->actingAs($editor)->put(route('admin.articles.update', $article), [
            'title' => 'Judul Diperbarui Tanpa Ganti Gambar',
            'category' => 'Kegiatan',
            'excerpt' => 'Ringkasan baru artikel.',
            'content' => 'Konten baru artikel setelah disunting.',
            'status' => 'published',
            // image sengaja tidak disertakan
        ]);

        $response->assertRedirect(route('admin.articles.index'));

        $article->refresh();
        $this->assertEquals('Judul Diperbarui Tanpa Ganti Gambar', $article->title);
        $this->assertEquals($originalPath, $article->image);
        Storage::disk('public')->assertExists($originalPath);
    }

    /**
     * 6. Mengganti gambar dengan benar: file baru disimpan dan file lama dihapus dari storage.
     */
    public function test_image_replacement_stores_new_file_and_deletes_old_file(): void
    {
        Storage::fake('public');
        $editor = $this->createUser(UserRole::Editor);

        $oldPath = 'articles/gambar-akan-diganti.png';
        Storage::disk('public')->put($oldPath, 'konten-gambar-sebelumnya');

        $article = Article::create([
            'title' => 'Artikel Yang Akan Diganti Gambarnya',
            'slug' => 'artikel-yang-akan-diganti-gambarnya',
            'excerpt' => 'Ringkasan artikel.',
            'content' => 'Konten artikel.',
            'category' => 'Pengumuman',
            'status' => 'published',
            'published_at' => now(),
            'image' => $oldPath,
        ]);

        $newFile = $this->createFakeImage('gambar-pengganti-baru.png', 60);

        $response = $this->actingAs($editor)->put(route('admin.articles.update', $article), [
            'title' => 'Artikel Yang Telah Diganti Gambarnya',
            'category' => 'Pengumuman',
            'excerpt' => 'Ringkasan artikel terbarukan.',
            'content' => 'Konten artikel terbarukan.',
            'status' => 'published',
            'image' => $newFile,
        ]);

        $response->assertRedirect(route('admin.articles.index'));

        $article->refresh();
        $this->assertNotEquals($oldPath, $article->image);
        $this->assertNotNull($article->image);

        // File baru ada di disk
        Storage::disk('public')->assertExists($article->image);
        // File lama telah dihapus secara bersih
        Storage::disk('public')->assertMissing($oldPath);
    }

    /**
     * 7. Jangan menghapus file lama jika masih digunakan oleh artikel lain.
     */
    public function test_shared_image_is_not_deleted_if_used_by_another_article(): void
    {
        Storage::fake('public');
        $editor = $this->createUser(UserRole::Editor);

        $sharedPath = 'articles/gambar-bersama.jpg';
        Storage::disk('public')->put($sharedPath, 'konten-gambar-bersama');

        $article1 = Article::create([
            'title' => 'Artikel Berita Pertama',
            'slug' => 'artikel-berita-pertama',
            'excerpt' => 'Ringkasan artikel 1.',
            'content' => 'Konten artikel 1.',
            'category' => 'Kegiatan',
            'status' => 'published',
            'published_at' => now(),
            'image' => $sharedPath,
        ]);

        $article2 = Article::create([
            'title' => 'Artikel Berita Kedua',
            'slug' => 'artikel-berita-kedua',
            'excerpt' => 'Ringkasan artikel 2.',
            'content' => 'Konten artikel 2.',
            'category' => 'Kegiatan',
            'status' => 'published',
            'published_at' => now(),
            'image' => $sharedPath,
        ]);

        // Ganti gambar pada artikel 1
        $replacementFile = $this->createFakeImage('gambar-khusus-artikel-1.png', 40);
        $this->actingAs($editor)->put(route('admin.articles.update', $article1), [
            'title' => 'Artikel Berita Pertama Ganti Gambar',
            'category' => 'Kegiatan',
            'excerpt' => 'Ringkasan artikel 1.',
            'content' => 'Konten artikel 1.',
            'status' => 'published',
            'image' => $replacementFile,
        ]);

        $article1->refresh();
        $this->assertNotEquals($sharedPath, $article1->image);

        // File lama sharedPath TIDAK boleh dihapus karena artikel 2 masih memakainya
        Storage::disk('public')->assertExists($sharedPath);
    }

    /**
     * 8. Artikel dengan image = null atau file yang hilang mengembalikan URL placeholder lokal SVG.
     */
    public function test_article_with_null_or_missing_image_returns_placeholder_url(): void
    {
        Storage::fake('public');

        // Artikel dengan image = null
        $articleNull = Article::create([
            'title' => 'Artikel Tanpa Gambar',
            'slug' => 'artikel-tanpa-gambar',
            'excerpt' => 'Ringkasan artikel.',
            'content' => 'Konten lengkap artikel.',
            'category' => 'Akademik',
            'status' => 'published',
            'published_at' => now(),
            'image' => null,
        ]);

        $this->assertStringContainsString('images/news-placeholder.svg', $articleNull->image_url);
        $this->assertFalse($articleNull->has_custom_image);

        // Artikel dengan path di DB tetapi file hilang dari disk
        $articleMissing = Article::create([
            'title' => 'Artikel Berkas Hilang',
            'slug' => 'artikel-berkas-hilang',
            'excerpt' => 'Ringkasan artikel.',
            'content' => 'Konten lengkap artikel.',
            'category' => 'Akademik',
            'status' => 'published',
            'published_at' => now(),
            'image' => 'articles/file-tidak-ada.jpg',
        ]);

        $this->assertStringContainsString('images/news-placeholder.svg', $articleMissing->image_url);
        $this->assertFalse($articleMissing->has_custom_image);
    }

    /**
     * 9. Halaman daftar warta berita publik menampilkan elemen gambar dengan aman.
     */
    public function test_public_news_list_renders_images_and_placeholders(): void
    {
        Storage::fake('public');

        $withImage = Article::create([
            'title' => 'Warta Berita Dengan Foto Sampul',
            'slug' => 'warta-berita-dengan-foto-sampul',
            'excerpt' => 'Ringkasan warta berita bergambar.',
            'content' => 'Konten lengkap warta berita.',
            'category' => 'Kegiatan',
            'status' => 'published',
            'published_at' => now(),
            'image' => 'articles/foto-sampul.jpg',
        ]);
        Storage::disk('public')->put('articles/foto-sampul.jpg', 'isi-foto');

        $withoutImage = Article::create([
            'title' => 'Warta Berita Tanpa Foto Sampul',
            'slug' => 'warta-berita-tanpa-foto-sampul',
            'excerpt' => 'Ringkasan warta berita tanpa gambar.',
            'content' => 'Konten lengkap warta berita.',
            'category' => 'Pengumuman',
            'status' => 'published',
            'published_at' => now(),
            'image' => null,
        ]);

        $response = $this->get(route('news.index'));

        $response->assertStatus(200);
        $response->assertSee('Warta Berita Dengan Foto Sampul');
        $response->assertSee('Warta Berita Tanpa Foto Sampul');
        // Gambar artikel pertama dirender
        $response->assertSee($withImage->image_url, false);
        // Placeholder dirender untuk artikel kedua
        $response->assertSee('news-placeholder.svg');
    }

    /**
     * 10. Halaman rincian berita publik menampilkan gambar sampul dengan atribut alt yang sesuai.
     */
    public function test_public_news_detail_renders_cover_image_properly(): void
    {
        Storage::fake('public');

        $article = Article::create([
            'title' => 'Inagurasi Kepengurusan BEM Kabinet Baru',
            'slug' => 'inagurasi-kepengurusan-bem-kabinet-baru',
            'excerpt' => 'Pelantikan resmi jajaran menteri dan biro BEM.',
            'content' => "Pelantikan resmi berlangsung khidmat di aula rektorat.\n\nSegenap pengurus bertekad mengawal aspirasi mahasiswa.",
            'category' => 'Kegiatan',
            'status' => 'published',
            'published_at' => now(),
            'image' => 'articles/inagurasi.webp',
        ]);
        Storage::disk('public')->put('articles/inagurasi.webp', 'isi-foto-inagurasi');

        $response = $this->get(route('news.show', $article->slug));

        $response->assertStatus(200);
        $response->assertSee($article->title);
        $response->assertSee($article->image_url, false);
        $response->assertSee('alt="' . $article->title . '"', false);
    }

    /**
     * 11. Peran yang tidak berhak (misalnya advokasi) tidak dapat mengunggah atau membuat berita.
     *     Pengguna tamu diarahkan ke halaman login.
     */
    public function test_unauthorized_roles_cannot_upload_or_create_articles(): void
    {
        Storage::fake('public');
        $advocacy = $this->createUser(UserRole::Advocacy);

        $file = $this->createFakeImage('percobaan-ilegal.png', 30);

        // Advocacy ditolak dengan 403 Forbidden
        $responseAdvocacy = $this->actingAs($advocacy)->post(route('admin.articles.store'), [
            'title' => 'Upaya Publikasi Oleh Advokasi',
            'category' => 'Advokasi & Aspirasi',
            'excerpt' => 'Ringkasan artikel.',
            'content' => 'Konten artikel.',
            'status' => 'published',
            'image' => $file,
        ]);
        $responseAdvocacy->assertStatus(403);

        // Guest ditolak akses (tidak dapat melewati middleware admin.auth)
        $guestFile = $this->createFakeImage('percobaan-tamu.png', 20);
        $guestResponse = $this->post(route('admin.articles.store'), [
            'title' => 'Upaya Publikasi Oleh Anonim',
            'category' => 'Pengumuman',
            'excerpt' => 'Ringkasan artikel.',
            'content' => 'Konten artikel.',
            'status' => 'published',
            'image' => $guestFile,
        ]);
        // Guest mendapat redirect (302) atau forbidden (403) tergantung urutan middleware
        $this->assertTrue(
            in_array($guestResponse->getStatusCode(), [302, 403, 419]),
            'Tamu seharusnya ditolak akses ke endpoint admin (302/403/419), mendapat: ' . $guestResponse->getStatusCode()
        );
        $this->assertDatabaseMissing('articles', ['title' => 'Upaya Publikasi Oleh Anonim']);
    }

    /**
     * 12. Berita berstatus konsep (draft) yang memiliki gambar tetap tidak dapat diakses publik (404).
     */
    public function test_draft_article_with_image_returns_404_on_public_route(): void
    {
        Storage::fake('public');

        $draft = Article::create([
            'title' => 'Rancangan Dokumen Rahasia Warta',
            'slug' => 'rancangan-dokumen-rahasia-warta',
            'excerpt' => 'Ringkasan artikel draft.',
            'content' => 'Isi draft yang belum siap dipublikasikan.',
            'category' => 'Pengumuman',
            'status' => 'draft',
            'published_at' => null,
            'image' => 'articles/draft-cover.png',
        ]);
        Storage::disk('public')->put('articles/draft-cover.png', 'draft-content');

        $response = $this->get(route('news.show', $draft->slug));
        $response->assertStatus(404);
    }
}
