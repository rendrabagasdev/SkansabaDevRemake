<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePrestasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['required', 'string'],
            'jenis' => ['required', 'in:akademik,non-akademik,kompetisi,sertifikasi'],
            'tingkat' => ['required', 'in:sekolah,kecamatan,kota,provinsi,nasional,internasional'],
            'penyelenggara' => ['required', 'string', 'max:255'],
            'nama_siswa' => ['required', 'string', 'max:255'],
            'kelas' => ['required', 'string', 'max:255'],
            'tanggal_prestasi' => ['required', 'date'],
            'tahun' => ['required', 'integer', 'min:2000', 'max:' . (date('Y') + 1)],
            'gambar' => ['nullable', 'string'], // Base64 from cropper
            'sertifikat' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:5120'], // 5MB
            'status' => ['required', 'in:draft,review,published,archived'],
        ];
    }

    public function messages(): array
    {
        return [
            'judul.required' => 'Judul prestasi wajib diisi',
            'deskripsi.required' => 'Deskripsi prestasi wajib diisi',
            'jenis.required' => 'Jenis prestasi wajib dipilih',
            'tingkat.required' => 'Tingkat prestasi wajib dipilih',
            'penyelenggara.required' => 'Penyelenggara wajib diisi',
            'nama_siswa.required' => 'Nama siswa wajib diisi',
            'kelas.required' => 'Kelas wajib diisi',
            'tanggal_prestasi.required' => 'Tanggal prestasi wajib diisi',
            'tahun.required' => 'Tahun prestasi wajib diisi',
            'tahun.min' => 'Tahun minimal 2000',
            'tahun.max' => 'Tahun tidak valid',
            'sertifikat.mimes' => 'Sertifikat harus berformat PDF, JPG, PNG, atau WebP',
            'sertifikat.max' => 'Ukuran sertifikat maksimal 5MB',
        ];
    }
}
