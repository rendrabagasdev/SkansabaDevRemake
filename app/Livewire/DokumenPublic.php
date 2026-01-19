<?php

namespace App\Livewire;

use App\Models\DokumenKurikulum;
use Livewire\Component;

class DokumenPublic extends Component
{
    public $search = '';
    public $filterJenis = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'filterJenis' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterJenis()
    {
        $this->resetPage();
    }

    public function render()
    {
        $dokumens = DokumenKurikulum::query()
            ->where('status', 'published')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('judul', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterJenis, fn($q) => $q->where('jenis', $this->filterJenis))
            ->orderBy('tahun_berlaku', 'desc')
            ->orderBy('published_at', 'desc')
            ->get();

        // Get unique jenis for filter
        $jenisOptions = DokumenKurikulum::where('status', 'published')
            ->distinct()
            ->pluck('jenis')
            ->filter()
            ->sort()
            ->values();

        return view('livewire.dokumen-public', [
            'dokumens' => $dokumens,
            'jenisOptions' => $jenisOptions,
        ])->layout('components.layouts.guest');
    }
}
