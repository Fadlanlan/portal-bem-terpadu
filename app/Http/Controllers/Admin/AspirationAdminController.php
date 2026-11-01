<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AspirationCategory;
use App\Enums\AspirationStatus;
use App\Http\Controllers\Controller;
use App\Models\AdminAuditLog;
use App\Models\Aspiration;
use App\Services\AspirationService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AspirationAdminController extends Controller
{
    /**
     * Tampilkan daftar seluruh aspirasi masuk dengan fitur pencarian dan filter multi-kriteria.
     */
    public function index(Request $request): View
    {
        $query = Aspiration::query()->latest('submitted_at');

        // Pencarian nomor tiket atau subjek
        if ($search = trim((string) $request->input('q', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        // Filter status
        if ($status = $request->input('status')) {
            if (in_array($status, AspirationStatus::values(), true)) {
                $query->where('status', $status);
            }
        }

        // Filter kategori
        if ($category = $request->input('category')) {
            if (in_array($category, AspirationCategory::values(), true)) {
                $query->where('category', $category);
            }
        }

        // Filter rentang tanggal pengiriman
        if ($dateFrom = $request->input('date_from')) {
            try {
                $query->whereDate('submitted_at', '>=', Carbon::parse($dateFrom));
            } catch (\Exception $e) {
                // Abaikan format tanggal tidak valid
            }
        }

        if ($dateTo = $request->input('date_to')) {
            try {
                $query->whereDate('submitted_at', '<=', Carbon::parse($dateTo));
            } catch (\Exception $e) {
                // Abaikan format tanggal tidak valid
            }
        }

        $aspirations = $query->paginate(15)->withQueryString();

        return view('admin.aspirations.index', [
            'aspirations' => $aspirations,
            'categories' => AspirationCategory::options(),
            'statuses' => AspirationStatus::options(),
            'filters' => [
                'q' => $request->input('q'),
                'status' => $request->input('status'),
                'category' => $request->input('category'),
                'date_from' => $request->input('date_from'),
                'date_to' => $request->input('date_to'),
            ],
        ]);
    }

    /**
     * Tampilkan rincian aspirasi mahasiswa beserta linimasa riwayat status lengkap.
     */
    public function show(Aspiration $aspiration): View
    {
        $aspiration->load(['statusHistories.changedByUser']);

        return view('admin.aspirations.show', [
            'aspiration' => $aspiration,
            'statuses' => AspirationStatus::options(),
        ]);
    }

    /**
     * Perbarui status penanganan, simpan catatan internal, atau publikasikan respons resmi.
     */
    public function update(Request $request, Aspiration $aspiration, AspirationService $service): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in(AspirationStatus::values())],
            'note' => ['nullable', 'string', 'max:1000'],
            'admin_response' => ['nullable', 'string', 'max:5000'],
        ], [
            'status.required' => 'Pilih status tindak lanjut yang valid.',
            'status.in' => 'Status tindak lanjut yang dipilih tidak terdaftar.',
            'note.max' => 'Catatan penanganan maksimal 1000 karakter.',
            'admin_response.max' => 'Tanggapan resmi pengelola maksimal 5000 karakter.',
        ]);

        $newStatus = AspirationStatus::from($validated['status']);
        $oldStatusValue = $aspiration->status instanceof AspirationStatus
            ? $aspiration->status->value
            : (string) $aspiration->status;

        $service->updateAspirationStatus(
            aspiration: $aspiration,
            newStatus: $newStatus,
            note: $validated['note'] ?? null,
            adminResponse: $validated['admin_response'] ?? null,
            changedBy: $request->user()->id,
        );

        AdminAuditLog::record(
            'aspiration.status_update',
            'Status aspirasi tiket #' . $aspiration->ticket_number . ' diperbarui menjadi ' . $newStatus->label() . '.',
            $aspiration,
            [
                'ticket_number' => $aspiration->ticket_number,
                'old_status' => $oldStatusValue,
                'new_status' => $newStatus->value,
                'has_response' => ! empty($validated['admin_response']),
            ]
        );

        return redirect()
            ->route('admin.aspirations.show', $aspiration)
            ->with('success', 'Status aspirasi ' . $aspiration->ticket_number . ' berhasil diperbarui menjadi ' . $newStatus->label() . '.');
    }
}
