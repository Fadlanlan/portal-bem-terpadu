<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Aspirasi Mahasiswa Baru</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc; color: #1e293b; padding: 24px; margin: 0;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
        <div style="background-color: #0f172a; padding: 24px; color: #ffffff;">
            <h1 style="margin: 0; font-size: 18px; font-weight: 800; color: #f59e0b;">Portal BEM Terpadu</h1>
            <p style="margin: 4px 0 0; font-size: 13px; color: #94a3b8;">Pemberitahuan Aspirasi Kemahasiswaan Baru</p>
        </div>
        <div style="padding: 24px;">
            <p style="margin-top: 0; font-size: 14px; line-height: 1.6;">
                Telah masuk satu aspirasi mahasiswa baru yang memerlukan peninjauan dan verifikasi dari tim pengelola:
            </p>

            <table style="width: 100%; border-collapse: collapse; margin: 16px 0; font-size: 13px;">
                <tr>
                    <td style="padding: 8px 12px; background-color: #f1f5f9; font-weight: bold; width: 35%; border-radius: 6px 0 0 6px;">Nomor Tiket</td>
                    <td style="padding: 8px 12px; background-color: #f8fafc; font-family: monospace; font-weight: bold; color: #0f172a; border-radius: 0 6px 6px 0;">{{ $aspiration->ticket_number }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 12px; background-color: #f1f5f9; font-weight: bold;">Kategori</td>
                    <td style="padding: 8px 12px; background-color: #f8fafc; color: #0f172a;">{{ $aspiration->category_label }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 12px; background-color: #f1f5f9; font-weight: bold;">Subjek Laporan</td>
                    <td style="padding: 8px 12px; background-color: #f8fafc; font-weight: 600; color: #0f172a;">{{ $aspiration->subject }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 12px; background-color: #f1f5f9; font-weight: bold;">Tipe Pelapor</td>
                    <td style="padding: 8px 12px; background-color: #f8fafc; color: #0f172a;">{{ $aspiration->is_anonymous ? 'Anonim (Identitas Terjaga)' : 'Terbuka' }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 12px; background-color: #f1f5f9; font-weight: bold;">Waktu Masuk</td>
                    <td style="padding: 8px 12px; background-color: #f8fafc; color: #0f172a;">{{ $aspiration->submitted_at?->format('d/m/Y H:i') ?? '-' }} WIB</td>
                </tr>
            </table>

            <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px; margin: 16px 0;">
                <strong style="display: block; font-size: 12px; color: #64748b; text-transform: uppercase; margin-bottom: 6px;">Isi Aspirasi:</strong>
                <p style="margin: 0; font-size: 13px; line-height: 1.6; white-space: pre-wrap; color: #334155;">{{ $aspiration->message }}</p>
            </div>

            <div style="text-align: center; margin-top: 24px;">
                <a href="{{ route('admin.aspirations.show', $aspiration) }}"
                   style="display: inline-block; background-color: #0f172a; color: #ffffff; padding: 12px 24px; font-size: 13px; font-weight: bold; text-decoration: none; border-radius: 10px;">
                    Buka Panel Tindak Lanjut &rarr;
                </a>
            </div>
        </div>
        <div style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; padding: 16px 24px; font-size: 11px; color: #94a3b8; text-align: center;">
            Surel otomatis ini dikirimkan oleh sistem Portal BEM Terpadu. Harap tidak membalas langsung ke alamat ini.
        </div>
    </div>
</body>
</html>
