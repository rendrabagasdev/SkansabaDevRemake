<?php

namespace App\Livewire;

use App\Http\Requests\StoreDokumenKurikulumRequest;
use App\Http\Requests\UpdateDokumenKurikulumRequest;
use App\Models\DokumenKurikulum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app')]
#[Title('Kelola Dokumen Kurikulum')]
class DokumenKurikulumManager extends Component
{
    use WithFileUploads;

    // List properties
    public $dokumenList = [];
    public $search = '';
    public $filterStatus = '';
    public $filterJenis = '';
    public $filterTahun = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    // Form properties
    public $showModal = false;
    public $showDetailModal = false;
    public $isEditMode = false;
    public $dokumenId = null;
    public $detailDokumen = null;

    // Form fields
    public $judul = '';
    public $jenis = 'kurikulum';
    public $tahun_berlaku;
    public $file = null;
    public $currentFile = null;
    public $status = 'draft';

    protected $listeners = ['refreshDokumen' => 'loadDokumen'];

    public function mount()
    {
        $this->tahun_berlaku = date('Y');
        $this->loadDokumen();
    }

    public function loadDokumen()
    {
        $query = DokumenKurikulum::with('user')
            ->when($this->search, function ($q) {
                $q->where('judul', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterJenis, fn($q) => $q->where('jenis', $this->filterJenis))
            ->when($this->filterTahun, fn($q) => $q->where('tahun_berlaku', $this->filterTahun))
            ->orderBy($this->sortField, $this->sortDirection);

        $this->dokumenList = $query->get();
    }

    public function updatedSearch()
    {
        $this->loadDokumen();
    }

    public function updatedFilterStatus()
    {
        $this->loadDokumen();
    }

    public function updatedFilterJenis()
    {
        $this->loadDokumen();
    }

    public function updatedFilterTahun()
    {
        $this->loadDokumen();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->loadDokumen();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->isEditMode = false;
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $dokumen = DokumenKurikulum::findOrFail((int)$id);
        
        $this->dokumenId = $dokumen->id;
        $this->judul = $dokumen->judul;
        $this->jenis = $dokumen->jenis;
        $this->tahun_berlaku = $dokumen->tahun_berlaku;
        $this->currentFile = $dokumen->file;
        $this->status = $dokumen->status;
        $this->isEditMode = true;
        $this->showModal = true;
    }

    public function openDetailModal($id)
    {
        $this->detailDokumen = DokumenKurikulum::with('user')->findOrFail((int)$id);
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
        $this->detailDokumen = null;
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
            'jenis' => ['required', 'in:kurikulum,silabus,modul,panduan,lainnya'],
            'tahun_berlaku' => ['required', 'integer', 'min:2000', 'max:' . (date('Y') + 10)],
            'file' => [
                'required',
                'file',
                'max:10240',
                'mimes:pdf,doc,docx',
                'mimetypes:application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            ],
            'status' => ['required', 'in:draft,published,archived'],
        ], [
            'file.max' => 'Ukuran file maksimal 10 MB.',
            'file.mimes' => 'File harus berformat PDF, DOC, atau DOCX.',
            'file.mimetypes' => 'File harus berformat PDF, DOC, atau DOCX.',
            'tahun_berlaku.min' => 'Tahun berlaku minimal 2000.',
            'tahun_berlaku.max' => 'Tahun berlaku tidak valid.',
        ]);

        // Sanitize filename and store file
        $filename = $this->sanitizeFilename($this->judul);
        $extension = $this->file->getClientOriginalExtension();
        $filepath = $this->file->storeAs('documents', $filename . '.' . $extension, 'public');

        $dokumen = DokumenKurikulum::create([
            'user_id' => Auth::id(),
            'judul' => $this->judul,
            'jenis' => $this->jenis,
            'tahun_berlaku' => $this->tahun_berlaku,
            'file' => $filepath,
            'ukuran_file' => $this->file->getSize(),
            'status' => $this->status,
            'published_at' => $this->status === 'published' ? now() : null,
        ]);

        $this->closeModal();
        $this->loadDokumen();
        session()->flash('message', 'Dokumen berhasil ditambahkan.');
    }

    public function update()
    {
        $this->validate([
            'judul' => ['required', 'string', 'max:255'],
            'jenis' => ['required', 'in:kurikulum,silabus,modul,panduan,lainnya'],
            'tahun_berlaku' => ['required', 'integer', 'min:2000', 'max:' . (date('Y') + 10)],
            'file' => [
                'nullable',
                'file',
                'max:10240',
                'mimes:pdf,doc,docx',
                'mimetypes:application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            ],
            'status' => ['required', 'in:draft,published,archived'],
        ], [
            'file.max' => 'Ukuran file maksimal 10 MB.',
            'file.mimes' => 'File harus berformat PDF, DOC, atau DOCX.',
            'file.mimetypes' => 'File harus berformat PDF, DOC, atau DOCX.',
            'tahun_berlaku.min' => 'Tahun berlaku minimal 2000.',
            'tahun_berlaku.max' => 'Tahun berlaku tidak valid.',
        ]);

        $dokumen = DokumenKurikulum::findOrFail((int)$this->dokumenId);

        $data = [
            'judul' => $this->judul,
            'jenis' => $this->jenis,
            'tahun_berlaku' => $this->tahun_berlaku,
            'status' => $this->status,
        ];

        // Handle file upload if new file provided
        if ($this->file) {
            // Delete old file
            if ($dokumen->file && Storage::disk('public')->exists($dokumen->file)) {
                Storage::disk('public')->delete($dokumen->file);
            }

            // Store new file
            $filename = $this->sanitizeFilename($this->judul);
            $extension = $this->file->getClientOriginalExtension();
            $filepath = $this->file->storeAs('documents', $filename . '.' . $extension, 'public');

            $data['file'] = $filepath;
            $data['ukuran_file'] = $this->file->getSize();
        }

        // Update published_at based on status
        if ($this->status === 'published' && $dokumen->status !== 'published') {
            $data['published_at'] = now();
        } elseif ($this->status !== 'published') {
            $data['published_at'] = null;
        }

        $dokumen->fill($data)->save();

        $this->closeModal();
        $this->loadDokumen();
        session()->flash('message', 'Dokumen berhasil diperbarui.');
    }

    public function delete($id)
    {
        $dokumen = DokumenKurikulum::findOrFail((int)$id);
        $dokumen->delete();

        $this->loadDokumen();
        session()->flash('message', 'Dokumen berhasil dihapus (soft delete).');
    }

    public function restore($id)
    {
        $dokumen = DokumenKurikulum::withTrashed()->findOrFail((int)$id);
        $dokumen->restore();

        $this->loadDokumen();
        session()->flash('message', 'Dokumen berhasil dipulihkan.');
    }

    public function forceDelete($id)
    {
        $dokumen = DokumenKurikulum::withTrashed()->findOrFail((int)$id);

        // Delete file from storage
        if ($dokumen->file && Storage::disk('public')->exists($dokumen->file)) {
            Storage::disk('public')->delete($dokumen->file);
        }

        $dokumen->forceDelete();

        $this->loadDokumen();
        session()->flash('message', 'Dokumen berhasil dihapus permanen.');
    }

    public function download($id)
    {
        $dokumen = DokumenKurikulum::where('status', 'published')->findOrFail((int)$id);

        if (!$dokumen->file || !Storage::disk('public')->exists($dokumen->file)) {
            session()->flash('error', 'File tidak ditemukan.');
            return;
        }

        return response()->download(Storage::disk('public')->path($dokumen->file), basename($dokumen->file));
    }

    private function sanitizeFilename($name)
    {
        // Remove special characters and replace spaces with underscores
        $name = Str::slug($name, '_');
        // Add timestamp to ensure uniqueness
        return $name . '_' . time();
    }

    private function resetForm()
    {
        $this->reset([
            'dokumenId',
            'judul',
            'jenis',
            'tahun_berlaku',
            'file',
            'currentFile',
            'status',
        ]);
        $this->jenis = 'kurikulum';
        $this->tahun_berlaku = date('Y');
        $this->status = 'draft';
    }

    public function render()
    {
        return view('livewire.dokumen-kurikulum-manager');
    }
}
