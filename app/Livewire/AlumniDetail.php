<?php

namespace App\Livewire;

use App\Models\Alumni;
use Livewire\Component;

class AlumniDetail extends Component
{
    public $alumni;
    public $relatedAlumni;

    public function mount($id)
    {
        $this->alumni = Alumni::where('status', 'published')
            ->findOrFail($id);

        // Get related alumni by tahun_lulus or status_alumni
        $this->relatedAlumni = Alumni::where('status', 'published')
            ->where('id', '!=', $this->alumni->id)
            ->where(function($query) {
                $query->where('tahun_lulus', $this->alumni->tahun_lulus)
                      ->orWhere('status_alumni', $this->alumni->status_alumni);
            })
            ->latest()
            ->limit(3)
            ->get();
    }

    public function render()
    {
        return view('livewire.alumni-detail')
            ->layout('components.layouts.guest');
    }
}
