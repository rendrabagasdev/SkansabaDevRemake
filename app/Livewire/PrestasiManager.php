<?php

namespace App\Livewire;

use App\Models\Prestasi;
use App\Services\ContentStatusService;
use App\Services\ImageService;
use App\Services\MarkdownService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app')]
#[Title('Kelola Prestasi Siswa')]
class PrestasiManager extends Component
{
    use WithFileUploads;

    // List properties
    public $prestasis = [];
    public $search = '';
    public $filterStatus = '';
    public $filterJenis = '';
    public $filterTingkat = '';
    public $filterTahun = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    // Form properties
    public $showModal = false;
    public $isEditMode = false;
    public $prestasiId = null;

    // Form fields
    public $judul = '';
    public $deskripsi = '';
    public $jenis = 'akademik';
    public $tingkat = 'sekolah';
    public $penyelenggara = '';
    public $nama_siswa = '';
    public $kelas = '';
    public $tanggal_prestasi = '';
    public $tahun = '';
    public $gambar = null;
    public $currentGambar = null;
    public $sertifikat = null;
    public $currentSertifikat = null;
    public $status = 'draft';

    protected $listeners = ['refreshPrestasis' => 'loadPrestasis'];

    public function mount()
    {
        $this->tahun = date('Y');
        $this->loadPrestasis();
    }

    public function loadPrestasis()
    {
        $query = Prestasi::with('user')
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('judul', 'like', '%' . $this->search . '%')
                        ->orWhere('nama_siswa', 'like', '%' . $this->search . '%')
                        ->orWhere('kelas', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterJenis, fn($q) => $q->where('jenis', $this->filterJenis))
            ->when($this->filterTingkat, fn($q) => $q->where('tingkat', $this->filterTingkat))
            ->when($this->filterTahun, fn($q) => $q->where('tahun', $this->filterTahun))
            ->orderBy($this->sortField, $this->sortDirection);

        $this->prestasis = $query->get();
    }

    public function updatedSearch()
    {
        $this->loadPrestasis();
    }

    public function updatedFilterStatus()
    {
        $this->loadPrestasis();
    }

    public function updatedFilterJenis()
    {
        $this->loadPrestasis();
    }

    public function updatedFilterTingkat()
    {
        $this->loadPrestasis();
    }

