<?php

namespace App\Livewire;

use App\Models\Berita;
use Livewire\Component;

class BeritaDetail extends Component
{
    public $berita;

    public function mount($slug)
    {
        $this->berita = Berita::where('status', 'published')
            ->where('slug', $slug)
            ->firstOrFail();

        // Increment views
        $this->berita->increment('views');
    }

    public function render()
    {
        $relatedBeritas = Berita::where('status', 'published')
            ->where('id', '!=', $this->berita->id)
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('livewire.berita-detail', [
            'relatedBeritas' => $relatedBeritas,
        ])->layout('components.layouts.guest');
    }
}
