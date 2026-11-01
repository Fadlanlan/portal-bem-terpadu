<?php

namespace App\Http\Controllers;

use App\Http\Requests\TrackAspirationRequest;
use App\Services\AspirationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AspirationTrackingController extends Controller
{
    /**
     * Tampilkan formulir pelacakan status aspirasi.
     */
    public function showForm(): View
    {
        return view('pages.aspirations.track');
    }

    /**
     * Verifikasi nomor tiket dan kode rahasia, lalu tampilkan hasil perkembangan aspirasi.
     */
    public function track(TrackAspirationRequest $request, AspirationService $service): View|RedirectResponse
    {
        $aspiration = $service->verifyTracking(
            ticketNumber: $request->validated('ticket_number'),
            trackingCode: $request->validated('tracking_code'),
        );

        if (! $aspiration) {
            return back()
                ->withErrors([
                    'tracking_error' => 'Kombinasi nomor tiket dan kode pelacakan rahasia tidak ditemukan atau tidak cocok.',
                ])
                ->withInput([
                    'ticket_number' => $request->validated('ticket_number'),
                ]);
        }

        return view('pages.aspirations.tracking-result', [
            'aspiration' => $aspiration,
        ]);
    }
}
