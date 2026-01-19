<?php

namespace App\Livewire;

use App\Models\GlobalSetting;
use App\Services\ImageService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

#[Layout('components.layouts.app')]
#[Title('Pengaturan Global - CMS Jurusan RPL')]
class GlobalSettingsManager extends Component
{
    use WithFileUploads;

    // Site Information
    public $site_name;
    public $site_tagline;
    
    // Logos and Images
    public $logo_primary;
    public $logo_secondary;
    public $favicon;
    
    // New uploads (temporary)
    public $new_logo_primary;
    public $new_logo_secondary;
    public $new_favicon;
    
    // Theme Colors
    public $primary_color;
    public $secondary_color;
    
    // Contact Information
    public $footer_text;
    public $contact_email;
    public $contact_phone;
    public $whatsapp;
    public $address;
    
    // Social Media
    public $facebook;
    public $instagram;
    public $twitter;
    public $youtube;
    public $linkedin;
    public $tiktok;
    public $maps_url;

    protected function rules()
    {
        return [
            'site_name' => 'required|string|max:255',
            'site_tagline' => 'nullable|string|max:255',
            'new_logo_primary' => 'nullable|image|max:5120',
            'new_logo_secondary' => 'nullable|image|max:5120',
            'new_favicon' => 'nullable|image|max:2048',
            'primary_color' => 'required|string|max:50',
            'secondary_color' => 'required|string|max:50',
            'footer_text' => 'nullable|string|max:500',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:30',
            'whatsapp' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:500',
            'facebook' => 'nullable|url|max:255',
            'instagram' => 'nullable|url|max:255',
            'twitter' => 'nullable|url|max:255',
            'youtube' => 'nullable|url|max:255',
            'linkedin' => 'nullable|url|max:255',
            'tiktok' => 'nullable|url|max:255',
            'maps_url' => 'nullable|string',
        ];
    }

    protected $messages = [
        'site_name.required' => 'Nama situs wajib diisi.',
        'site_name.max' => 'Nama situs maksimal 255 karakter.',
        'site_tagline.max' => 'Tagline maksimal 255 karakter.',
        'new_logo_primary.image' => 'Logo utama harus berupa gambar.',
        'new_logo_primary.max' => 'Logo utama maksimal 5MB.',
        'new_logo_secondary.image' => 'Logo sekunder harus berupa gambar.',
        'new_logo_secondary.max' => 'Logo sekunder maksimal 5MB.',
        'new_favicon.image' => 'Favicon harus berupa gambar.',
        'new_favicon.max' => 'Favicon maksimal 2MB.',
        'primary_color.required' => 'Warna primer wajib diisi.',
        'secondary_color.required' => 'Warna sekunder wajib diisi.',
        'footer_text.max' => 'Teks footer maksimal 500 karakter.',
        'contact_email.email' => 'Format email tidak valid.',
        'contact_phone.max' => 'Nomor telepon maksimal 30 karakter.',
        'address.max' => 'Alamat maksimal 500 karakter.',
    ];

    public function mount()
    {
        $this->loadSettings();
    }

    public function loadSettings()
    {
        $settings = GlobalSetting::instance();
        
        $this->site_name = $settings->site_name;
        $this->site_tagline = $settings->site_tagline;
        $this->logo_primary = $settings->logo_primary;
        $this->logo_secondary = $settings->logo_secondary;
        $this->favicon = $settings->favicon;
        $this->primary_color = $settings->primary_color;
        $this->secondary_color = $settings->secondary_color;
        $this->footer_text = $settings->footer_text;
        $this->contact_email = $settings->contact_email;
        $this->contact_phone = $settings->contact_phone;
        $this->whatsapp = $settings->whatsapp;
        $this->address = $settings->address;
        $this->facebook = $settings->facebook;
        $this->instagram = $settings->instagram;
        $this->twitter = $settings->twitter;
        $this->youtube = $settings->youtube;
        $this->linkedin = $settings->linkedin;
        $this->tiktok = $settings->tiktok;
        $this->maps_url = $settings->maps_url;
    }

    public function deleteLogo($type)
    {
        $settings = GlobalSetting::instance();
        
        $validTypes = ['logo_primary', 'logo_secondary', 'favicon'];
        
        if (!in_array($type, $validTypes)) {
            session()->flash('error', 'Tipe logo tidak valid.');
            return;
        }
        
        // Delete file from storage
        if ($settings->$type) {
            Storage::delete($settings->$type);
        }
        
        // Update database
        $settings->update([$type => null]);
        
        // Update component property
        $this->$type = null;
        
        $labels = [
            'logo_primary' => 'Logo utama',
            'logo_secondary' => 'Logo sekunder',
            'favicon' => 'Favicon'
        ];
        
        session()->flash('message', $labels[$type] . ' berhasil dihapus.');
        $this->loadSettings();
    }

    public function save()
    {
        $this->validate();

        $settings = GlobalSetting::instance();
        $imageService = app(ImageService::class);

        // Process new logo_primary upload
        if ($this->new_logo_primary) {
            // Delete old logo
            if ($settings->logo_primary) {
                Storage::delete($settings->logo_primary);
            }
            
            $this->logo_primary = $imageService->processAndStore(
                $this->new_logo_primary,
                'logos',
                [
                    'resize' => [400, 400],
                    'compress' => true,
                    'webp' => true,
                ]
            );
            
            $this->new_logo_primary = null;
        }

        // Process new logo_secondary upload
        if ($this->new_logo_secondary) {
            // Delete old logo
            if ($settings->logo_secondary) {
                Storage::delete($settings->logo_secondary);
            }
            
            $this->logo_secondary = $imageService->processAndStore(
                $this->new_logo_secondary,
                'logos',
                [
                    'resize' => [400, 400],
                    'compress' => true,
                    'webp' => true,
                ]
            );
            
            $this->new_logo_secondary = null;
        }

        // Process new favicon upload
        if ($this->new_favicon) {
            // Delete old favicon
            if ($settings->favicon) {
                Storage::delete($settings->favicon);
            }
            
            $this->favicon = $imageService->processAndStore(
                $this->new_favicon,
                'logos',
                [
                    'resize' => [64, 64],
                    'compress' => true,
                    'webp' => true,
                ]
            );
            
            $this->new_favicon = null;
        }

        // Update settings
        $settings->update([
            'site_name' => $this->site_name,
            'site_tagline' => $this->site_tagline,
            'logo_primary' => $this->logo_primary,
            'logo_secondary' => $this->logo_secondary,
            'favicon' => $this->favicon,
            'primary_color' => $this->primary_color,
            'secondary_color' => $this->secondary_color,
            'footer_text' => $this->footer_text,
            'contact_email' => $this->contact_email,
            'contact_phone' => $this->contact_phone,
            'whatsapp' => $this->whatsapp,
            'address' => $this->address,
            'facebook' => $this->facebook,
            'instagram' => $this->instagram,
            'twitter' => $this->twitter,
            'youtube' => $this->youtube,
            'linkedin' => $this->linkedin,
            'tiktok' => $this->tiktok,
            'maps_url' => $this->maps_url,
        ]);

        session()->flash('message', 'Pengaturan berhasil disimpan.');
        $this->loadSettings();
    }

    public function render()
    {
        $settings = GlobalSetting::instance();
        
        return view('livewire.global-settings-manager', [
            'settings' => $settings,
        ]);
    }
}
