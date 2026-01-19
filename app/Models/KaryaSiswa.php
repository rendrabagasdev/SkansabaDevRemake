<?php

namespace App\Models;

use App\Services\MarkdownService;
use App\Traits\HasContentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class KaryaSiswa extends Model
{
    use HasContentStatus, SoftDeletes;

    protected $fillable = [
        'user_id',
        'judul',
        'deskripsi',
        'kategori',
        'teknologi',
        'nama_siswa',
        'kelas',
        'tahun',
        'gambar',
        'url_demo',
        'url_repo',
        'status',
        'published_at',
    ];

    protected $casts = [
        'tahun' => 'integer',
        'published_at' => 'datetime',
        'teknologi' => 'array',
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
     * Get excerpt from description (first 150 chars)
     */
    public function getDeskripsiExcerptAttribute()
    {
        if (!$this->deskripsi) {
            return '';
        }
        // Strip markdown syntax and get first 150 chars
        $text = strip_tags($this->deskripsi);
        return strlen($text) > 150 ? substr($text, 0, 150) . '...' : $text;
    }

    /**
     * Get thumbnail URL from image path
     */
    public function getGambarUrlAttribute()
    {
        if ($this->gambar) {
            return Storage::url($this->gambar);
        }
        return null;
    }
}
