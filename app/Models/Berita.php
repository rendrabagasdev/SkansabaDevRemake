<?php

namespace App\Models;

use App\Traits\HasContentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Berita extends Model
{
    use HasContentStatus, SoftDeletes;

    protected $table = 'news';

    protected $fillable = [
        'slug',
        'title',
        'excerpt',
        'content_md',
        'thumbnail',
        'author_id',
        'published_at',
        'status',
        'is_highlight',
        'views',
        'meta',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_highlight' => 'boolean',
        'views' => 'integer',
        'meta' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        // Auto-generate slug from title
        static::creating(function ($berita) {
            if (empty($berita->slug)) {
                $berita->slug = Str::slug($berita->title);
            }
        });

        static::updating(function ($berita) {
            if ($berita->isDirty('title') && empty($berita->slug)) {
                $berita->slug = Str::slug($berita->title);
            }
        });
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function incrementViews(): void
    {
        $this->increment('views');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeHighlight($query)
    {
        return $query->where('is_highlight', true);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
