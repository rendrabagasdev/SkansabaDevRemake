<?php

namespace App\Livewire;

use App\Models\StrukturOrganisasiRpl;
use App\Services\ImageService;
use App\Services\MarkdownService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

#[Layout('components.layouts.app')]
#[Title('Struktur Organisasi RPL - CMS RPL')]
class StrukturOrganisasiRplManager extends Component
{
    use WithFileUploads;

    public $struktur_list = [];
    public $struktur_id;
    public $nama;
    public $jabatan;
    public $foto;
    public $new_foto;
    public $foto_crop_data; // For image cropper coordinates
    public $deskripsi_md;
    public $order = 0;
    public $status = 'draft';
    public $showModal = false;

    protected function rules()
    {
        return [
            'nama' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'new_foto' => 'nullable|string', // Base64 string from cropper
            'deskripsi_md' => 'nullable|string',
            'order' => 'required|integer',
            'status' => 'required|in:draft,published',
        ];
    }

    protected $messages = [
        'nama.required' => 'Nama wajib diisi.',
        'nama.max' => 'Nama maksimal 255 karakter.',
        'jabatan.required' => 'Jabatan wajib diisi.',
        'jabatan.max' => 'Jabatan maksimal 255 karakter.',
        'order.required' => 'Urutan wajib diisi.',
        'order.integer' => 'Urutan harus berupa angka.',
        'status.required' => 'Status wajib dipilih.',
    ];

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->struktur_list = StrukturOrganisasiRpl::ordered()->get()->toArray();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->order = StrukturOrganisasiRpl::max('order') + 1;
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $data = StrukturOrganisasiRpl::findOrFail($id);
        
        $this->struktur_id = $data->id;
        $this->nama = $data->nama;
        $this->jabatan = $data->jabatan;
        $this->foto = $data->foto;
        $this->deskripsi_md = $data->deskripsi_md;
        $this->order = $data->order;
        $this->status = $data->status;
        
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->struktur_id = null;
        $this->nama = '';
        $this->jabatan = '';
        $this->foto = null;
        $this->new_foto = null;
        $this->foto_crop_data = null;
        $this->deskripsi_md = '';
        $this->order = 0;
        $this->status = 'draft';
        $this->resetValidation();
    }

    public function save()
    {
        $this->validate();

        // MANDATORY: Validate markdown if description exists
        if ($this->deskripsi_md) {
            $markdownService = app(MarkdownService::class);
            if (!$markdownService->isValid($this->deskripsi_md)) {
                $this->addError('deskripsi_md', 'Format markdown tidak valid.');
                return;
            }
        }

        $fotoPath = $this->foto;

        // MANDATORY: Process image with ImageService
        if ($this->new_foto && str_starts_with($this->new_foto, 'data:image')) {
            // Delete old image if exists
            if ($this->foto && $this->struktur_id) {
                $oldData = StrukturOrganisasiRpl::find($this->struktur_id);
                if ($oldData && $oldData->foto) {
                    $imageService = app(ImageService::class);
                    $imageService->delete($oldData->foto);
                }
            }

            // Decode base64 to image
            $base64 = preg_replace('/^data:image\/\w+;base64,/', '', $this->new_foto);
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
            $fotoPath = $imageService->processAndStore(
                $uploadedFile,
                'struktur-organisasi'
            );
            
            // Cleanup temp file
            @unlink($tempFile);

            $this->new_foto = null;
            $this->foto_crop_data = null;
        }

        $data = [
            'nama' => $this->nama,
            'jabatan' => $this->jabatan,
            'foto' => $fotoPath,
            'deskripsi_md' => $this->deskripsi_md,
            'order' => $this->order,
            'status' => $this->status,
        ];

        if ($this->struktur_id) {
            StrukturOrganisasiRpl::find($this->struktur_id)->update($data);
            session()->flash('message', 'Data struktur organisasi berhasil diperbarui.');
        } else {
            StrukturOrganisasiRpl::create($data);
            session()->flash('message', 'Data struktur organisasi berhasil ditambahkan.');
        }

        $this->closeModal();
        $this->loadData();
    }

    public function toggleStatus($id)
    {
        $data = StrukturOrganisasiRpl::findOrFail($id);
        $newStatus = $data->status === 'published' ? 'draft' : 'published';
        $data->update(['status' => $newStatus]);
        
        $this->loadData();
        
        $statusText = $newStatus === 'published' ? 'dipublikasi' : 'dijadikan draft';
        session()->flash('message', "Data berhasil {$statusText}.");
    }

    public function updateOrder($items)
    {
        foreach ($items as $item) {
            StrukturOrganisasiRpl::where('id', $item['id'])
                ->update(['order' => $item['order']]);
        }
        
        $this->loadData();
        session()->flash('message', 'Urutan berhasil diperbarui.');
    }

    public function delete($id)
    {
        $data = StrukturOrganisasiRpl::findOrFail($id);
        
        // MANDATORY: Delete image using ImageService
        if ($data->foto) {
            try {
                $imageService = app(ImageService::class);
                $imageService->delete($data->foto);
            } catch (\Exception $e) {
                logger()->error('Failed to delete struktur foto: ' . $e->getMessage());
            }
        }
        
        $data->delete(); // Soft delete
        
        $this->loadData();
        session()->flash('message', 'Data berhasil dihapus.');
    }

    public function render()
    {
        return view('livewire.struktur-organisasi-rpl-manager');
    }
}
