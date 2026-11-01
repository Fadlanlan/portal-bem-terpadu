<?php

use App\Http\Controllers\Admin\AspirationAdminController;
use App\Http\Controllers\Admin\ArticleAdminController;
use App\Http\Controllers\Admin\AuditLogAdminController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\BemMemberAdminController;
use App\Http\Controllers\Admin\ChatAdminController;
use App\Http\Controllers\Admin\ChatFaqAdminController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EventAdminController;
use App\Http\Controllers\Admin\OrganizationAdminController;
use App\Http\Controllers\Admin\ReportAdminController;
use App\Http\Controllers\Admin\SettingAdminController;
use App\Http\Controllers\Admin\UserAdminController;
use App\Http\Controllers\Admin\WorkProgramAdminController;
use App\Http\Controllers\AspirationController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\AspirationTrackingController;
use App\Http\Controllers\BemStructureController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\SeoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes — Portal BEM Terpadu
|--------------------------------------------------------------------------
| Seluruh rute antarmuka publik yang dapat diakses oleh mahasiswa, pengurus,
| dan sivitas akademika perguruan tinggi.
|
*/

// Halaman Beranda Utama
Route::get('/', [HomeController::class, 'index'])->name('home');

// SEO: robots.txt dan sitemap.xml dinamis (mengikuti APP_URL aktif)
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('robots');
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');

// Warta & Berita Mahasiswa
Route::prefix('berita')->name('news.')->group(function () {
    Route::get('/', [NewsController::class, 'index'])->name('index');
    Route::get('/{slug}', [NewsController::class, 'show'])->name('show');
});

// Program Kerja Kementerian BEM
Route::prefix('program-kerja')->name('programs.')->group(function () {
    Route::get('/', [ProgramController::class, 'index'])->name('index');
    Route::get('/{slug}', [ProgramController::class, 'show'])->name('show');
});

// Kalender Agenda & Kegiatan Kampus
Route::prefix('agenda')->name('events.')->group(function () {
    Route::get('/', [EventController::class, 'index'])->name('index');
    Route::get('/{slug}', [EventController::class, 'show'])->name('show');
});

// Struktur Organisasi Mahasiswa (BEM, MAM, DPM, HIMA, HMPS, UKM)
Route::get('/organisasi', [OrganizationController::class, 'index'])->name('organizations.index');

// Struktur Kepengurusan & Kabinet Eksekutif BEM
Route::get('/struktur-bem', [BemStructureController::class, 'index'])->name('bem.structure');
Route::get('/kepengurusan', fn () => redirect()->route('bem.structure'));

// Profil, Visi Misi, & Nilai
Route::get('/tentang', [PageController::class, 'about'])->name('about');

// Kontak & Sekretariat Ormawa
Route::get('/kontak', [PageController::class, 'contact'])->name('contact');

// Pertanyaan yang Sering Diajukan (FAQ)
Route::get('/faq', [PageController::class, 'faq'])->name('faq');

// Sistem Kanal Aspirasi Mahasiswa
Route::prefix('aspirasi')->name('aspirations.')->group(function () {
    // Pusat informasi & alur aspirasi
    Route::get('/', [AspirationController::class, 'index'])->name('info');

    // Formulir pengiriman aspirasi
    Route::get('/kirim', [AspirationController::class, 'create'])->name('create');
    Route::post('/kirim', [AspirationController::class, 'store'])
        ->middleware('throttle:aspiration-submission')
        ->name('store');

    // Halaman tanda terima tiket & kode rahasia (one-time display)
    Route::get('/sukses', [AspirationController::class, 'success'])->name('success');

    // Formulir & pemrosesan pelacakan aspirasi
    Route::get('/lacak', [AspirationTrackingController::class, 'showForm'])->name('tracking.form');
    Route::post('/lacak', [AspirationTrackingController::class, 'track'])
        ->middleware('throttle:aspiration-tracking')
        ->name('tracking.show');
});

// Live Chat (publik)
Route::post('/chat/conversations', [ChatController::class, 'store'])->middleware('throttle:30,1')->name('chat.conversations.store');
Route::get('/chat/conversations/{conversation}/messages', [ChatController::class, 'messages'])->middleware('throttle:60,1')->name('chat.conversations.messages');
Route::post('/chat/conversations/{conversation}/messages', [ChatController::class, 'message'])->middleware('throttle:30,1')->name('chat.conversations.message');
Route::post('/chat/conversations/{conversation}/attachments', [ChatController::class, 'upload'])->middleware('throttle:20,1')->name('chat.conversations.attachments.store');
Route::get('/chat/attachments/{attachment}/download', [ChatController::class, 'download'])->middleware('throttle:60,1')->name('chat.attachments.download');
Route::post('/chat/conversations/{conversation}/handoff', [ChatController::class, 'handoff'])->middleware('throttle:20,1')->name('chat.conversations.handoff');

