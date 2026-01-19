<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDokumenKurikulumRequest extends FormRequest
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
            'jenis' => ['required', Rule::in(['kurikulum', 'silabus', 'modul', 'panduan', 'lainnya'])],
            'tahun_berlaku' => ['required', 'integer', 'min:2000', 'max:' . (date('Y') + 10)],
            'file' => [
                'required',
                'file',
                'max:10240', // 10MB in kilobytes
                'mimes:pdf,doc,docx',
                'mimetypes:application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            ],
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
            'judul' => 'judul dokumen',
            'jenis' => 'jenis dokumen',
            'tahun_berlaku' => 'tahun berlaku',
            'file' => 'file dokumen',
            'status' => 'status publikasi',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'file.max' => 'Ukuran file maksimal 10 MB.',
            'file.mimes' => 'File harus berformat PDF, DOC, atau DOCX.',
            'file.mimetypes' => 'File harus berformat PDF, DOC, atau DOCX.',
            'tahun_berlaku.min' => 'Tahun berlaku minimal 2000.',
            'tahun_berlaku.max' => 'Tahun berlaku tidak valid.',
        ];
    }
}
