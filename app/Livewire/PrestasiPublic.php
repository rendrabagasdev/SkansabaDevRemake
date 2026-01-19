<?php

namespace App\Livewire;

use App\Models\Prestasi;
use Livewire\Component;
use Livewire\WithPagination;

class PrestasiPublic extends Component
{
    use WithPagination;

    public $search = '';
    public $filterJenis = '';
    public $filterTingkat = '';
    public $filterTahun = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'filterJenis' => ['except' => ''],
        'filterTingkat' => ['except' => ''],
        'filterTahun' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterJenis()
    {
        $this->resetPage();
    }

    public function updatingFilterTingkat()
    {
        $this->resetPage();
    }

    public function updatingFilterTahun()
    {
        $this->resetPage();
    }

    public function render()
    {
        $prestasis = Prestasi::query()
            ->where('status', 'published')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('judul', 'like', '%' . $this->search . '%')
                      ->orWhere('nama_siswa', 'like', '%' . $this->search . '%')
                      ->orWhere('penyelenggara', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterJenis, fn($q) => $q->where('jenis', $this->filterJenis))
            ->when($this->filterTingkat, fn($q) => $q->where('tingkat', $this->filterTingkat))
            ->when($this->filterTahun, fn($q) => $q->where('tahun', $this->filterTahun))
            ->orderBy('tanggal_prestasi', 'desc')
            ->paginate(12);

        $years = Prestasi::where('status', 'published')
            ->selectRaw('DISTINCT tahun')
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        return view('livewire.prestasi-public', [
            'prestasis' => $prestasis,
            'years' => $years,
        ])->layout('components.layouts.guest');
    }
}