/*
|--------------------------------------------------------------------------
| Admin Routes — Portal BEM Terpadu
|--------------------------------------------------------------------------
| Seluruh rute dashboard manajemen pengelola terpadu (Super Admin,
| Administrator, Tim Advokasi, dan Editor Konten).
|
*/
Route::prefix('admin')->name('admin.')->group(function () {
    // Autentikasi Pengelola (Tamu)
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])
        ->middleware('throttle:admin-login')
        ->name('login.attempt');

    // Area Terproteksi (Login & Role Verification)
    Route::middleware('admin.auth')->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Modul Manajemen Aspirasi Mahasiswa
        Route::middleware('admin.role:aspirations')->prefix('aspirasi')->name('aspirations.')->group(function () {
            Route::get('/', [AspirationAdminController::class, 'index'])->name('index');
            Route::get('/{aspiration}', [AspirationAdminController::class, 'show'])->name('show');
            Route::put('/{aspiration}', [AspirationAdminController::class, 'update'])->name('update');
        });

        // Modul Rekapitulasi & Ekspor Laporan Aspirasi
        Route::middleware('admin.role:aspirations')->prefix('laporan')->name('reports.')->group(function () {
            Route::get('/', [ReportAdminController::class, 'index'])->name('index');
            Route::get('/ekspor', [ReportAdminController::class, 'export'])->name('export');
        });

        // Modul Manajemen Warta & Berita
        Route::middleware('admin.role:articles')->prefix('berita')->name('articles.')->group(function () {
            Route::get('/', [ArticleAdminController::class, 'index'])->name('index');
            Route::get('/buat', [ArticleAdminController::class, 'create'])->name('create');
            Route::post('/', [ArticleAdminController::class, 'store'])->name('store');
            Route::get('/{article}/edit', [ArticleAdminController::class, 'edit'])->name('edit');
            Route::put('/{article}', [ArticleAdminController::class, 'update'])->name('update');
            Route::delete('/{article}', [ArticleAdminController::class, 'destroy'])->name('destroy');
        });

        // Modul Manajemen Program Kerja
        Route::middleware('admin.role:programs')->prefix('program-kerja')->name('programs.')->group(function () {
            Route::get('/', [WorkProgramAdminController::class, 'index'])->name('index');
            Route::get('/buat', [WorkProgramAdminController::class, 'create'])->name('create');
            Route::post('/', [WorkProgramAdminController::class, 'store'])->name('store');
            Route::get('/{program}/edit', [WorkProgramAdminController::class, 'edit'])->name('edit');
            Route::put('/{program}', [WorkProgramAdminController::class, 'update'])->name('update');
            Route::delete('/{program}', [WorkProgramAdminController::class, 'destroy'])->name('destroy');
        });

        // Modul Manajemen Agenda & Event
        Route::middleware('admin.role:events')->prefix('agenda')->name('events.')->group(function () {
            Route::get('/', [EventAdminController::class, 'index'])->name('index');
            Route::get('/buat', [EventAdminController::class, 'create'])->name('create');
            Route::post('/', [EventAdminController::class, 'store'])->name('store');
            Route::get('/{event}/edit', [EventAdminController::class, 'edit'])->name('edit');
            Route::put('/{event}', [EventAdminController::class, 'update'])->name('update');
            Route::delete('/{event}', [EventAdminController::class, 'destroy'])->name('destroy');
        });

        // Modul Manajemen Organisasi Mahasiswa
        Route::middleware('admin.role:organizations')->prefix('organisasi')->name('organizations.')->group(function () {
            Route::get('/', [OrganizationAdminController::class, 'index'])->name('index');
            Route::get('/buat', [OrganizationAdminController::class, 'create'])->name('create');
            Route::post('/', [OrganizationAdminController::class, 'store'])->name('store');
            Route::get('/{organization}/edit', [OrganizationAdminController::class, 'edit'])->name('edit');
            Route::put('/{organization}', [OrganizationAdminController::class, 'update'])->name('update');
            Route::delete('/{organization}', [OrganizationAdminController::class, 'destroy'])->name('destroy');
        });

        // Modul Kepengurusan & Kabinet BEM
        Route::middleware('admin.role:bem_members')->prefix('kepengurusan')->name('bem_members.')->group(function () {
            Route::get('/', [BemMemberAdminController::class, 'index'])->name('index');
            Route::get('/buat', [BemMemberAdminController::class, 'create'])->name('create');
            Route::post('/', [BemMemberAdminController::class, 'store'])->name('store');
            Route::get('/{bemMember}/edit', [BemMemberAdminController::class, 'edit'])->name('edit');
            Route::put('/{bemMember}', [BemMemberAdminController::class, 'update'])->name('update');
            Route::delete('/{bemMember}', [BemMemberAdminController::class, 'destroy'])->name('destroy');
        });

        // Modul Manajemen Pengguna / Admin (Khusus Super Admin)
        Route::middleware('admin.role:super_admin')->prefix('pengguna')->name('users.')->group(function () {
            Route::get('/', [UserAdminController::class, 'index'])->name('index');
            Route::get('/buat', [UserAdminController::class, 'create'])->name('create');
            Route::post('/', [UserAdminController::class, 'store'])->name('store');
            Route::get('/{user}/edit', [UserAdminController::class, 'edit'])->name('edit');
            Route::put('/{user}', [UserAdminController::class, 'update'])->name('update');
            Route::put('/{user}/reset-password', [UserAdminController::class, 'resetPassword'])->name('reset_password');
            Route::delete('/{user}', [UserAdminController::class, 'destroy'])->name('destroy');
        });

        // Modul Pengaturan Kustomisasi Tema & Logo (Khusus Super Admin)
        Route::middleware('admin.role:super_admin')->prefix('pengaturan')->name('settings.')->group(function () {
            Route::get('/tema', [SettingAdminController::class, 'theme'])->name('theme');
            Route::put('/tema', [SettingAdminController::class, 'updateTheme'])->name('theme.update');
            Route::post('/tema/reset', [SettingAdminController::class, 'resetTheme'])->name('theme.reset');
            Route::get('/logo', [SettingAdminController::class, 'logo'])->name('logo');
            Route::post('/logo', [SettingAdminController::class, 'updateLogo'])->name('logo.update');
            Route::post('/logo/proses', [SettingAdminController::class, 'processLogoPreview'])->name('logo.process');
            Route::post('/logo/terapkan', [SettingAdminController::class, 'applyLogo'])->name('logo.apply');
            Route::post('/logo/batal', [SettingAdminController::class, 'cancelLogoPreview'])->name('logo.cancel');
            Route::get('/logo/pratinjau-temp/{token}', [SettingAdminController::class, 'serveTempPreview'])->name('logo.temp_preview');
            Route::get('/logo/asli-temp/{token}', [SettingAdminController::class, 'serveTempOriginal'])->name('logo.temp_original');
            Route::delete('/logo', [SettingAdminController::class, 'destroyLogo'])->name('logo.destroy');
        });

        // Modul Audit Log Aktivitas Sistem (Khusus Super Admin)
        Route::middleware('admin.role:super_admin')->prefix('audit-log')->name('audit_logs.')->group(function () {
            Route::get('/', [AuditLogAdminController::class, 'index'])->name('index');
        });

        // Modul FAQ Chat (Petugas)
        Route::middleware('admin.role:content')->prefix('chat-faq')->name('chat_faqs.')->group(function () {
            Route::get('/', [ChatFaqAdminController::class, 'index'])->name('index');
            Route::get('/buat', [ChatFaqAdminController::class, 'create'])->name('create');
            Route::post('/', [ChatFaqAdminController::class, 'store'])->name('store');
            Route::get('/{faq}/edit', [ChatFaqAdminController::class, 'edit'])->name('edit');
            Route::put('/{faq}', [ChatFaqAdminController::class, 'update'])->name('update');
            Route::delete('/{faq}', [ChatFaqAdminController::class, 'destroy'])->name('destroy');
        });
        Route::middleware('admin.role:chat')->prefix('chat')->name('chat.')->group(function () {
            Route::get('/', [ChatAdminController::class, 'index'])->name('index');
            Route::get('/{conversation}', [ChatAdminController::class, 'show'])->name('show');
            Route::post('/{conversation}/balas', [ChatAdminController::class, 'reply'])->name('reply');
            Route::put('/{conversation}/assign', [ChatAdminController::class, 'assign'])->name('assign');
            Route::post('/{conversation}/selesai', [ChatAdminController::class, 'resolve'])->name('resolve');
        });
    });
});
