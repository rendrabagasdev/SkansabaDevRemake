<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImageProcessingJob extends Model
{
    protected $fillable = [
        'original_filename',
        'temp_path',
        'storage_path',
        'options',
        'status',
        'output_path',
        'fallback_path',
        'fallback_used',
        'error_message',
        'queued_at',
        'completed_at',
        'failed_at'
    ];

    protected $casts = [
        'options' => 'array',
        'fallback_used' => 'boolean',
        'queued_at' => 'datetime',
        'completed_at' => 'datetime',
        'failed_at' => 'datetime'
    ];

    /**
     * Check if job is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if job is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if job is processing
     */
    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }

    /**
     * Check if job has failed
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Get the final image path (output or fallback)
     */
    public function getImagePath(): ?string
    {
        if ($this->isCompleted() && $this->output_path) {
            return $this->output_path;
        }

        if ($this->fallback_used && $this->fallback_path) {
            return $this->fallback_path;
        }

        return null;
    }

    /**
     * Get public URL for the image
     */
    public function getImageUrl(): ?string
    {
        $path = $this->getImagePath();
        
        if (!$path) {
            return null;
        }

        return asset('storage/' . $path);
    }

    /**
     * Scope: Get pending jobs
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: Get processing jobs
     */
    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    /**
     * Scope: Get completed jobs
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope: Get failed jobs
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }
}
