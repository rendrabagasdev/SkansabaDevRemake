<?php

namespace App\Services;

use App\Models\ContentStatusLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class ContentStatusService
{
    /**
     * Validate if user can change status
     */
    public function canChangeStatus(Model $model, string $newStatus, $user = null): bool
    {
        $user = $user ?? auth()->user();

        // Admin can do anything
        if ($this->isAdmin($user)) {
            return true;
        }

        // Non-admin restrictions
        if ($newStatus === 'published' || $newStatus === 'archived') {
            return false;
        }

        return true;
    }

    /**
     * Change content status with full validation
     */
    public function changeStatus(Model $model, string $newStatus, $user = null): bool
    {
        $user = $user ?? auth()->user();
        $oldStatus = $model->status;

        // Validate transition
        if (!$this->isValidTransition($oldStatus, $newStatus)) {
            Log::warning('Invalid status transition attempt', [
                'model' => get_class($model),
                'id' => $model->id,
                'from' => $oldStatus,
                'to' => $newStatus,
                'user' => $user?->id
            ]);
            
            throw new \Exception("Invalid status transition from {$oldStatus} to {$newStatus}");
        }

        // Check permissions
        if (!$this->canChangeStatus($model, $newStatus, $user)) {
            throw new \Exception('Insufficient permissions to change status');
        }

        // Perform transition
        try {
            $model->status = $newStatus;
            $result = $model->save();

            if ($result) {
                Log::info('Status changed successfully', [
                    'model' => get_class($model),
                    'id' => $model->id,
                    'from' => $oldStatus,
                    'to' => $newStatus,
                    'user' => $user?->id
                ]);
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Status change failed', [
                'model' => get_class($model),
                'id' => $model->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Check if transition is valid
     */
    public function isValidTransition(string $from, string $to): bool
    {
        $allowedTransitions = [
            'draft' => ['review'],
            'review' => ['published', 'draft'], // Can rollback to draft
            'published' => ['archived'],
            'archived' => ['published'], // Can rollback
        ];

        return isset($allowedTransitions[$from]) && in_array($to, $allowedTransitions[$from]);
    }

    /**
     * Get available transitions for current status
     */
    public function getAvailableTransitions(Model $model, $user = null): array
    {
        $user = $user ?? auth()->user();
        $isAdmin = $this->isAdmin($user);
        $currentStatus = $model->status;

        $transitions = [];

        switch ($currentStatus) {
            case 'draft':
                $transitions[] = [
                    'status' => 'review',
                    'label' => 'Move to Review',
                    'action' => 'moveToReview',
                    'color' => 'yellow',
                    'icon' => '📝',
                    'requires_admin' => false
                ];
                break;

            case 'review':
                if ($isAdmin) {
                    $transitions[] = [
                        'status' => 'published',
                        'label' => 'Publish',
                        'action' => 'publish',
                        'color' => 'green',
                        'icon' => '✅',
                        'requires_admin' => true
                    ];
                }
                $transitions[] = [
                    'status' => 'draft',
                    'label' => 'Back to Draft',
                    'action' => 'rollback',
                    'color' => 'gray',
                    'icon' => '↩️',
                    'requires_admin' => false
                ];
                break;

            case 'published':
                if ($isAdmin) {
                    $transitions[] = [
                        'status' => 'archived',
                        'label' => 'Archive',
                        'action' => 'archive',
                        'color' => 'red',
                        'icon' => '📦',
                        'requires_admin' => true
                    ];
                }
                break;

            case 'archived':
                if ($isAdmin) {
                    $transitions[] = [
                        'status' => 'published',
                        'label' => 'Restore to Published',
                        'action' => 'rollback',
                        'color' => 'green',
                        'icon' => '↩️',
                        'requires_admin' => true
                    ];
                }
                break;
        }

        return $transitions;
    }

    /**
     * Check if user is admin
     */
    protected function isAdmin($user): bool
    {
        if (!$user) {
            return false;
        }

        // Implement your admin check logic
        // Example: return $user->hasRole('admin');
        return method_exists($user, 'isAdmin') ? $user->isAdmin() : false;
    }

    /**
     * Get status history for a model
     */
    public function getStatusHistory(Model $model): \Illuminate\Support\Collection
    {
        return ContentStatusLog::where('model_type', get_class($model))
            ->where('model_id', $model->id)
            ->with('user')
            ->orderByDesc('changed_at')
            ->get();
    }

    /**
     * Get status statistics
     */
    public function getStatistics(string $modelClass): array
    {
        $model = new $modelClass;

        return [
            'draft' => $model->draft()->count(),
            'review' => $model->inReview()->count(),
            'published' => $model->published()->count(),
            'archived' => $model->archived()->count(),
            'total' => $model->count()
        ];
    }

    /**
     * Bulk update status
     */
    public function bulkUpdateStatus(array $modelIds, string $modelClass, string $newStatus, $user = null): array
    {
        $user = $user ?? auth()->user();
        $results = [
            'success' => [],
            'failed' => []
        ];

        $models = $modelClass::whereIn('id', $modelIds)->get();

        foreach ($models as $model) {
            try {
                $this->changeStatus($model, $newStatus, $user);
                $results['success'][] = $model->id;
            } catch (\Exception $e) {
                $results['failed'][] = [
                    'id' => $model->id,
                    'error' => $e->getMessage()
                ];
            }
        }

        return $results;
    }
}
