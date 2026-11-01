<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TrackAspirationRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna diizinkan membuat permintaan ini.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Dapatkan aturan validasi yang berlaku untuk permintaan ini.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'ticket_number' => ['required', 'string', 'max:50'],
            'tracking_code' => ['required', 'string', 'max:50'],
        ];
    }

    /**
     * Dapatkan pesan kesalahan khusus untuk aturan yang ditentukan.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'ticket_number.required' => 'Nomor tiket pelacakan wajib diisi.',
            'ticket_number.max' => 'Nomor tiket tidak valid.',
            'tracking_code.required' => 'Kode pelacakan rahasia wajib diisi.',
            'tracking_code.max' => 'Kode pelacakan tidak valid.',
        ];
    }

    /**
     * Siapkan data untuk validasi.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'ticket_number' => is_string($this->ticket_number) ? trim(strtoupper($this->ticket_number)) : $this->ticket_number,
            'tracking_code' => is_string($this->tracking_code) ? trim($this->tracking_code) : $this->tracking_code,
        ]);
    }
}
