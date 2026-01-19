<?php

namespace App\Livewire;

use App\Models\Galeri;
use App\Services\ImageService;
use App\Services\MarkdownService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Kelola Galeri')]
class GaleriManager extends Component
{
    // List properties
    public $galeriList = [];
    public $search = '';
    public $filterStatus = '';
    public $filterKategori = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    // Form properties
    public $showModal = false;
    public $showDetailModal = false;
    public $isEditMode = false;
    public $galeriId = null;
    public $detailGaleri = null;

    // Form fields
    public $judul = '';
    public $deskripsi = '';
    public $gambar = null;
    public $currentGambar = null;
    public $kategori = 'kegiatan';
    public $status = 'draft';

    protected $listeners = ['refreshGaleri' => 'loadGaleri'];

    public function mount()
    {
        $this->loadGaleri();
    }

    public function loadGaleri()
    {
        $query = Galeri::with('user')
            ->when($this->search, function ($q) {
                $q->where('judul', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterKategori, fn($q) => $q->where('kategori', $this->filterKategori))
            ->orderBy($this->sortField, $this->sortDirection);

        $this->galeriList = $query->get();
    }

    public function updatedSearch()
    {
        $this->loadGaleri();
    }

    public function updatedFilterStatus()
    {
        $this->loadGaleri();
    }

    public function updatedFilterKategori()
    {
        $this->loadGaleri();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->loadGaleri();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->isEditMode = false;
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $galeri = Galeri::findOrFail((int)$id);
        
        $this->galeriId = $galeri->id;
        $this->judul = $galeri->judul;
        $this->deskripsi = $galeri->deskripsi ?? '';
        $this->currentGambar = $galeri->gambar;
        $this->kategori = $galeri->kategori;
        $this->status = $galeri->status;
        $this->isEditMode = true;
        $this->showModal = true;
    }

    public function openDetailModal($id)
    {
        $this->detailGaleri = Galeri::with('user')->findOrFail((int)$id);
        $this->showDetailModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->detailGaleri = null;
    }

    public function save()
    {
        if ($this->isEditMode) {
            $this->update();
        } else {
            $this->store();
        }
    }

    public function store()
    {
        $this->validate([
            'judul' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'in:kegiatan,lomba,pembelajaran,kunjungan,lainnya'],
            'deskripsi' => ['nullable', 'string'],
            'gambar' => ['nullable', 'string'],
            'status' => ['required', 'in:draft,published,archived'],
        ]);

        // Manual check for gambar on create
        if (!$this->gambar || !str_starts_with($this->gambar, 'data:image')) {
            $this->addError('gambar', 'Gambar wajib diisi.');
            return;
        }

        $data = [
            'user_id' => Auth::id(),
            'judul' => $this->judul,
            'kategori' => $this->kategori,
            'deskripsi' => $this->deskripsi,
            'status' => $this->status,
            'published_at' => $this->status === 'published' ? now() : null,
        ];

        // Handle image upload via ImageService (16:9 ratio)
        if ($this->gambar && str_starts_with($this->gambar, 'data:image')) {
            $base64 = preg_replace('/^data:image\/\w+;base64,/', '', $this->gambar);
            $imageData = base64_decode($base64);
            
            $tempPath = storage_path('app/temp');
            if (!file_exists($tempPath)) {
                mkdir($tempPath, 0755, true);
            }
            
            $tempFile = $tempPath . '/' . uniqid('crop_') . '.jpg';
            file_put_contents($tempFile, $imageData);
            
            $uploadedFile = new \Illuminate\Http\UploadedFile(
                $tempFile,
                uniqid() . '.jpg',
                'image/jpeg',
                null,
                true
            );

            $imageService = app(ImageService::class);
            $data['gambar'] = $imageService->processAndStore(
                $uploadedFile,
                'galeri',
                ['width' => 1600, 'height' => 900]
            );
            
            @unlink($tempFile);
        }

        Galeri::create($data);

        $this->closeModal();
        $this->loadGaleri();
        session()->flash('message', 'Galeri berhasil ditambahkan.');
    }

    public function update()
    {
        $this->validate([
            'judul' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'in:kegiatan,lomba,pembelajaran,kunjungan,lainnya'],
            'deskripsi' => ['nullable', 'string'],
            'gambar' => ['nullable', 'string'],
            'status' => ['required', 'in:draft,published,archived'],
        ]);

        $galeri = Galeri::findOrFail((int)$this->galeriId);

        $data = [
            'judul' => $this->judul,
            'kategori' => $this->kategori,
            'deskripsi' => $this->deskripsi,
            'status' => $this->status,
        ];

        // Handle image upload if new image provided
        if ($this->gambar && str_starts_with($this->gambar, 'data:image')) {
            // Delete old image
            if ($galeri->gambar) {
                app(ImageService::class)->delete($galeri->gambar);
            }

            $base64 = preg_replace('/^data:image\/\w+;base64,/', '', $this->gambar);
            $imageData = base64_decode($base64);
            
            $tempPath = storage_path('app/temp');
            if (!file_exists($tempPath)) {
                mkdir($tempPath, 0755, true);
            }
            
            $tempFile = $tempPath . '/' . uniqid('crop_') . '.jpg';
            file_put_contents($tempFile, $imageData);
            
            $uploadedFile = new \Illuminate\Http\UploadedFile(
                $tempFile,
                uniqid() . '.jpg',
                'image/jpeg',
                null,
                true
            );

            $imageService = app(ImageService::class);
            $data['gambar'] = $imageService->processAndStore(
                $uploadedFile,
                'galeri',
                ['width' => 1600, 'height' => 900]
            );
            
            @unlink($tempFile);
        }

        // Update published_at based on status
        if ($this->status === 'published' && $galeri->status !== 'published') {
            $data['published_at'] = now();
        } elseif ($this->status !== 'published') {
            $data['published_at'] = null;
        }

        $galeri->fill($data)->save();

        $this->closeModal();
        $this->loadGaleri();
        session()->flash('message', 'Galeri berhasil diperbarui.');
    }

    public function delete($id)
    {
        $galeri = Galeri::findOrFail((int)$id);
        
        // Delete image
        if ($galeri->gambar) {
            app(ImageService::class)->delete($galeri->gambar);
        }

        $galeri->delete();

        $this->loadGaleri();
        session()->flash('message', 'Galeri berhasil dihapus (soft delete).');
    }

    public function restore($id)
    {
        $galeri = Galeri::withTrashed()->findOrFail((int)$id);
        $galeri->restore();

        $this->loadGaleri();
        session()->flash('message', 'Galeri berhasil dipulihkan.');
    }

    public function forceDelete($id)
    {
        $galeri = Galeri::withTrashed()->findOrFail((int)$id);

        // Delete image from storage
        if ($galeri->gambar) {
            app(ImageService::class)->delete($galeri->gambar);
        }

        $galeri->forceDelete();

        $this->loadGaleri();
        session()->flash('message', 'Galeri berhasil dihapus permanen.');
    }

    private function resetForm()
    {
        $this->reset([
            'galeriId',
            'judul',
            'deskripsi',
            'gambar',
            'currentGambar',
            'kategori',
            'status',
        ]);
        $this->kategori = 'kegiatan';
        $this->status = 'draft';
    }

    public function render()
    {
        return view('livewire.galeri-manager');
    }
}
