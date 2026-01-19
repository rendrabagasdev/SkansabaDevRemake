<?php

namespace App\Models;

use App\Traits\HasContentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class DokumenKurikulum extends Model
{
    use HasContentStatus, SoftDeletes;

    protected $fillable = [
        'user_id',
        'judul',
        'jenis',
        'tahun_berlaku',
        'file',
        'ukuran_file',
        'status',
        'published_at',
    ];

    protected $casts = [
        'tahun_berlaku' => 'integer',
        'ukuran_file' => 'integer',
        'published_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get full URL for file
     */
    public function getFileUrlAttribute()
    {
        return $this->file ? Storage::url($this->file) : null;
    }

    /**
     * Get formatted file size (KB/MB)
     */
    public function getFormattedFileSizeAttribute()
    {
        if (!$this->ukuran_file) {
            return '0 KB';
        }

        $bytes = $this->ukuran_file;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }

    /**
     * Get file extension
     */
    public function getFileExtensionAttribute()
    {
        return $this->file ? strtoupper(pathinfo($this->file, PATHINFO_EXTENSION)) : null;
    }

    /**
     * Get badge color for jenis
     */
    public function getJenisBadgeColorAttribute()
    {
        return match ($this->jenis) {
            'kurikulum' => 'bg-blue-100 text-blue-800',
            'silabus' => 'bg-green-100 text-green-800',
            'modul' => 'bg-purple-100 text-purple-800',
            'panduan' => 'bg-yellow-100 text-yellow-800',
            'lainnya' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
