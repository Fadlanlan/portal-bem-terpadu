<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Pembaruan Status Aspirasi</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc; color: #1e293b; padding: 24px; margin: 0;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
        <div style="background-color: #0f172a; padding: 24px; color: #ffffff;">
            <h1 style="margin: 0; font-size: 18px; font-weight: 800; color: #f59e0b;">Portal BEM Terpadu</h1>
            <p style="margin: 4px 0 0; font-size: 13px; color: #94a3b8;">Informasi Perkembangan Status Aspirasi</p>
        </div>
        <div style="padding: 24px;">
            <p style="margin-top: 0; font-size: 14px; line-height: 1.6;">
                Halo Rekan Mahasiswa,<br>
                Status laporan aspirasi Anda dengan nomor tiket <strong>{{ $aspiration->ticket_number }}</strong> telah diperbarui oleh pengelola:
            </p>

            <div style="background-color: #f1f5f9; border-left: 4px solid #f59e0b; padding: 16px; border-radius: 0 8px 8px 0; margin: 20px 0;">
                <span style="font-size: 11px; font-weight: bold; text-transform: uppercase; color: #64748b; display: block; margin-bottom: 4px;">Status Terkini:</span>
                <span style="font-size: 16px; font-weight: 800; color: #0f172a;">{{ $aspiration->status_label }}</span>
                @if($updateNote)
                    <p style="margin: 8px 0 0; font-size: 13px; color: #475569; line-height: 1.5;">
                        <strong>Catatan Pengelola:</strong> {{ $updateNote }}
                    </p>
                @endif
            </div>

            <p style="font-size: 13px; color: #64748b; line-height: 1.6;">
                Untuk melihat linimasa penanganan lengkap serta tanggapan resmi, silakan kunjungi laman pelacakan aspirasi dengan memasukkan nomor tiket dan kode rahasia pelacakan yang telah Anda simpan sebelumnya:
            </p>

            <div style="text-align: center; margin: 24px 0;">
                <a href="{{ route('aspirations.tracking.form') }}"
                   style="display: inline-block; background-color: #0f172a; color: #ffffff; padding: 12px 24px; font-size: 13px; font-weight: bold; text-decoration: none; border-radius: 10px;">
                    Lacak Status di Portal BEM &rarr;
                </a>
            </div>

            <p style="font-size: 12px; color: #94a3b8; font-style: italic; margin-bottom: 0;">
                *Catatan Keamanan: Kode pelacakan rahasia Anda tidak dicantumkan dalam surel ini demi menjaga kerahasiaan kepemilikan tiket.
            </p>
        </div>
        <div style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; padding: 16px 24px; font-size: 11px; color: #94a3b8; text-align: center;">
            &copy; {{ date('Y') }} Portal BEM Terpadu &bull; Kanal Advokasi & Aspirasi Terpercaya Mahasiswa
        </div>
    </div>
</body>
</html>
