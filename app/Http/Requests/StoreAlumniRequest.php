<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAlumniRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:255'],
            'tahun_lulus' => ['required', 'integer', 'min:1990', 'max:' . (date('Y') + 1)],
            'status_alumni' => ['required', 'in:kuliah,kerja,wirausaha,belum_diketahui'],
            'institusi' => ['nullable', 'required_unless:status_alumni,belum_diketahui', 'string', 'max:255'],
            'bidang' => ['nullable', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'foto' => ['nullable', 'string'], // Base64 from cropper
            'status' => ['required', 'in:draft,published,archived'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama alumni wajib diisi.',
            'tahun_lulus.required' => 'Tahun lulus wajib diisi.',
            'tahun_lulus.min' => 'Tahun lulus minimal 1990.',
            'tahun_lulus.max' => 'Tahun lulus tidak valid.',
            'status_alumni.required' => 'Status alumni wajib dipilih.',
            'institusi.required_unless' => 'Institusi wajib diisi untuk status kuliah, kerja, atau wirausaha.',
            'status.required' => 'Status publikasi wajib dipilih.',
        ];
    }
}
