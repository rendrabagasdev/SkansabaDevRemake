<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFasilitasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tempat' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'gambar' => ['nullable', 'string'], // Base64 from cropper
            'fasilitas' => ['nullable', 'array'],
            'fasilitas.*' => ['string', 'max:255'],
            'status' => ['required', 'in:draft,published,archived'],
        ];
    }

    public function messages(): array
    {
        return [
            'tempat.required' => 'Nama tempat/ruangan wajib diisi',
            'tempat.max' => 'Nama tempat maksimal 255 karakter',
            'fasilitas.array' => 'Fasilitas harus berupa array',
            'fasilitas.*.string' => 'Setiap item fasilitas harus berupa teks',
            'fasilitas.*.max' => 'Setiap item fasilitas maksimal 255 karakter',
            'status.required' => 'Status wajib diisi',
            'status.in' => 'Status harus salah satu dari: draft, published, archived',
        ];
    }
}
