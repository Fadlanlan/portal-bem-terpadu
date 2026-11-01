<?php

namespace App\Http\Controllers;

use App\Models\BemMember;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class BemStructureController extends Controller
{
    /**
     * Tampilkan halaman publik struktur kepengurusan dan kabinet eksekutif BEM.
     */
    public function index(): View
    {
        $hasTable = Schema::hasTable('bem_members');
        $members = $hasTable ? BemMember::published()->get() : collect();

        // Pisahkan anggota per tingkatan hierarki
        $presmaWapresma = $members->where('level', 'presma_wapresma')->values();
        $bph = $members->where('level', 'bph')->values();
        $ministers = $members->where('level', 'minister')->values();
        $staff = $members->where('level', 'staff')->values();

        // Kelompokkan menteri dan staf berdasarkan departemen
        $ministersByDept = $ministers->groupBy(fn ($item) => $item->department ?: 'Kementerian / Biro Umum');
        $staffByDept = $staff->groupBy(fn ($item) => $item->department ?: 'Departemen / Staf Umum');

        // Tentukan periode aktif (ambil periode pengurus pertama atau default 2025/2026)
        $activePeriod = $members->first()?->period ?? '2025/2026';

        return view('pages.bem.structure', [
            'totalMembers' => $members->count(),
            'activePeriod' => $activePeriod,
            'presmaWapresma' => $presmaWapresma,
            'bph' => $bph,
            'ministersByDept' => $ministersByDept,
            'staffByDept' => $staffByDept,
            'allMembers' => $members,
        ]);
    }
}
