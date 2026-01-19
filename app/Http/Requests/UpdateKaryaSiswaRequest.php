<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateKaryaSiswaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['required', 'string', 'min:10'],
            'kategori' => ['required', 'string', 'max:255'],
            'teknologi' => ['required', 'string', 'max:255'],
            'nama_siswa' => ['required', 'string', 'max:255'],
            'kelas' => ['required', 'string', 'max:255'],
            'tahun' => ['required', 'integer', 'min:2000', 'max:' . (date('Y') + 1)],
            'gambar' => ['nullable', 'string'],
            'url_demo' => ['nullable', 'url', 'max:255'],
            'url_repo' => ['nullable', 'url', 'max:255'],
            'status' => ['required', 'in:draft,review,published,archived'],
        ];
    }

    public function messages(): array
    {
        return [
            'judul.required' => 'Judul karya wajib diisi',
            'deskripsi.required' => 'Deskripsi karya wajib diisi',
            'deskripsi.min' => 'Deskripsi minimal 10 karakter',
            'kategori.required' => 'Kategori karya wajib dipilih',
            'teknologi.required' => 'Teknologi yang digunakan wajib diisi',
            'nama_siswa.required' => 'Nama siswa wajib diisi',
            'kelas.required' => 'Kelas wajib diisi',
            'tahun.required' => 'Tahun karya wajib diisi',
            'tahun.min' => 'Tahun minimal 2000',
            'tahun.max' => 'Tahun tidak valid',
            'url_demo.url' => 'URL demo harus valid',
            'url_repo.url' => 'URL repository harus valid',
        ];
    }
}
