<?php

namespace App\Livewire;

use App\Models\Alumni;
use Livewire\Component;

class AlumniPublic extends Component
{
    public $search = '';
    public $filterStatus = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'filterStatus' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function render()
    {
        $alumniData = Alumni::query()
            ->where('status', 'published')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nama', 'like', '%' . $this->search . '%')
                      ->orWhere('institusi', 'like', '%' . $this->search . '%')
                      ->orWhere('bidang', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterStatus, fn($q) => $q->where('status_alumni', $this->filterStatus))
            ->orderBy('tahun_lulus', 'desc')
            ->get()
            ->groupBy('tahun_lulus');

        return view('livewire.alumni-public', [
            'alumniData' => $alumniData,
        ])->layout('components.layouts.guest');
    }
}
