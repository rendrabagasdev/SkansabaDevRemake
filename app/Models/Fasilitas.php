<?php

namespace App\Models;

use App\Services\MarkdownService;
use App\Traits\HasContentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Fasilitas extends Model
{
    use HasContentStatus, SoftDeletes;

    protected $fillable = [
        'user_id',
        'tempat',
        'deskripsi',
        'gambar',
        'fasilitas',
        'status',
        'published_at',
    ];

    protected $casts = [
        'fasilitas' => 'array',
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
        return app(MarkdownService::class)->parseDeskripsi($this->deskripsi);
    }

    /**
     * Get full URL for gambar
     */
    public function getGambarUrlAttribute()
    {
        return $this->gambar ? Storage::url($this->gambar) : null;
    }

    /**
     * Get count of fasilitas items
     */
    public function getFasilitasCountAttribute()
    {
        return is_array($this->fasilitas) ? count($this->fasilitas) : 0;
    }
}
