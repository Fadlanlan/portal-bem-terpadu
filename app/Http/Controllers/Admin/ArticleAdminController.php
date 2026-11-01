<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminAuditLog;
use App\Models\Article;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ArticleAdminController extends Controller
{
    /**
     * Tampilkan daftar warta dan berita dalam dashboard.
     */
    public function index(Request $request): View
    {
        $query = Article::query()->latest('created_at');

        if ($search = trim((string) $request->input('q', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            if (in_array($status, ['draft', 'published'], true)) {
                $query->where('status', $status);
            }
        }

        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }

        $articles = $query->paginate(15)->withQueryString();
        $categories = Article::select('category')->distinct()->pluck('category')->filter()->values();

        return view('admin.articles.index', [
            'articles' => $articles,
            'categories' => $categories,
            'filters' => [
                'q' => $request->input('q'),
                'status' => $request->input('status'),
                'category' => $request->input('category'),
            ],
        ]);
    }

    /**
     * Tampilkan formulir pembuatan berita baru.
     */
    public function create(): View
    {
        return view('admin.articles.create');
    }

    /**
     * Simpan berita baru ke basis data.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'excerpt' => ['required', 'string', 'max:1000'],
            'content' => ['required', 'string'],
            'category' => ['required', 'string', 'max:64'],
            'author_name' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'string', 'in:draft,published'],
            'read_time' => ['nullable', 'string', 'max:32'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ], [
            'title.required' => 'Judul berita wajib diisi.',
            'title.max' => 'Judul berita maksimal 200 karakter.',
            'excerpt.required' => 'Ringkasan berita wajib diisi.',
            'content.required' => 'Isi lengkap berita wajib diisi.',
            'category.required' => 'Kategori berita wajib diisi.',
            'status.required' => 'Pilih status publikasi berita.',
            'image.image' => 'Berkas gambar harus berupa file visual yang valid.',
            'image.mimes' => 'Format gambar yang diizinkan hanya JPEG, PNG, JPG, atau WebP.',
            'image.max' => 'Ukuran berkas gambar maksimal 2MB.',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            if ($imageFile->isValid()) {
                $imagePath = $imageFile->store('articles', 'public');
            }
        }

        $slug = Article::generateUniqueSlug($validated['title']);
        $publishedAt = $validated['status'] === 'published' ? now() : null;
        $authorName = ! empty($validated['author_name'])
            ? $validated['author_name']
            : ($request->input('author') ?: ($request->user()?->name ?? 'Admin Portal'));

        $article = Article::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'excerpt' => $validated['excerpt'],
            'content' => $validated['content'],
            'category' => $validated['category'],
            'author_name' => $authorName,
            'status' => $validated['status'],
            'published_at' => $publishedAt,
            'read_time' => $validated['read_time'] ?? null,
            'image' => $imagePath,
        ]);

        AdminAuditLog::record(
            'article.create',
            'Warta berita "' . $article->title . '" berhasil dibuat.',
            $article,
            ['status' => $article->status, 'category' => $article->category]
        );

        return redirect()
            ->route('admin.articles.index')
            ->with('success', 'Warta berita "' . $validated['title'] . '" berhasil dibuat.');
    }

    /**
     * Tampilkan formulir penyuntingan berita.
     */
    public function edit(Article $article): View
    {
        return view('admin.articles.edit', [
            'article' => $article,
        ]);
    }

    /**
     * Perbarui data berita dalam basis data.
     */
    public function update(Request $request, Article $article): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'excerpt' => ['required', 'string', 'max:1000'],
            'content' => ['required', 'string'],
            'category' => ['required', 'string', 'max:64'],
            'author_name' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'string', 'in:draft,published'],
            'read_time' => ['nullable', 'string', 'max:32'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ], [
            'title.required' => 'Judul berita wajib diisi.',
            'title.max' => 'Judul berita maksimal 200 karakter.',
            'excerpt.required' => 'Ringkasan berita wajib diisi.',
            'content.required' => 'Isi lengkap berita wajib diisi.',
            'category.required' => 'Kategori berita wajib diisi.',
            'status.required' => 'Pilih status publikasi berita.',
            'image.image' => 'Berkas gambar harus berupa file visual yang valid.',
            'image.mimes' => 'Format gambar yang diizinkan hanya JPEG, PNG, JPG, atau WebP.',
            'image.max' => 'Ukuran berkas gambar maksimal 2MB.',
        ]);

        $imagePath = $article->image;
        if ($request->hasFile('image')) {
            $newImage = $request->file('image');
            if ($newImage->isValid()) {
                $storedPath = $newImage->store('articles', 'public');
                if ($storedPath) {
                    $oldImage = $article->image;
                    $imagePath = $storedPath;

                    // Hapus gambar lama hanya jika file baru sukses disimpan, path aman, dan tidak dipakai artikel lain
                    if (! empty($oldImage)
                        && Storage::disk('public')->exists($oldImage)
                        && ! str_contains($oldImage, '..')
                        && ! Article::where('id', '!=', $article->id)->where('image', $oldImage)->exists()
                    ) {
                        Storage::disk('public')->delete($oldImage);
                    }
                }
            }
        }

        $slug = $article->slug;
        if ($article->title !== $validated['title']) {
            $slug = Article::generateUniqueSlug($validated['title'], $article->id);
        }

        $publishedAt = $article->published_at;
        if ($validated['status'] === 'published' && ! $publishedAt) {
            $publishedAt = now();
        }

        $authorName = ! empty($validated['author_name'])
            ? $validated['author_name']
            : ($request->input('author') ?: $article->author_name);

        $article->update([
            'title' => $validated['title'],
            'slug' => $slug,
            'excerpt' => $validated['excerpt'],
            'content' => $validated['content'],
            'category' => $validated['category'],
            'author_name' => $authorName,
            'status' => $validated['status'],
            'published_at' => $publishedAt,
            'read_time' => $validated['read_time'] ?? $article->read_time,
            'image' => $imagePath,
        ]);

        AdminAuditLog::record(
            'article.update',
            'Warta berita "' . $article->title . '" berhasil diperbarui.',
            $article,
            ['status' => $article->status, 'category' => $article->category]
        );

        return redirect()
            ->route('admin.articles.index')
            ->with('success', 'Warta berita "' . $article->title . '" berhasil diperbarui.');
    }

    /**
     * Hapus artikel berita dari basis data.
     */
    public function destroy(Article $article): RedirectResponse
    {
        $oldImage = $article->image;
        if (! empty($oldImage)
            && Storage::disk('public')->exists($oldImage)
            && ! str_contains($oldImage, '..')
            && ! Article::where('id', '!=', $article->id)->where('image', $oldImage)->exists()
        ) {
            Storage::disk('public')->delete($oldImage);
        }

        $id = $article->id;
        $title = $article->title;
        $article->delete();

        AdminAuditLog::record(
            'article.delete',
            'Warta berita "' . $title . '" berhasil dihapus dari sistem.',
            null,
            ['article_id' => $id, 'title' => $title]
        );

        return redirect()
            ->route('admin.articles.index')
            ->with('success', 'Warta berita "' . $title . '" berhasil dihapus.');
    }
}
