<?php

namespace App\Livewire;

use App\Models\KaryaSiswa;
use Livewire\Component;

class KaryaSiswaPublic extends Component
{
    public $search = '';
    public $filterKategori = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'filterKategori' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterKategori()
    {
        $this->resetPage();
    }

    public function render()
    {
        $karyas = KaryaSiswa::query()
            ->where('status', 'published')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('judul', 'like', '%' . $this->search . '%')
                      ->orWhere('nama_siswa', 'like', '%' . $this->search . '%')
                      ->orWhere('deskripsi', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterKategori, fn($q) => $q->where('kategori', $this->filterKategori))
            ->orderBy('tahun', 'desc')
            ->orderBy('published_at', 'desc')
            ->get();

        // Get unique categories for filter
        $categories = KaryaSiswa::where('status', 'published')
            ->distinct()
            ->pluck('kategori')
            ->filter()
            ->sort()
            ->values();

        return view('livewire.karya-siswa-public', [
            'karyas' => $karyas,
            'categories' => $categories,
        ])->layout('components.layouts.guest');
    }
}
