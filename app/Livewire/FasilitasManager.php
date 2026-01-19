<?php

namespace App\Livewire;

use App\Models\Fasilitas;
use App\Services\ImageService;
use App\Services\MarkdownService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app')]
#[Title('Kelola Fasilitas')]
class FasilitasManager extends Component
{
    use WithFileUploads;

    // List properties
    public $fasilitasList = [];
    public $search = '';
    public $filterStatus = '';
    public $sortField = 'tempat';
    public $sortDirection = 'asc';

    // Form properties
    public $showModal = false;
    public $showDetailModal = false;
    public $isEditMode = false;
    public $fasilitasId = null;
    public $detailFasilitas = null;

    // Form fields
    public $tempat = '';
    public $deskripsi = '';
    public $gambar = null;
    public $currentGambar = null;
    public $fasilitas = [];
    public $newFasilitasItem = '';
    public $status = 'draft';

    protected $listeners = ['refreshFasilitas' => 'loadFasilitas'];

    public function mount()
    {
        $this->loadFasilitas();
    }

    public function loadFasilitas()
    {
        $query = Fasilitas::with('user')
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('tempat', 'like', '%' . $this->search . '%')
                        ->orWhere('deskripsi', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->orderBy($this->sortField, $this->sortDirection);

        $this->fasilitasList = $query->get();
    }

    public function updatedSearch()
    {
        $this->loadFasilitas();
    }

    public function updatedFilterStatus()
    {
        $this->loadFasilitas();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->loadFasilitas();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->isEditMode = false;
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $fasilitasData = Fasilitas::findOrFail($id);
        
        $this->fasilitasId = $fasilitasData->id;
        $this->tempat = $fasilitasData->tempat;
        $this->deskripsi = $fasilitasData->deskripsi ?? '';
        $this->currentGambar = $fasilitasData->gambar;
        $this->fasilitas = $fasilitasData->fasilitas ?? [];
        $this->status = $fasilitasData->status;
        
        $this->isEditMode = true;
        $this->showModal = true;
    }

    public function openDetailModal($id)
    {
        $this->detailFasilitas = Fasilitas::with('user')->findOrFail($id);
        $this->showDetailModal = true;
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->detailFasilitas = null;
    }

    public function addFasilitasItem()
    {
        $this->validate([
            'newFasilitasItem' => 'required|string|max:255',
        ]);

        if (!empty($this->newFasilitasItem)) {
            $this->fasilitas[] = trim($this->newFasilitasItem);
            $this->newFasilitasItem = '';
        }
    }

    public function removeFasilitasItem($index)
    {
        unset($this->fasilitas[$index]);
        $this->fasilitas = array_values($this->fasilitas); // Re-index array
    }

    public function save()
    {
        $this->validate([
            'tempat' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|string',
            'fasilitas' => 'nullable|array',
            'fasilitas.*' => 'string|max:255',
            'status' => 'required|in:draft,published,archived',
        ]);

        try {
            $data = [
                'user_id' => Auth::id(),
                'tempat' => $this->tempat,
                'deskripsi' => $this->deskripsi ?: null,
                'fasilitas' => $this->fasilitas,
                'status' => $this->status,
            ];

            // Handle image upload via ImageService
            if ($this->gambar && str_starts_with($this->gambar, 'data:image')) {
                // Delete old image if editing
                if ($this->isEditMode && $this->currentGambar) {
                    $oldFasilitas = Fasilitas::where('id', (int)$this->fasilitasId)->first();
                    if ($oldFasilitas && $oldFasilitas->gambar) {
                        $imageService = app(ImageService::class);
                        $imageService->delete($oldFasilitas->gambar);
                    }
                }

                // Decode base64 to image
                $base64 = preg_replace('/^data:image\/\w+;base64,/', '', $this->gambar);
                $imageData = base64_decode($base64);
                
                // Create temporary file
                $tempPath = storage_path('app/temp');
                if (!file_exists($tempPath)) {
                    mkdir($tempPath, 0755, true);
                }
                
                $tempFile = $tempPath . '/' . uniqid('crop_') . '.jpg';
                file_put_contents($tempFile, $imageData);
                
                // Create UploadedFile instance
                $uploadedFile = new \Illuminate\Http\UploadedFile(
                    $tempFile,
                    uniqid() . '.jpg',
                    'image/jpeg',
                    null,
                    true
                );

                // Process: compress → WebP (already cropped by Croppie)
                $imageService = app(ImageService::class);
                $data['gambar'] = $imageService->processAndStore(
                    $uploadedFile,
                    'fasilitas'
                );
                
                // Cleanup temp file
                @unlink($tempFile);

                $this->gambar = null;
            }

            // Handle published_at timestamp
            if ($this->status === 'published') {
                $data['published_at'] = now();
            }

            if ($this->isEditMode) {
                $fasilitasData = Fasilitas::findOrFail((int)$this->fasilitasId);
                $fasilitasData->fill($data)->save();
                
                session()->flash('message', 'Fasilitas berhasil diperbarui!');
            } else {
                Fasilitas::create($data);
                session()->flash('message', 'Fasilitas berhasil ditambahkan!');
            }

            $this->closeModal();
            $this->loadFasilitas();

        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $fasilitasData = Fasilitas::where('id', (int)$id)->firstOrFail();
            
            // Delete image if exists
            if ($fasilitasData->gambar) {
                $imageService = app(ImageService::class);
                $imageService->delete($fasilitasData->gambar);
            }
            
            $fasilitasData->delete();
            
            session()->flash('message', 'Fasilitas berhasil dihapus!');
            $this->loadFasilitas();
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus fasilitas: ' . $e->getMessage());
        }
    }

    public function restore($id)
    {
        try {
            $fasilitasData = Fasilitas::withTrashed()->where('id', (int)$id)->firstOrFail();
            $fasilitasData->restore();
            
            session()->flash('message', 'Fasilitas berhasil dipulihkan!');
            $this->loadFasilitas();
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal memulihkan fasilitas: ' . $e->getMessage());
        }
    }

    public function forceDelete($id)
    {
        try {
            $fasilitasData = Fasilitas::withTrashed()->where('id', (int)$id)->firstOrFail();
            
            // Delete image permanently
            if ($fasilitasData->gambar) {
                $imageService = app(ImageService::class);
                $imageService->delete($fasilitasData->gambar);
            }
            
            $fasilitasData->forceDelete();
            
            session()->flash('message', 'Fasilitas berhasil dihapus permanen!');
            $this->loadFasilitas();
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus permanen: ' . $e->getMessage());
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->fasilitasId = null;
        $this->tempat = '';
        $this->deskripsi = '';
        $this->gambar = null;
        $this->currentGambar = null;
        $this->fasilitas = [];
        $this->newFasilitasItem = '';
        $this->status = 'draft';
        $this->isEditMode = false;
    }

    public function render()
    {
        return view('livewire.fasilitas-manager');
    }
}
