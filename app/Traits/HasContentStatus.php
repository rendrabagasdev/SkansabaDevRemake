<?php

namespace App\Traits;

use App\Models\ContentStatusLog;
use Illuminate\Database\Eloquent\SoftDeletes;

trait HasContentStatus
{
    use SoftDeletes;

    /**
     * Boot the trait
     */
    protected static function bootHasContentStatus(): void
    {
        // Set default status to draft on creation
        static::creating(function ($model) {
            if (empty($model->status)) {
                $model->status = 'draft';
            }
        });

        // Log status changes
        static::updating(function ($model) {
            if ($model->isDirty('status')) {
                $model->logStatusChange($model->getOriginal('status'), $model->status);
            }
        });
    }

    /**
     * Get all valid content statuses
     */
    public static function getStatuses(): array
    {
        return ['draft', 'review', 'published', 'archived'];
    }

    /**
     * Get public-accessible statuses
     */
    public static function getPublicStatuses(): array
    {
        return ['published'];
    }

    /**
     * Get editable statuses
     */
    public static function getEditableStatuses(): array
    {
        return ['draft', 'review'];
    }

    /**
     * Get locked statuses
     */
    public static function getLockedStatuses(): array
    {
        return ['archived'];
    }

    /**
     * Check if content is in draft
     */
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Check if content is in review
     */
    public function isInReview(): bool
    {
        return $this->status === 'review';
    }

    /**
     * Check if content is published
     */
    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    /**
     * Check if content is archived
     */
    public function isArchived(): bool
    {
        return $this->status === 'archived';
    }

    /**
     * Check if content is editable
     */
    public function isEditable(): bool
    {
        return in_array($this->status, self::getEditableStatuses());
    }

    /**
     * Check if content is locked
     */
    public function isLocked(): bool
    {
        return in_array($this->status, self::getLockedStatuses());
    }

    /**
     * Check if content is visible to public
     */
    public function isPublicVisible(): bool
    {
        return in_array($this->status, self::getPublicStatuses());
    }

    /**
     * Scope: Get only draft content
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope: Get only content in review
     */
    public function scopeInReview($query)
    {
        return $query->where('status', 'review');
    }

    /**
     * Scope: Get only published content
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope: Get only archived content
     */
    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }

    /**
     * Scope: Get publicly visible content
     */
    public function scopePublicVisible($query)
    {
        return $query->whereIn('status', self::getPublicStatuses());
    }

    /**
     * Scope: Get editable content
     */
    public function scopeEditable($query)
    {
        return $query->whereIn('status', self::getEditableStatuses());
    }

    /**
     * Transition to review status
     */
    public function moveToReview(): bool
    {
        if (!$this->isDraft()) {
            throw new \Exception('Only draft content can be moved to review');
        }

        return $this->updateStatus('review');
    }

    /**
     * Transition to published status (admin only)
     */
    public function publish(bool $isAdmin = false): bool
    {
        if (!$isAdmin) {
            throw new \Exception('Only administrators can publish content');
        }

        if (!$this->isInReview()) {
            throw new \Exception('Only reviewed content can be published');
        }

        return $this->updateStatus('published');
    }

    /**
     * Transition to archived status (admin only)
     */
    public function archive(bool $isAdmin = false): bool
    {
        if (!$isAdmin) {
            throw new \Exception('Only administrators can archive content');
        }

        if (!$this->isPublished()) {
            throw new \Exception('Only published content can be archived');
        }

        return $this->updateStatus('archived');
    }

    /**
     * Rollback to previous status
     */
    public function rollback(): bool
    {
        $lastLog = $this->statusLogs()->latest()->first();

        if (!$lastLog) {
            throw new \Exception('No status history found for rollback');
        }

        return $this->updateStatus($lastLog->from_status, isRollback: true);
    }

    /**
     * Update status with validation
     */
    protected function updateStatus(string $newStatus, bool $isRollback = false): bool
    {
        // Validate status
        if (!in_array($newStatus, self::getStatuses())) {
            throw new \Exception("Invalid status: {$newStatus}");
        }

        // Prevent direct publish without review
        if ($newStatus === 'published' && $this->status === 'draft') {
            throw new \Exception('Content must be reviewed before publishing');
        }

        $oldStatus = $this->status;
        $this->status = $newStatus;
        $result = $this->save();

        return $result;
    }

    /**
     * Log status change
     */
    protected function logStatusChange(?string $fromStatus, string $toStatus): void
    {
        ContentStatusLog::create([
            'model_type' => get_class($this),
            'model_id' => $this->id,
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'user_id' => auth()->id(),
            'changed_at' => now()
        ]);
    }

    /**
     * Get status logs relationship
     */
    public function statusLogs()
    {
        return $this->morphMany(ContentStatusLog::class, 'model');
    }

    /**
     * Get human-readable status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'draft' => 'Draft',
            'review' => 'In Review',
            'published' => 'Published',
            'archived' => 'Archived',
            default => ucfirst($this->status)
        };
    }

    /**
     * Get status badge color class
     */
    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status) {
            'draft' => 'gray',
            'review' => 'yellow',
            'published' => 'green',
            'archived' => 'red',
            default => 'gray'
        };
    }

    /**
     * Get status badge HTML
     */
    public function getStatusBadgeAttribute(): string
    {
        $color = $this->status_badge_color;
        $label = $this->status_label;

        $colorClasses = [
            'gray' => 'bg-gray-100 text-gray-800',
            'yellow' => 'bg-yellow-100 text-yellow-800',
            'green' => 'bg-green-100 text-green-800',
            'red' => 'bg-red-100 text-red-800',
        ];

        $class = $colorClasses[$color] ?? $colorClasses['gray'];

        return "<span class=\"inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {$class}\">{$label}</span>";
    }
}
