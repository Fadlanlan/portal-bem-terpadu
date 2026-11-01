<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AspirationCategory;
use App\Enums\AspirationStatus;
use App\Http\Controllers\Controller;
use App\Models\Aspiration;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportAdminController extends Controller
{
    /**
     * Tampilkan halaman rekapitulasi laporan aspirasi dengan filter dan ringkasan metrik.
     */
    public function index(Request $request): View
    {
        $validatedFilters = $this->validateFilters($request);

        $baseQuery = $this->buildFilteredQuery($validatedFilters);

        // Agregasi Statistik Ringkasan
        $totalAspirations = (clone $baseQuery)->count();
        $statusCounts = (clone $baseQuery)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $categoryCounts = (clone $baseQuery)
            ->selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->pluck('count', 'category')
            ->toArray();

        $anonymousCount = (clone $baseQuery)
            ->where('is_anonymous', true)
            ->count();

        // Data Terpaginasi
        $aspirations = $baseQuery
            ->orderBy('submitted_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.reports.index', [
            'aspirations' => $aspirations,
            'totalAspirations' => $totalAspirations,
            'statusCounts' => $statusCounts,
            'categoryCounts' => $categoryCounts,
            'anonymousCount' => $anonymousCount,
            'categories' => AspirationCategory::options(),
            'statuses' => AspirationStatus::options(),
            'filters' => $validatedFilters,
        ]);
    }

    /**
     * Ekspor data aspirasi terfilter ke dalam berkas CSV yang aman.
     */
    public function export(Request $request): StreamedResponse
    {
        $validatedFilters = $this->validateFilters($request);
        $query = $this->buildFilteredQuery($validatedFilters)->orderBy('submitted_at', 'desc');

        $fileName = 'laporan-aspirasi-' . date('Ymd-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($query) {
            $handle = fopen('php://output', 'w');

            // Tambahkan UTF-8 BOM untuk kompatibilitas tampilan Microsoft Excel
            fputs($handle, "\xEF\xBB\xBF");

            // Header Kolom CSV (Aman: Tanpa Nama Pelapor, Surel, NIM, IP, Catatan Internal, atau Kode Pelacakan)
            fputcsv($handle, [
                'No. Tiket',
                'Kategori',
                'Subjek',
                'Isi Pesan Aspirasi',
                'Status Penanganan',
                'Tipe Laporan',
                'Tanggal Diajukan (WIB)',
                'Tanggal Diselesaikan (WIB)',
                'Tanggapan Resmi BEM',
            ]);

            // Eksekusi kueri secara chunking agar ramah penggunaan memori
            $query->chunk(200, function ($aspirations) use ($handle) {
                foreach ($aspirations as $asp) {
                    $statusLabel = $asp->status instanceof AspirationStatus
                        ? $asp->status->label()
                        : ($asp->status_label ?? $asp->status);

                    $categoryLabel = $asp->category instanceof AspirationCategory
                        ? $asp->category->label()
                        : ($asp->category_label ?? $asp->category);

                    $submittedDate = $asp->submitted_at ? $asp->submitted_at->format('d/m/Y H:i') : '-';
                    $resolvedDate = $asp->resolved_at ? $asp->resolved_at->format('d/m/Y H:i') : '-';

                    fputcsv($handle, [
                        self::sanitizeForCsv((string) $asp->ticket_number),
                        self::sanitizeForCsv((string) $categoryLabel),
                        self::sanitizeForCsv((string) $asp->subject),
                        self::sanitizeForCsv((string) $asp->message),
                        self::sanitizeForCsv((string) $statusLabel),
                        $asp->is_anonymous ? 'Anonim (Rahasia)' : 'Terbuka',
                        $submittedDate,
                        $resolvedDate,
                        self::sanitizeForCsv((string) ($asp->admin_response ?? '-')),
                    ]);
                }
            });

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Validasi parameter filter pencarian dan rentang waktu.
     *
     * @return array<string, mixed>
     */
    private function validateFilters(Request $request): array
    {
        $validator = Validator::make($request->all(), [
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'category' => ['nullable', 'string'],
            'status' => ['nullable', 'string'],
        ], [
            'end_date.after_or_equal' => 'Tanggal akhir tidak boleh mendahului tanggal mulai.',
        ]);

        $data = $validator->validated();

        // Validasi batasan rentang waktu (maksimal 5 tahun / 1825 hari)
        if (! empty($data['start_date']) && ! empty($data['end_date'])) {
            $start = Carbon::parse($data['start_date']);
            $end = Carbon::parse($data['end_date']);
            $maxDays = config('aspirations.reports.max_export_days', 1825);

            if ($end->diffInDays($start) > $maxDays) {
                $data['end_date'] = $start->copy()->addDays($maxDays)->format('Y-m-d');
            }
        }

        return $data;
    }

    /**
     * Susun kueri basis data berdasarkan filter yang aktif.
     */
    private function buildFilteredQuery(array $filters)
    {
        $query = Aspiration::query();

        if (! empty($filters['start_date'])) {
            $query->whereDate('submitted_at', '>=', $filters['start_date']);
        }

        if (! empty($filters['end_date'])) {
            $query->whereDate('submitted_at', '<=', $filters['end_date']);
        }

        if (! empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query;
    }

    /**
     * Netralkan potensi CSV Formula Injection (CWE-1236).
     * Jika nilai diawali karakter eksekusi (=, +, -, @, tab, newline),
     * tambahkan tanda petik tunggal di depannya agar spreadsheet memperlakukannya sebagai teks statis.
     */
    public static function sanitizeForCsv(string $value): string
    {
        $trimmed = ltrim($value, ' ');

        if ($trimmed === '') {
            return '';
        }

        $dangerousPrefixes = ['=', '+', '-', '@', "\t", "\r", "\n"];

        foreach ($dangerousPrefixes as $prefix) {
            if (str_starts_with($trimmed, $prefix)) {
                return "'" . $value;
            }
        }

        return $value;
    }
}
