<?php

namespace App\Livewire;

use App\Models\Galeri;
use Livewire\Component;

class GaleriDetail extends Component
{
    public $galeri;
    public $relatedGaleris;

    public function mount($id)
    {
        $this->galeri = Galeri::where('status', 'published')
            ->findOrFail($id);

        // Get related galeri by kategori
        $this->relatedGaleris = Galeri::where('status', 'published')
            ->where('id', '!=', $this->galeri->id)
            ->when($this->galeri->kategori, fn($q) => $q->where('kategori', $this->galeri->kategori))
            ->latest()
            ->limit(3)
            ->get();
    }

    public function render()
    {
        return view('livewire.galeri-detail')
            ->layout('components.layouts.guest');
    }
}