    public function updatedFilterTahun()
    {
        $this->loadPrestasis();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->loadPrestasis();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->isEditMode = false;
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $prestasi = Prestasi::findOrFail($id);
        
        $this->prestasiId = $prestasi->id;
        $this->judul = $prestasi->judul;
        $this->deskripsi = $prestasi->deskripsi;
        $this->jenis = $prestasi->jenis;
        $this->tingkat = $prestasi->tingkat;
        $this->penyelenggara = $prestasi->penyelenggara;
        $this->nama_siswa = $prestasi->nama_siswa;
        $this->kelas = $prestasi->kelas;
        $this->tanggal_prestasi = $prestasi->tanggal_prestasi->format('Y-m-d');
        $this->tahun = $prestasi->tahun;
        $this->currentGambar = $prestasi->gambar;
        $this->currentSertifikat = $prestasi->sertifikat;
        $this->status = $prestasi->status;
        
        $this->isEditMode = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'jenis' => 'required|in:akademik,non-akademik,kompetisi,sertifikasi',
            'tingkat' => 'required|in:sekolah,kecamatan,kota,provinsi,nasional,internasional',
            'penyelenggara' => 'required|string|max:255',
            'nama_siswa' => 'required|string|max:255',
            'kelas' => 'required|string|max:255',
            'tanggal_prestasi' => 'required|date',
            'tahun' => 'required|integer|min:2000|max:' . (date('Y') + 1),
            'gambar' => 'nullable|string',
            'sertifikat' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
            'status' => 'required|in:draft,review,published,archived',
        ]);

        try {
            $data = [
                'user_id' => Auth::id(),
                'judul' => $this->judul,
                'deskripsi' => $this->deskripsi,
                'jenis' => $this->jenis,
                'tingkat' => $this->tingkat,
                'penyelenggara' => $this->penyelenggara,
                'nama_siswa' => $this->nama_siswa,
                'kelas' => $this->kelas,
                'tanggal_prestasi' => $this->tanggal_prestasi,
                'tahun' => $this->tahun,
                'status' => $this->status,
            ];

            // Handle image upload via ImageService
            if ($this->gambar && str_starts_with($this->gambar, 'data:image')) {
                // Delete old image if editing
                if ($this->isEditMode && $this->currentGambar) {
                    $oldPrestasi = Prestasi::find($this->prestasiId);
                    if ($oldPrestasi && $oldPrestasi->gambar) {
                        $imageService = app(ImageService::class);
                        $imageService->delete($oldPrestasi->gambar);
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
                    'prestasi'
                );
                
                // Cleanup temp file
                @unlink($tempFile);

                $this->gambar = null;
            }

            // Handle sertifikat upload
            if ($this->sertifikat) {
                $data['sertifikat'] = $this->sertifikat->store('sertifikat', 'public');

                // Delete old sertifikat if editing
                if ($this->isEditMode && $this->currentSertifikat) {
                    Storage::disk('public')->delete($this->currentSertifikat);
                }
            }

            // Handle published_at timestamp
            if ($this->status === 'published') {
                $data['published_at'] = now();
            }

            if ($this->isEditMode) {
                $prestasi = Prestasi::findOrFail($this->prestasiId);
                $prestasi->update($data);
                
                // Log status change
                if ($prestasi->wasChanged('status')) {
                    app(ContentStatusService::class)->logStatusChange(
                        $prestasi,
                        $prestasi->getOriginal('status'),
                        $this->status,
                        Auth::id()
                    );
                }

                session()->flash('message', 'Prestasi berhasil diperbarui!');
            } else {
                Prestasi::create($data);
                session()->flash('message', 'Prestasi berhasil ditambahkan!');
            }

            $this->closeModal();
            $this->loadPrestasis();

        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $prestasi = Prestasi::findOrFail($id);
            
            // Delete images
            if ($prestasi->gambar) {
                app(ImageService::class)->delete($prestasi->gambar);
            }
            if ($prestasi->sertifikat) {
                Storage::disk('public')->delete($prestasi->sertifikat);
            }

            $prestasi->delete();
            
            session()->flash('message', 'Prestasi berhasil dihapus!');
            $this->loadPrestasis();

        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function restore($id)
    {
        try {
            Prestasi::withTrashed()->findOrFail($id)->restore();
            session()->flash('message', 'Prestasi berhasil dipulihkan!');
            $this->loadPrestasis();
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function forceDelete($id)
    {
        try {
            $prestasi = Prestasi::withTrashed()->findOrFail($id);
            
            // Delete images permanently
            if ($prestasi->gambar) {
                app(ImageService::class)->deleteImage($prestasi->gambar);
            }
            if ($prestasi->sertifikat) {
                Storage::disk('public')->delete($prestasi->sertifikat);
            }

            $prestasi->forceDelete();
            
            session()->flash('message', 'Prestasi berhasil dihapus permanen!');
            $this->loadPrestasis();

        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset([
            'prestasiId',
            'judul',
            'deskripsi',
            'jenis',
            'tingkat',
            'penyelenggara',
            'nama_siswa',
            'kelas',
            'tanggal_prestasi',
            'tahun',
            'gambar',
            'currentGambar',
            'sertifikat',
            'currentSertifikat',
            'status',
        ]);
        $this->tahun = date('Y');
        $this->jenis = 'akademik';
        $this->tingkat = 'sekolah';
        $this->status = 'draft';
    }

    public function render()
    {
        return view('livewire.prestasi-manager', [
            'years' => range(date('Y'), 2000),
        ]);
    }
}
