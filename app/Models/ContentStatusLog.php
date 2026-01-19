<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentStatusLog extends Model
{
    protected $fillable = [
        'model_type',
        'model_id',
        'from_status',
        'to_status',
        'user_id',
        'changed_at'
    ];

    protected $casts = [
        'changed_at' => 'datetime'
    ];

    /**
     * Get the model that owns the log
     */
    public function model()
    {
        return $this->morphTo();
    }

    /**
     * Get the user who made the change
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * Get human-readable from status
     */
    public function getFromStatusLabelAttribute(): string
    {
        return $this->getStatusLabel($this->from_status);
    }

    /**
     * Get human-readable to status
     */
    public function getToStatusLabelAttribute(): string
    {
        return $this->getStatusLabel($this->to_status);
    }

    /**
     * Get status label
     */
    protected function getStatusLabel(?string $status): string
    {
        if (!$status) {
            return 'None';
        }

        return match($status) {
            'draft' => 'Draft',
            'review' => 'In Review',
            'published' => 'Published',
            'archived' => 'Archived',
            default => ucfirst($status)
        };
    }

    /**
     * Get formatted change description
     */
    public function getChangeDescriptionAttribute(): string
    {
        $from = $this->from_status_label;
        $to = $this->to_status_label;
        $user = $this->user ? $this->user->name : 'System';

        return "{$user} changed status from {$from} to {$to}";
    }
}
