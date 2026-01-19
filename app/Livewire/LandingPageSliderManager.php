<?php

namespace App\Livewire;

use App\Models\LandingPageSlider;
use App\Services\ImageService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app')]
#[Title('Landing Page Slider - CMS RPL')]
class LandingPageSliderManager extends Component
{
    use WithFileUploads;

    public $sliders = [];
    
    // Form properties
    public $slider_id = null;
    public $image;
    public $new_image; // Base64 from cropper
    public $title = '';
    public $subtitle = '';
    public $link = '';
    public $order = 0;
    public $is_active = true;
    
    // UI state
    public $showModal = false;
    public $isEditing = false;
    public $currentImage = null;

    protected $rules = [
        'image' => 'nullable|image|max:10240', // 10MB max
        'new_image' => 'nullable|string', // Base64 from cropper
        'title' => 'required|string|max:255',
        'subtitle' => 'nullable|string|max:255',
        'link' => 'nullable|url',
        'order' => 'integer',
        'is_active' => 'boolean',
    ];

    protected $messages = [
        'image.image' => 'File harus berupa gambar',
        'image.max' => 'Ukuran gambar maksimal 10MB',
        'title.required' => 'Judul wajib diisi',
        'title.max' => 'Judul maksimal 255 karakter',
        'subtitle.max' => 'Subtitle maksimal 255 karakter',
        'link.url' => 'Link harus berupa URL yang valid',
    ];

    public function mount()
    {
        $this->loadSliders();
    }

    public function loadSliders()
    {
        $this->sliders = LandingPageSlider::ordered()->get();
    }

    public function openCreateModal()
    {
        $this->reset(['slider_id', 'image', 'title', 'subtitle', 'link', 'order', 'is_active', 'currentImage']);
        $this->isEditing = false;
        $this->order = LandingPageSlider::max('order') + 1;
        $this->is_active = true;
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $slider = LandingPageSlider::findOrFail($id);
        
        $this->slider_id = $slider->id;
        $this->title = $slider->title;
        $this->subtitle = $slider->subtitle;
        $this->link = $slider->link;
        $this->order = $slider->order;
        $this->is_active = $slider->is_active;
        $this->currentImage = $slider->image;
        $this->image = null;
        
        $this->isEditing = true;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['slider_id', 'image', 'new_image', 'title', 'subtitle', 'link', 'order', 'is_active', 'currentImage']);
        $this->resetValidation();
    }

    public function save()
    {
        $this->validate();

        $data = [
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'link' => $this->link,
            'order' => $this->order,
            'is_active' => $this->is_active,
        ];

        // Process image from cropper (base64) or direct upload
        if ($this->new_image && str_starts_with($this->new_image, 'data:image')) {
            $imageService = app(ImageService::class);
            
            // Delete old image if editing
            if ($this->slider_id && $this->currentImage) {
                $imageService->delete($this->currentImage);
            }
            
            // Decode base64
            $base64 = preg_replace('/^data:image\/\w+;base64,/', '', $this->new_image);
            $imageData = base64_decode($base64);
            
            // Create temp file
            $tempPath = storage_path('app/temp/');
            if (!file_exists($tempPath)) {
                mkdir($tempPath, 0755, true);
            }
            $tempFile = $tempPath . uniqid('crop_') . '.jpg';
            file_put_contents($tempFile, $imageData);
            
            // Create UploadedFile instance
            $uploadedFile = new \Illuminate\Http\UploadedFile(
                $tempFile,
                uniqid() . '.jpg',
                'image/jpeg',
                null,
                true
            );
            
            // Process (already cropped, just compress and convert to WebP)
            $imagePath = $imageService->processAndStore(
                $uploadedFile,
                'landing-sliders'
            );
            
            @unlink($tempFile);
            $data['image'] = $imagePath;
        } elseif ($this->image) {
            $imageService = app(ImageService::class);
            $imagePath = $imageService->processAndStore(
                $this->image,
                'landing-sliders',
                [
                    'resize' => ['width' => 1920, 'height' => 1080],
                    'compress' => true,
                    'webp' => true,
                ]
            );
            $data['image'] = $imagePath;

            // Delete old image if editing
            if ($this->isEditing && $this->currentImage) {
                $imageService->delete($this->currentImage);
            }
        }

        if ($this->isEditing) {
            $slider = LandingPageSlider::findOrFail($this->slider_id);
            $slider->update($data);
            session()->flash('message', 'Slider berhasil diperbarui');
        } else {
            LandingPageSlider::create($data);
            session()->flash('message', 'Slider berhasil ditambahkan');
        }

        $this->closeModal();
        $this->loadSliders();
    }

    public function toggleActive($id)
    {
        $slider = LandingPageSlider::findOrFail($id);
        $slider->update(['is_active' => !$slider->is_active]);
        
        $this->loadSliders();
        
        $status = $slider->is_active ? 'diaktifkan' : 'dinonaktifkan';
        session()->flash('message', "Slider berhasil {$status}");
    }

    public function updateOrder($items)
    {
        foreach ($items as $item) {
            LandingPageSlider::where('id', $item['id'])
                ->update(['order' => $item['order']]);
        }
        
        $this->loadSliders();
        session()->flash('message', 'Urutan slider berhasil diperbarui');
    }

    public function delete($id)
    {
        $slider = LandingPageSlider::findOrFail($id);
        
        // MANDATORY: Delete image using ImageService
        if ($slider->image) {
            try {
                $imageService = app(ImageService::class);
                $imageService->delete($slider->image);
            } catch (\Exception $e) {
                // Log error but continue deletion
                logger()->error('Failed to delete slider image: ' . $e->getMessage());
            }
        }
        
        $slider->delete();
        
        $this->loadSliders();
        session()->flash('message', 'Slider berhasil dihapus');
    }

    public function render()
    {
        return view('livewire.landing-page-slider-manager');
    }
}
