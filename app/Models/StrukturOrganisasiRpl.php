<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use App\Services\MarkdownService;

class StrukturOrganisasiRpl extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nama',
        'jabatan',
        'foto',
        'deskripsi_md',
        'order',
        'status',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    /**
     * Scope to get only published records
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope to order by order column
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }

    /**
     * Get photo URL
     */
    public function getFotoUrlAttribute()
    {
        return $this->foto ? Storage::url($this->foto) : null;
    }

    /**
     * Get rendered HTML from markdown description
     * MANDATORY: Use MarkdownService for safe server-side rendering
     */
    public function getDeskripsiHtmlAttribute()
    {
        return app(MarkdownService::class)->parseDeskripsi($this->deskripsi_md);
    }

    /**
     * Get plain text excerpt from description
     */
    public function getDeskripsiExcerptAttribute()
    {
        return app(MarkdownService::class)->excerpt($this->deskripsi_md, 100);
    }
}
