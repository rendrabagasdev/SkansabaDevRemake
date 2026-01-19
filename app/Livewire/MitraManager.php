<?php

namespace App\Livewire;

use App\Models\Mitra;
use App\Services\ImageService;
use Livewire\Component;
use Livewire\WithFileUploads;

class MitraManager extends Component
{
    use WithFileUploads;

    // List
    public $mitras;
    public $search = '';
    public $filterStatus = '';

    // Form
    public $mitraId = null;
    public $nama_mitra = '';
    public $logo = null;
    public $crop_data = null; // For image cropper component
    public $currentLogo = null;
    public $website = '';
    public $status = 'draft';
    public $order = 0;

    public $isEditMode = false;
    public $showModal = false;

    protected function rules()
    {
        return [
            'nama_mitra' => 'required|string|max:255',
            'logo' => 'nullable|string',
            'website' => 'nullable|url|max:255',
            'status' => 'required|in:draft,published',
            'order' => 'required|integer|min:0',
        ];
    }

    public function mount()
    {
        $this->loadMitras();
    }

    public function loadMitras()
    {
        $query = Mitra::query();

        if ($this->search) {
            $query->where('nama_mitra', 'like', '%' . $this->search . '%');
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        $this->mitras = $query->orderBy('order')->get();
    }

    public function updatedSearch()
    {
        $this->loadMitras();
    }

    public function updatedFilterStatus()
    {
        $this->loadMitras();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->order = Mitra::max('order') + 1;
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $mitra = Mitra::findOrFail($id);
        $this->mitraId = $mitra->id;
        $this->nama_mitra = $mitra->nama_mitra;
        $this->currentLogo = $mitra->logo;
        $this->website = $mitra->website ?? '';
        $this->status = $mitra->status;
        $this->order = $mitra->order;
        $this->isEditMode = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'nama_mitra' => $this->nama_mitra,
            'website' => $this->website ?: null,
            'status' => $this->status,
            'order' => $this->order,
        ];

        // Handle logo upload
        if ($this->logo && str_starts_with($this->logo, 'data:image')) {
            // Delete old logo if editing
            if ($this->isEditMode && $this->currentLogo) {
                $oldMitra = Mitra::find($this->mitraId);
                if ($oldMitra && $oldMitra->logo) {
                    app(ImageService::class)->delete($oldMitra->logo);
                }
            }

            // Decode base64 to image
            $base64 = preg_replace('/^data:image\/\w+;base64,/', '', $this->logo);
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

            // Process with ImageService (compress → WebP)
            $imageService = app(ImageService::class);
            $data['logo'] = $imageService->processAndStore(
                $uploadedFile,
                'mitra',
                ['width' => 300, 'height' => 300]
            );

            // Cleanup temp file
            @unlink($tempFile);
            $this->logo = null;
        }

        // Save to database
        if ($this->isEditMode) {
            Mitra::find($this->mitraId)->update($data);
            session()->flash('message', 'Mitra berhasil diperbarui!');
        } else {
            Mitra::create($data);
            session()->flash('message', 'Mitra berhasil ditambahkan!');
        }

        $this->closeModal();
        $this->loadMitras();
    }

    public function delete($id)
    {
        $mitra = Mitra::findOrFail($id);

        // Delete logo if exists
        if ($mitra->logo) {
            app(ImageService::class)->delete($mitra->logo);
        }

        $mitra->delete();

        session()->flash('message', 'Mitra berhasil dihapus!');
        $this->loadMitras();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->mitraId = null;
        $this->nama_mitra = '';
        $this->logo = null;
        $this->currentLogo = null;
        $this->website = '';
        $this->status = 'draft';
        $this->order = 0;
        $this->isEditMode = false;
        $this->resetErrorBag();
    }

    public function updateOrder($items)
    {
        foreach ($items as $item) {
            Mitra::where('id', $item['value'])->update(['order' => $item['order']]);
        }

        $this->loadMitras();
        session()->flash('message', 'Urutan mitra berhasil diperbarui.');
    }

    public function render()
    {
        return view('livewire.mitra-manager')->layout('components.layouts.app');
    }
}
