<?php

namespace App\Models;

use App\Services\MarkdownService;
use App\Traits\HasContentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Galeri extends Model
{
    use HasContentStatus, SoftDeletes;

    protected $fillable = [
        'user_id',
        'judul',
        'deskripsi',
        'gambar',
        'kategori',
        'status',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get rendered HTML from markdown description
     * MANDATORY: Use MarkdownService for safe server-side rendering
     */
    public function getDeskripsiHtmlAttribute()
    {
        return $this->deskripsi ? app(MarkdownService::class)->parseDeskripsi($this->deskripsi) : null;
    }

    /**
     * Get full URL for gambar
     */
    public function getGambarUrlAttribute()
    {
        return $this->gambar ? Storage::url($this->gambar) : null;
    }

    /**
     * Get badge color for kategori
     */
    public function getKategoriBadgeColorAttribute()
    {
        return match ($this->kategori) {
            'kegiatan' => 'bg-blue-100 text-blue-800',
            'lomba' => 'bg-yellow-100 text-yellow-800',
            'pembelajaran' => 'bg-green-100 text-green-800',
            'kunjungan' => 'bg-purple-100 text-purple-800',
            'lainnya' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}

