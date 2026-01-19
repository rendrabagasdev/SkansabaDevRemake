<?php

namespace App\Livewire;

use App\Models\Berita;
use Livewire\Component;
use Livewire\WithPagination;

class BeritaPublic extends Component
{
    use WithPagination;

    public $search = '';
    public $filterHighlight = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'filterHighlight' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterHighlight()
    {
        $this->resetPage();
    }

    public function render()
    {
        $beritas = Berita::query()
            ->where('status', 'published')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('excerpt', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterHighlight === 'yes', fn($q) => $q->where('is_highlight', true))
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        return view('livewire.berita-public', [
            'beritas' => $beritas,
        ])->layout('components.layouts.guest');
    }
}
