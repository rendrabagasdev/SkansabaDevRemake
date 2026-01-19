<?php

namespace App\Livewire;

use App\Models\Alumni;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.guest')]
#[Title('Alumni')]
class AlumniPage extends Component
{
    public $alumniList = [];
    public $filterTahunLulus = '';
    public $filterStatusAlumni = '';
    public $showDetailModal = false;
    public $detailAlumni = null;

    public function mount()
    {
        $this->loadAlumni();
    }

    public function loadAlumni()
    {
        $query = Alumni::publishedOnly()
            ->when($this->filterTahunLulus, fn($q) => $q->where('tahun_lulus', $this->filterTahunLulus))
            ->when($this->filterStatusAlumni, fn($q) => $q->where('status_alumni', $this->filterStatusAlumni))
            ->orderBy('tahun_lulus', 'desc')
            ->orderBy('nama', 'asc');

        $this->alumniList = $query->get();
    }

    public function updatedFilterTahunLulus()
    {
        $this->loadAlumni();
    }

    public function updatedFilterStatusAlumni()
    {
        $this->loadAlumni();
    }

    public function openDetailModal($id)
    {
        $this->detailAlumni = Alumni::publishedOnly()->findOrFail((int)$id);
        $this->showDetailModal = true;
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->detailAlumni = null;
    }

    public function render()
    {
        return view('livewire.alumni-page');
    }
}
