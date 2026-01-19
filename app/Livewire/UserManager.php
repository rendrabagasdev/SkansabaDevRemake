<?php

namespace App\Livewire;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Services\ImageService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;

class UserManager extends Component
{
    public $users;
    
    // Form fields
    public $user_id;
    public $name;
    public $email;
    public $password;
    public $role = 'guru';
    public $status = 'active';
    public $photo;
    
    // Filters
    public $search = '';
    public $filterRole = '';
    public $filterStatus = '';
    
    // Modal states
    public $showFormModal = false;
    public $showDetailModal = false;
    public $showDeleteModal = false;
    public $isEditing = false;
    
    // Detail & delete
    public $selectedUser;
    public $userToDelete;
    
    protected $imageService;

    public function boot(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function mount()
    {
        $this->authorize('viewAny', User::class);
        $this->loadUsers();
    }

    public function loadUsers()
    {
        $query = User::query()
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('name', 'ilike', '%' . $this->search . '%')
                        ->orWhere('email', 'ilike', '%' . $this->search . '%');
                });
            })
            ->when($this->filterRole, fn($q) => $q->byRole($this->filterRole))
            ->when($this->filterStatus, fn($q) => $q->byStatus($this->filterStatus))
            ->orderBy('created_at', 'desc');

        $this->users = $query->get();
    }

    #[On('refreshUsers')]
    public function refreshUsers()
    {
        $this->loadUsers();
    }

    public function updatedSearch()
    {
        $this->loadUsers();
    }

    public function updatedFilterRole()
    {
        $this->loadUsers();
    }

    public function updatedFilterStatus()
    {
        $this->loadUsers();
    }

    public function openCreateModal()
    {
        $this->authorize('create', User::class);
        
        $this->reset(['user_id', 'name', 'email', 'password', 'role', 'status', 'photo']);
        $this->role = 'guru';
        $this->status = 'active';
        $this->password = $this->generatePassword();
        $this->isEditing = false;
        $this->showFormModal = true;
    }

    public function openEditModal($id)
    {
        $user = User::findOrFail($id);
        $this->authorize('update', $user);
        
        $this->user_id = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = '';
        $this->role = $user->role;
        $this->status = $user->status;
        $this->photo = null;
        
        $this->isEditing = true;
        $this->showFormModal = true;
    }

    public function openDetailModal($id)
    {
        $this->selectedUser = User::findOrFail($id);
        $this->authorize('view', $this->selectedUser);
        $this->showDetailModal = true;
    }

    public function closeFormModal()
    {
        $this->showFormModal = false;
        $this->reset(['user_id', 'name', 'email', 'password', 'role', 'status', 'photo']);
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->selectedUser = null;
    }

    #[On('imageUpdated')]
    public function handleImageUpdate($data)
    {
        $this->photo = $data['base64'];
    }

    public function save()
    {
        if ($this->isEditing) {
            $this->update();
        } else {
            $this->store();
        }
    }

    public function store()
    {
        $this->authorize('create', User::class);

        $validated = app(StoreUserRequest::class)->validated();

        // Process photo if provided
        if ($this->photo && str_starts_with($this->photo, 'data:image')) {
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $this->photo));
            $tempPath = sys_get_temp_dir() . '/' . uniqid() . '.jpg';
            file_put_contents($tempPath, $imageData);
            
            $uploadedFile = new \Illuminate\Http\UploadedFile(
                $tempPath,
                'photo.jpg',
                mime_content_type($tempPath),
                null,
                true
            );
            
            $validated['photo'] = $this->imageService->processAndStore(
                $uploadedFile,
                'users',
                ['width' => 500, 'height' => 500]
            );
            
            @unlink($tempPath);
        }

        // Hash password
        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        $this->closeFormModal();
        $this->loadUsers();
        
        session()->flash('message', 'User berhasil ditambahkan.');
    }

    public function update()
    {
        $user = User::findOrFail($this->user_id);
        $this->authorize('update', $user);

        $validated = app(UpdateUserRequest::class)->setUserResolver(fn() => auth()->user())->validated();

        // Process photo if provided
        if ($this->photo && str_starts_with($this->photo, 'data:image')) {
            // Delete old photo
            if ($user->photo) {
                $this->imageService->delete($user->photo);
            }

            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $this->photo));
            $tempPath = sys_get_temp_dir() . '/' . uniqid() . '.jpg';
            file_put_contents($tempPath, $imageData);
            
            $uploadedFile = new \Illuminate\Http\UploadedFile(
                $tempPath,
                'photo.jpg',
                mime_content_type($tempPath),
                null,
                true
            );
            
            $validated['photo'] = $this->imageService->processAndStore(
                $uploadedFile,
                'users',
                ['width' => 500, 'height' => 500]
            );
            
            @unlink($tempPath);
        }

        // Update password only if provided
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        $this->closeFormModal();
        $this->loadUsers();
        
        session()->flash('message', 'User berhasil diperbarui.');
    }

    public function confirmDelete($id)
    {
        $this->userToDelete = User::findOrFail($id);
        $this->authorize('delete', $this->userToDelete);
        
        // Check if trying to delete self
        if ($this->userToDelete->id === auth()->id()) {
            session()->flash('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
            return;
        }
        
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        if (!$this->userToDelete) {
            return;
        }

        $this->authorize('delete', $this->userToDelete);

        // Delete photo if exists
        if ($this->userToDelete->photo) {
            $this->imageService->delete($this->userToDelete->photo);
        }

        $this->userToDelete->delete();

        $this->showDeleteModal = false;
        $this->userToDelete = null;
        $this->loadUsers();
        
        session()->flash('message', 'User berhasil dihapus.');
    }

    public function cancelDelete()
    {
        $this->showDeleteModal = false;
        $this->userToDelete = null;
    }

    public function generatePassword()
    {
        return Str::random(12);
    }

    public function regeneratePassword()
    {
        $this->password = $this->generatePassword();
    }

    public function render()
    {
        return view('livewire.user-manager')->layout('layouts.app');
    }
}
