<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Services\MarkdownService;

class ProspekKarir extends Model
{
    protected $fillable = [
        'title',
        'description',
        'icon',
        'image',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Scope to get only active career prospects
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by order column
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Get URL for image
     */
    public function getImageUrlAttribute()
    {
        return $this->image ? Storage::url($this->image) : null;
    }

    /**
     * Get rendered HTML from markdown description
     * MANDATORY: Use MarkdownService for safe server-side rendering
     */
    public function getDescriptionHtmlAttribute()
    {
        return app(MarkdownService::class)->parseDeskripsi($this->description);
    }

    /**
     * Get plain text excerpt from description
     */
    public function getDescriptionExcerptAttribute()
    {
        return app(MarkdownService::class)->excerpt($this->description, 100);
    }
}
