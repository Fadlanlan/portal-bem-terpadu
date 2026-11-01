<?php

namespace App\Http\Controllers;

use App\Enums\AspirationCategory;
use App\Http\Requests\StoreAspirationRequest;
use App\Services\AspirationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AspirationController extends Controller
{
    /**
     * Tampilkan halaman pusat informasi dan panduan aspirasi mahasiswa.
     */
    public function index(): View
    {
        return view('pages.aspiration-info');
    }

    /**
     * Tampilkan formulir pengiriman aspirasi mahasiswa.
     */
    public function create(): View
    {
        $categories = AspirationCategory::options();

        return view('pages.aspirations.create', compact('categories'));
    }

    /**
     * Simpan aspirasi baru ke basis data melalui transaksi layanan.
     */
    public function store(StoreAspirationRequest $request, AspirationService $service): RedirectResponse
    {
        $result = $service->createAspiration($request->validated());

        return redirect()
            ->route('aspirations.success')
            ->with([
                'aspiration_saved' => true,
                'ticket_number' => $result['ticket_number'],
                'tracking_code' => $result['tracking_code'],
            ]);
    }

    /**
     * Tampilkan nomor tiket dan kode pelacakan rahasia satu kali setelah pengiriman berhasil.
     */
    public function success(): View|RedirectResponse
    {
        $ticketNumber = session('ticket_number');
        $trackingCode = session('tracking_code');

        if (! $ticketNumber || ! $trackingCode) {
            return redirect()
                ->route('aspirations.info')
                ->with('status_notice', 'Sesi tampilan tiket telah berakhir atau tautan dibuka kembali.');
        }

        return view('pages.aspirations.success', [
            'ticketNumber' => $ticketNumber,
            'trackingCode' => $trackingCode,
        ]);
    }
}
