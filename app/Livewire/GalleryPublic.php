<?php

namespace App\Livewire;

use App\Models\Galeri;
use Livewire\Component;

class GalleryPublic extends Component
{
    public $filterKategori = '';

    protected $queryString = [
        'filterKategori' => ['except' => ''],
    ];

    public function updatingFilterKategori()
    {
        $this->resetPage();
    }

    public function render()
    {
        $galleries = Galeri::query()
            ->where('status', 'published')
            ->when($this->filterKategori, fn($q) => $q->where('kategori', $this->filterKategori))
            ->orderBy('published_at', 'desc')
            ->get();

        // Get unique categories for filter
        $categories = Galeri::where('status', 'published')
            ->distinct()
            ->pluck('kategori')
            ->filter()
            ->sort()
            ->values();

        return view('livewire.gallery-public', [
            'galleries' => $galleries,
            'categories' => $categories,
        ])->layout('components.layouts.guest');
    }
}
