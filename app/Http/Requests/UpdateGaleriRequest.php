<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGaleriRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'judul' => ['required', 'string', 'max:255'],
            'kategori' => ['required', Rule::in(['kegiatan', 'lomba', 'pembelajaran', 'kunjungan', 'lainnya'])],
            'deskripsi' => ['nullable', 'string'],
            'gambar' => ['nullable', 'string'], // Base64 encoded image from cropper (optional for update)
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'judul' => 'judul galeri',
            'kategori' => 'kategori',
            'deskripsi' => 'deskripsi',
            'gambar' => 'gambar',
            'status' => 'status publikasi',
        ];
    }
}
