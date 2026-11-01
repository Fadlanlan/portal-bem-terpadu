<?php

namespace App\Http\Requests;

use App\Enums\AspirationCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAspirationRequest extends FormRequest
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
        $isAnonymous = filter_var($this->input('is_anonymous'), FILTER_VALIDATE_BOOLEAN);

        return [
            'category' => ['required', 'string', Rule::in(AspirationCategory::values())],
            'subject' => ['required', 'string', 'min:5', 'max:150'],
            'message' => ['required', 'string', 'min:15', 'max:5000'],
            'is_anonymous' => ['nullable', 'boolean'],
            'reporter_name' => [$isAnonymous ? 'nullable' : 'required', 'nullable', 'string', 'max:100'],
            'reporter_email' => [$isAnonymous ? 'nullable' : 'required', 'nullable', 'email', 'max:100'],
            'reporter_student_id' => ['nullable', 'string', 'max:30'],
            'privacy_consent' => ['accepted'],
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
            'category.required' => 'Pilih kategori aspirasi yang sesuai.',
            'category.in' => 'Kategori aspirasi yang dipilih tidak valid.',
            'subject.required' => 'Judul / subjek aspirasi wajib diisi.',
            'subject.min' => 'Judul aspirasi minimal 5 karakter.',
            'subject.max' => 'Judul aspirasi maksimal 150 karakter.',
            'message.required' => 'Uraian isi aspirasi wajib diisi.',
            'message.min' => 'Uraian isi aspirasi minimal 15 karakter.',
            'message.max' => 'Uraian isi aspirasi maksimal 5000 karakter.',
            'reporter_name.required' => 'Nama pelapor wajib diisi jika Anda tidak memilih mode anonim.',
            'reporter_name.max' => 'Nama pelapor maksimal 100 karakter.',
            'reporter_email.required' => 'Alamat email wajib diisi jika Anda tidak memilih mode anonim.',
            'reporter_email.email' => 'Format alamat email tidak valid.',
            'reporter_email.max' => 'Alamat email maksimal 100 karakter.',
            'reporter_student_id.max' => 'NIM / Nomor Mahasiswa maksimal 30 karakter.',
            'privacy_consent.accepted' => 'Anda wajib menyetujui ketentuan privasi dan keabsahan laporan sebelum mengirim.',
        ];
    }

    /**
     * Siapkan data untuk validasi.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_anonymous' => $this->boolean('is_anonymous'),
            'subject' => is_string($this->subject) ? trim($this->subject) : $this->subject,
            'message' => is_string($this->message) ? trim($this->message) : $this->message,
            'reporter_name' => is_string($this->reporter_name) ? trim($this->reporter_name) : $this->reporter_name,
            'reporter_email' => is_string($this->reporter_email) ? trim($this->reporter_email) : $this->reporter_email,
            'reporter_student_id' => is_string($this->reporter_student_id) ? trim($this->reporter_student_id) : $this->reporter_student_id,
        ]);
    }
}
