<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Mitra extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nama_mitra',
        'logo',
        'website',
        'status',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    /**
     * Get URL for logo
     */
    public function getLogoUrlAttribute()
    {
        return $this->logo ? Storage::url($this->logo) : null;
    }

    /**
     * Scope to get published mitra only
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')->orderBy('order');
    }
}
