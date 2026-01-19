<?php

namespace App\Livewire;

use App\Models\Alumni;
use App\Services\ImageService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Kelola Alumni')]
class AlumniManager extends Component
{
    // List properties
    public $alumniList = [];
    public $search = '';
    public $filterStatus = '';
    public $filterStatusAlumni = '';
    public $filterTahunLulus = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    // Form properties
    public $showModal = false;
    public $showDetailModal = false;
    public $isEditMode = false;
    public $alumniId = null;
    public $detailAlumni = null;

    // Form fields
    public $nama = '';
    public $tahun_lulus = '';
    public $status_alumni = 'belum_diketahui';
    public $institusi = '';
    public $bidang = '';
    public $deskripsi = '';
    public $foto = null;
    public $currentFoto = null;
    public $status = 'draft';

    protected $listeners = ['refreshAlumni' => 'loadAlumni'];

    public function mount()
    {
        $this->tahun_lulus = date('Y');
        $this->loadAlumni();
    }

    public function loadAlumni()
    {
        $query = Alumni::with('user')
            ->when($this->search, function ($q) {
                $q->where('nama', 'like', '%' . $this->search . '%')
                  ->orWhere('institusi', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterStatusAlumni, fn($q) => $q->where('status_alumni', $this->filterStatusAlumni))
            ->when($this->filterTahunLulus, fn($q) => $q->where('tahun_lulus', $this->filterTahunLulus))
            ->orderBy($this->sortField, $this->sortDirection);

        $this->alumniList = $query->get();
    }

    public function updatedSearch()
    {
        $this->loadAlumni();
    }

    public function updatedFilterStatus()
    {
        $this->loadAlumni();
    }

    public function updatedFilterStatusAlumni()
    {
        $this->loadAlumni();
    }

    public function updatedFilterTahunLulus()
    {
        $this->loadAlumni();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->loadAlumni();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->isEditMode = false;
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $alumni = Alumni::findOrFail((int)$id);
        
        $this->alumniId = $alumni->id;
        $this->nama = $alumni->nama;
        $this->tahun_lulus = $alumni->tahun_lulus;
        $this->status_alumni = $alumni->status_alumni;
        $this->institusi = $alumni->institusi ?? '';
        $this->bidang = $alumni->bidang ?? '';
        $this->deskripsi = $alumni->deskripsi ?? '';
        $this->currentFoto = $alumni->foto;
        $this->status = $alumni->status;
        
        $this->isEditMode = true;
        $this->showModal = true;
    }

    public function openDetailModal($id)
    {
        $this->detailAlumni = Alumni::with('user')->findOrFail((int)$id);
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
        $this->detailAlumni = null;
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
            'nama' => ['required', 'string', 'max:255'],
            'tahun_lulus' => ['required', 'integer', 'min:1990', 'max:' . (date('Y') + 1)],
            'status_alumni' => ['required', 'in:kuliah,kerja,wirausaha,belum_diketahui'],
            'institusi' => ['nullable', 'string', 'max:255'],
            'bidang' => ['nullable', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'foto' => ['nullable', 'string'],
            'status' => ['required', 'in:draft,published,archived'],
        ]);

        // Manual validation for institusi based on status_alumni
        if (in_array($this->status_alumni, ['kuliah', 'kerja', 'wirausaha']) && empty($this->institusi)) {
            $this->addError('institusi', 'Institusi wajib diisi untuk status kuliah, kerja, atau wirausaha.');
            return;
        }

        $data = [
            'user_id' => Auth::id(),
            'nama' => $this->nama,
            'tahun_lulus' => $this->tahun_lulus,
            'status_alumni' => $this->status_alumni,
            'institusi' => $this->institusi,
            'bidang' => $this->bidang,
            'deskripsi' => $this->deskripsi,
            'status' => $this->status,
            'published_at' => $this->status === 'published' ? now() : null,
        ];

        // Handle image upload via ImageService (1:1 ratio, 800x800)
        if ($this->foto && str_starts_with($this->foto, 'data:image')) {
            $base64 = preg_replace('/^data:image\/\w+;base64,/', '', $this->foto);
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
            $data['foto'] = $imageService->processAndStore(
                $uploadedFile,
                'alumni',
                ['width' => 800, 'height' => 800]
            );
            
            @unlink($tempFile);
            $this->foto = null;
        }

        Alumni::create($data);

        $this->closeModal();
        $this->loadAlumni();
        session()->flash('message', 'Data alumni berhasil ditambahkan.');
    }

    public function update()
    {
        $this->validate([
            'nama' => ['required', 'string', 'max:255'],
            'tahun_lulus' => ['required', 'integer', 'min:1990', 'max:' . (date('Y') + 1)],
            'status_alumni' => ['required', 'in:kuliah,kerja,wirausaha,belum_diketahui'],
            'institusi' => ['nullable', 'string', 'max:255'],
            'bidang' => ['nullable', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'foto' => ['nullable', 'string'],
            'status' => ['required', 'in:draft,published,archived'],
        ]);

        // Manual validation for institusi
        if (in_array($this->status_alumni, ['kuliah', 'kerja', 'wirausaha']) && empty($this->institusi)) {
            $this->addError('institusi', 'Institusi wajib diisi untuk status kuliah, kerja, atau wirausaha.');
            return;
        }

        $alumni = Alumni::findOrFail((int)$this->alumniId);

        $data = [
            'nama' => $this->nama,
            'tahun_lulus' => $this->tahun_lulus,
            'status_alumni' => $this->status_alumni,
            'institusi' => $this->institusi,
            'bidang' => $this->bidang,
            'deskripsi' => $this->deskripsi,
            'status' => $this->status,
        ];

        // Handle image upload if new image provided
        if ($this->foto && str_starts_with($this->foto, 'data:image')) {
            // Delete old image
            if ($alumni->foto) {
                app(ImageService::class)->delete($alumni->foto);
            }

            $base64 = preg_replace('/^data:image\/\w+;base64,/', '', $this->foto);
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
            $data['foto'] = $imageService->processAndStore(
                $uploadedFile,
                'alumni',
                ['width' => 800, 'height' => 800]
            );
            
            @unlink($tempFile);
        }

        // Update published_at based on status
        if ($this->status === 'published' && $alumni->status !== 'published') {
            $data['published_at'] = now();
        } elseif ($this->status !== 'published') {
            $data['published_at'] = null;
        }

        $alumni->fill($data)->save();

        $this->closeModal();
        $this->loadAlumni();
        session()->flash('message', 'Data alumni berhasil diperbarui.');
    }

    public function delete($id)
    {
        $alumni = Alumni::findOrFail((int)$id);

        // Delete foto if exists
        if ($alumni->foto) {
            app(ImageService::class)->delete($alumni->foto);
        }

        $alumni->delete();

        $this->loadAlumni();
        session()->flash('message', 'Data alumni berhasil dihapus.');
    }

    private function resetForm()
    {
        $this->reset([
            'alumniId',
            'nama',
            'tahun_lulus',
            'status_alumni',
            'institusi',
            'bidang',
            'deskripsi',
            'foto',
            'currentFoto',
            'status',
        ]);
        $this->tahun_lulus = date('Y');
        $this->status_alumni = 'belum_diketahui';
        $this->status = 'draft';
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.alumni-manager');
    }
}
