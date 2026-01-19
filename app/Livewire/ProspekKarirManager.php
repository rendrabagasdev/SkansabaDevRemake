<?php

namespace App\Livewire;

use App\Models\ProspekKarir;
use App\Services\ImageService;
use App\Services\MarkdownService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

#[Layout('components.layouts.app')]
#[Title('Prospek Karir - CMS Jurusan RPL')]
class ProspekKarirManager extends Component
{
    use WithFileUploads;

    public $prospek_karirs = [];
    public $prospek_karir_id;
    public $title;
    public $description;
    public $icon;
    public $image;
    public $new_image;
    public $order = 0;
    public $is_active = true;
    public $showModal = false;

    protected function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'icon' => 'nullable|string|max:100',
            'new_image' => 'nullable|image|max:5120',
            'order' => 'required|integer',
            'is_active' => 'boolean',
        ];
    }

    protected $messages = [
        'title.required' => 'Judul prospek karir wajib diisi.',
        'title.max' => 'Judul maksimal 255 karakter.',
        'description.required' => 'Deskripsi wajib diisi.',
        'icon.max' => 'Icon maksimal 100 karakter.',
        'new_image.image' => 'File harus berupa gambar.',
        'new_image.max' => 'Gambar maksimal 5MB.',
        'order.required' => 'Urutan wajib diisi.',
        'order.integer' => 'Urutan harus berupa angka.',
    ];

    public function mount()
    {
        $this->loadProspekKarirs();
    }

    public function loadProspekKarirs()
    {
        $this->prospek_karirs = ProspekKarir::ordered()->get()->toArray();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->order = ProspekKarir::max('order') + 1;
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $prospekKarir = ProspekKarir::findOrFail($id);
        
        $this->prospek_karir_id = $prospekKarir->id;
        $this->title = $prospekKarir->title;
        $this->description = $prospekKarir->description;
        $this->icon = $prospekKarir->icon;
        $this->image = $prospekKarir->image;
        $this->order = $prospekKarir->order;
        $this->is_active = $prospekKarir->is_active;
        
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->prospek_karir_id = null;
        $this->title = '';
        $this->description = '';
        $this->icon = '';
        $this->image = null;
        $this->new_image = null;
        $this->order = 0;
        $this->is_active = true;
        $this->resetValidation();
    }

    public function save()
    {
        $this->validate();

        // MANDATORY: Validate markdown with MarkdownService
        $markdownService = app(MarkdownService::class);
        if (!$markdownService->isValid($this->description)) {
            $this->addError('description', 'Format markdown tidak valid.');
            return;
        }

        $imageService = app(ImageService::class);
        $imagePath = $this->image;

        // MANDATORY: Process image with ImageService (resize, compress, webp)
        if ($this->new_image) {
            // Delete old image if exists
            if ($this->image && $this->prospek_karir_id) {
                $oldProspekKarir = ProspekKarir::find($this->prospek_karir_id);
                if ($oldProspekKarir && $oldProspekKarir->image) {
                    Storage::delete($oldProspekKarir->image);
                }
            }
            
            $imagePath = $imageService->processAndStore(
                $this->new_image,
                'prospek-karir',
                [
                    'resize' => [800, 600],
                    'compress' => true,
                    'webp' => true,
                ]
            );
        }

        $data = [
            'title' => $this->title,
            'description' => $this->description, // Store raw markdown, render via accessor
            'icon' => $this->icon,
            'image' => $imagePath,
            'order' => $this->order,
            'is_active' => $this->is_active,
        ];

        if ($this->prospek_karir_id) {
            ProspekKarir::find($this->prospek_karir_id)->update($data);
            session()->flash('message', 'Prospek karir berhasil diperbarui.');
        } else {
            ProspekKarir::create($data);
            session()->flash('message', 'Prospek karir berhasil ditambahkan.');
        }

        $this->closeModal();
        $this->loadProspekKarirs();
    }

    public function toggleActive($id)
    {
        $prospekKarir = ProspekKarir::findOrFail($id);
        $prospekKarir->update(['is_active' => !$prospekKarir->is_active]);
        
        $this->loadProspekKarirs();
        session()->flash('message', 'Status berhasil diubah.');
    }

    public function updateOrder($items)
    {
        foreach ($items as $item) {
            ProspekKarir::where('id', $item['id'])->update(['order' => $item['order']]);
        }
        
        $this->loadProspekKarirs();
    }

    public function delete($id)
    {
        $prospekKarir = ProspekKarir::findOrFail($id);
        
        // Delete image if exists
        if ($prospekKarir->image) {
            Storage::delete($prospekKarir->image);
        }
        
        $prospekKarir->delete();
        
        $this->loadProspekKarirs();
        session()->flash('message', 'Prospek karir berhasil dihapus.');
    }

    public function render()
    {
        return view('livewire.prospek-karir-manager');
    }
}
