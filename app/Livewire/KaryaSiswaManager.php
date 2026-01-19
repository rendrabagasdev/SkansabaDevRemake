<?php

namespace App\Livewire;

use App\Models\KaryaSiswa;
use App\Services\ImageService;
use App\Services\MarkdownService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app')]
#[Title('Kelola Karya Siswa')]
class KaryaSiswaManager extends Component
{
    use WithFileUploads;

    // List properties
    public $karyaSiswas = [];
    public $search = '';
    public $filterStatus = '';
    public $filterKategori = '';
    public $filterTahun = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    // Form properties
    public $showModal = false;
    public $showDetailModal = false;
    public $isEditMode = false;
    public $karyaSiswaId = null;
    public $detailKarya = null;

    // Form fields
    public $judul = '';
    public $deskripsi = '';
    public $kategori = '';
    public $teknologi = '';
    public $nama_siswa = '';
    public $kelas = '';
    public $tahun = '';
    public $gambar = null;
    public $currentGambar = null;
    public $url_demo = '';
    public $url_repo = '';
    public $status = 'draft';

    protected $listeners = ['refreshKaryaSiswas' => 'loadKaryaSiswas'];

    public function mount()
    {
        $this->tahun = date('Y');
        $this->loadKaryaSiswas();
    }

    public function loadKaryaSiswas()
    {
        $query = KaryaSiswa::with('user')
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('judul', 'like', '%' . $this->search . '%')
                        ->orWhere('nama_siswa', 'like', '%' . $this->search . '%')
                        ->orWhere('kelas', 'like', '%' . $this->search . '%')
                        ->orWhere('teknologi', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterKategori, fn($q) => $q->where('kategori', $this->filterKategori))
            ->when($this->filterTahun, fn($q) => $q->where('tahun', $this->filterTahun))
            ->orderBy($this->sortField, $this->sortDirection);

        $this->karyaSiswas = $query->get();
    }

    public function updatedSearch()
    {
        $this->loadKaryaSiswas();
    }

    public function updatedFilterStatus()
    {
        $this->loadKaryaSiswas();
    }

    public function updatedFilterKategori()
    {
        $this->loadKaryaSiswas();
    }

    public function updatedFilterTahun()
    {
        $this->loadKaryaSiswas();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->loadKaryaSiswas();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->isEditMode = false;
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $karya = KaryaSiswa::findOrFail($id);
        
        $this->karyaSiswaId = $karya->id;
        $this->judul = $karya->judul;
        $this->deskripsi = $karya->deskripsi;
        $this->kategori = $karya->kategori;
        $this->teknologi = $karya->teknologi;
        $this->nama_siswa = $karya->nama_siswa;
        $this->kelas = $karya->kelas;
        $this->tahun = $karya->tahun;
        $this->currentGambar = $karya->gambar;
        $this->url_demo = $karya->url_demo ?? '';
        $this->url_repo = $karya->url_repo ?? '';
        $this->status = $karya->status;
        
        $this->isEditMode = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string|min:10',
            'kategori' => 'required|string|max:255',
            'teknologi' => 'required|string|max:255',
            'nama_siswa' => 'required|string|max:255',
            'kelas' => 'required|string|max:255',
            'tahun' => 'required|integer|min:2000|max:' . (date('Y') + 1),
            'gambar' => 'nullable|string',
            'url_demo' => 'nullable|url|max:255',
            'url_repo' => 'nullable|url|max:255',
            'status' => 'required|in:draft,review,published,archived',
        ]);

        try {
            $data = [
                'user_id' => Auth::id(),
                'judul' => $this->judul,
                'deskripsi' => $this->deskripsi,
                'kategori' => $this->kategori,
                'teknologi' => $this->teknologi,
                'nama_siswa' => $this->nama_siswa,
                'kelas' => $this->kelas,
                'tahun' => $this->tahun,
                'url_demo' => $this->url_demo ?: null,
                'url_repo' => $this->url_repo ?: null,
                'status' => $this->status,
            ];

            // Handle image upload via ImageService
            if ($this->gambar && str_starts_with($this->gambar, 'data:image')) {
                // Delete old image if editing
                if ($this->isEditMode && $this->currentGambar) {
                    $oldKarya = KaryaSiswa::find((int)$this->karyaSiswaId);
                    if ($oldKarya && $oldKarya->gambar) {
                        $imageService = app(ImageService::class);
                        $imageService->delete($oldKarya->gambar);
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
                    'karya-siswa'
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
                $karya = KaryaSiswa::findOrFail((int)$this->karyaSiswaId);
                $karya->fill($data)->save();
                
                session()->flash('message', 'Karya siswa berhasil diperbarui!');
            } else {
                KaryaSiswa::create($data);
                session()->flash('message', 'Karya siswa berhasil ditambahkan!');
            }

            $this->closeModal();
            $this->loadKaryaSiswas();

        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $karya = KaryaSiswa::findOrFail((int)$id);
            
            // Delete image
            if ($karya->gambar) {
                app(ImageService::class)->delete($karya->gambar);
            }

            $karya->delete();
            
            session()->flash('message', 'Karya siswa berhasil dihapus!');
            $this->loadKaryaSiswas();

        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function restore($id)
    {
        try {
            KaryaSiswa::withTrashed()->findOrFail($id)->restore();
            session()->flash('message', 'Karya siswa berhasil dipulihkan!');
            $this->loadKaryaSiswas();
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function forceDelete($id)
    {
        try {
            $karya = KaryaSiswa::withTrashed()->findOrFail($id);
            
            // Delete image permanently
            if ($karya->gambar) {
                app(ImageService::class)->delete($karya->gambar);
            }

            $karya->forceDelete();
            
            session()->flash('message', 'Karya siswa berhasil dihapus permanen!');
            $this->loadKaryaSiswas();

        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function openDetailModal($id)
    {
        $this->detailKarya = KaryaSiswa::with('user')->findOrFail((int)$id);
        $this->showDetailModal = true;
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->detailKarya = null;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset([
            'karyaSiswaId',
            'judul',
            'deskripsi',
            'kategori',
            'teknologi',
            'nama_siswa',
            'kelas',
            'tahun',
            'gambar',
            'currentGambar',
            'url_demo',
            'url_repo',
            'status',
        ]);
        $this->tahun = date('Y');
        $this->status = 'draft';
    }

    public function render()
    {
        return view('livewire.karya-siswa-manager', [
            'years' => range(date('Y'), 2000),
        ]);
    }
}
