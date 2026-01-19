<?php

namespace App\Livewire\Components;

use App\Services\ContentStatusService;
use Livewire\Component;

class ContentStatusToggle extends Component
{
    public $model;
    public $showTransitions = false;
    public $confirmingTransition = false;
    public $pendingStatus = null;
    public $pendingAction = null;

    protected $contentStatusService;

    public function boot(ContentStatusService $contentStatusService)
    {
        $this->contentStatusService = $contentStatusService;
    }

    public function mount($model)
    {
        $this->model = $model;
    }

    public function toggleTransitions()
    {
        $this->showTransitions = !$this->showTransitions;
    }

    public function initiateTransition($status, $action)
    {
        $this->pendingStatus = $status;
        $this->pendingAction = $action;
        $this->confirmingTransition = true;
    }

    public function confirmTransition()
    {
        try {
            $action = $this->pendingAction;
            $isAdmin = $this->isUserAdmin();

            // Call the appropriate method on the model
            switch ($action) {
                case 'moveToReview':
                    $this->model->moveToReview();
                    $message = 'Content moved to review successfully';
                    break;

                case 'publish':
                    $this->model->publish($isAdmin);
                    $message = 'Content published successfully';
                    break;

                case 'archive':
                    $this->model->archive($isAdmin);
                    $message = 'Content archived successfully';
                    break;

                case 'rollback':
                    $this->model->rollback();
                    $message = 'Status rolled back successfully';
                    break;

                default:
                    throw new \Exception('Invalid action');
            }

            $this->model->refresh();
            $this->dispatch('status-changed', status: $this->model->status);
            session()->flash('message', $message);

        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }

        $this->cancelTransition();
    }

    public function cancelTransition()
    {
        $this->confirmingTransition = false;
        $this->pendingStatus = null;
        $this->pendingAction = null;
    }

    public function getAvailableTransitions()
    {
        return $this->contentStatusService->getAvailableTransitions(
            $this->model,
            auth()->user()
        );
    }

    protected function isUserAdmin(): bool
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }

        return method_exists($user, 'isAdmin') ? $user->isAdmin() : false;
    }

    public function render()
    {
        return view('livewire.components.content-status-toggle', [
            'availableTransitions' => $this->getAvailableTransitions()
        ]);
    }
}
