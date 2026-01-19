<?php

namespace App\Models;

use App\Services\MarkdownService;
use App\Traits\HasContentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Prestasi extends Model
{
    use HasContentStatus, SoftDeletes;

    protected $fillable = [
        'user_id',
        'judul',
        'deskripsi',
        'jenis',
        'tingkat',
        'penyelenggara',
        'nama_siswa',
        'kelas',
        'tanggal_prestasi',
        'tahun',
        'gambar',
        'sertifikat',
        'status',
        'published_at',
    ];

    protected $casts = [
        'tanggal_prestasi' => 'date',
        'tahun' => 'integer',
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
     * Get plain text excerpt from description
     */
    public function getDeskripsiExcerptAttribute()
    {
        return app(MarkdownService::class)->excerpt($this->deskripsi, 100);
    }

    /**
     * Get URL for gambar
     */
    public function getGambarUrlAttribute()
    {
        return $this->gambar ? Storage::url($this->gambar) : null;
    }

    /**
     * Get URL for sertifikat
     */
    public function getSertifikatUrlAttribute()
    {
        return $this->sertifikat ? Storage::url($this->sertifikat) : null;
    }

    /**
     * Scope to get published prestasi only
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope to filter by jenis
     */
    public function scopeJenis($query, $jenis)
    {
        return $query->where('jenis', $jenis);
    }

    /**
     * Scope to filter by tingkat
     */
    public function scopeTingkat($query, $tingkat)
    {
        return $query->where('tingkat', $tingkat);
    }

    /**
     * Scope to filter by tahun
     */
    public function scopeTahun($query, $tahun)
    {
        return $query->where('tahun', $tahun);
    }
}
