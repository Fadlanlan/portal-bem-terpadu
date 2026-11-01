<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AspirationStatus;
use App\Http\Controllers\Controller;
use App\Models\Aspiration;
use App\Models\Article;
use App\Models\Event;
use App\Models\WorkProgram;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman utama Dashboard Admin beserta ringkasan statistik aktual.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        // Agregasi statistik aspirasi dari database
        $aspirationCounts = Aspiration::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $stats = [
            'total_aspirations' => array_sum($aspirationCounts),
            'aspirations_new' => $aspirationCounts[AspirationStatus::Submitted->value] ?? 0,
            'aspirations_under_review' => $aspirationCounts[AspirationStatus::UnderReview->value] ?? 0,
            'aspirations_in_progress' => $aspirationCounts[AspirationStatus::InProgress->value] ?? 0,
            'aspirations_resolved' => $aspirationCounts[AspirationStatus::Resolved->value] ?? 0,
            'aspirations_rejected' => $aspirationCounts[AspirationStatus::Rejected->value] ?? 0,
            'total_articles' => Article::count(),
            'total_programs' => WorkProgram::count(),
            'total_upcoming_events' => Event::where('started_at', '>=', now()->startOfDay())->count(),
        ];

        // Aktivitas terkini
        $recentAspirations = Aspiration::latest('submitted_at')
            ->take(6)
            ->get();

        $recentArticles = Article::latest('created_at')
            ->take(5)
            ->get();

        $upcomingEvents = Event::where('started_at', '>=', now()->startOfDay())
            ->orderBy('started_at', 'asc')
            ->take(4)
            ->get();

        return view('admin.dashboard', [
            'user' => $user,
            'stats' => $stats,
            'recentAspirations' => $recentAspirations,
            'recentArticles' => $recentArticles,
            'upcomingEvents' => $upcomingEvents,
        ]);
    }
}
