<?php

namespace App\Models;

use App\Services\MarkdownService;
use App\Traits\HasContentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Alumni extends Model
{
    use HasFactory, HasContentStatus;

    protected $fillable = [
        'user_id',
        'nama',
        'tahun_lulus',
        'status_alumni',
        'institusi',
        'bidang',
        'deskripsi',
        'foto',
        'status',
        'published_at',
    ];

    protected $casts = [
        'tahun_lulus' => 'integer',
        'published_at' => 'datetime',
    ];

    /**
     * Relasi ke User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Accessor untuk URL foto
     */
    public function getFotoUrlAttribute(): ?string
    {
        if (!$this->foto) {
            return null;
        }

        return Storage::disk('public')->url($this->foto);
    }

    /**
     * Accessor untuk deskripsi HTML (markdown to HTML)
     */
    public function getDeskripsiHtmlAttribute(): string
    {
        if (!$this->deskripsi) {
            return '';
        }

        return app(MarkdownService::class)->parseDeskripsi($this->deskripsi);
    }

    /**
     * Accessor untuk excerpt deskripsi
     */
    public function getDeskripsiExcerptAttribute(): string
    {
        if (!$this->deskripsi) {
            return '';
        }

        return app(MarkdownService::class)->toPlainText($this->deskripsi, 150);
    }

    /**
     * Accessor untuk badge color status_alumni
     */
    public function getStatusAlumniBadgeColorAttribute(): string
    {
        return match ($this->status_alumni) {
            'kuliah' => 'blue',
            'kerja' => 'green',
            'wirausaha' => 'purple',
            'belum_diketahui' => 'gray',
            default => 'gray',
        };
    }

    /**
     * Accessor untuk label status_alumni
     */
    public function getStatusAlumniLabelAttribute(): string
    {
        return match ($this->status_alumni) {
            'kuliah' => 'Kuliah',
            'kerja' => 'Kerja',
            'wirausaha' => 'Wirausaha',
            'belum_diketahui' => 'Belum Diketahui',
            default => ucfirst($this->status_alumni),
        };
    }

    /**
     * Scope: Alumni published only
     */
    public function scopePublishedOnly($query)
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /**
     * Scope: Filter by tahun lulus
     */
    public function scopeByTahunLulus($query, $tahun)
    {
        return $query->where('tahun_lulus', $tahun);
    }

    /**
     * Scope: Filter by status alumni
     */
    public function scopeByStatusAlumni($query, $status)
    {
        return $query->where('status_alumni', $status);
    }
}
