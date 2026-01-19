<?php

namespace App\Livewire;

use App\Models\Fasilitas;
use Livewire\Component;

class FasilitasDetail extends Component
{
    public $fasilitas;
    public $relatedFasilitas;

    public function mount($id)
    {
        $this->fasilitas = Fasilitas::where('status', 'published')
            ->findOrFail($id);

        // Get other fasilitas
        $this->relatedFasilitas = Fasilitas::where('status', 'published')
            ->where('id', '!=', $this->fasilitas->id)
            ->latest()
            ->limit(3)
            ->get();
    }

    public function render()
    {
        return view('livewire.fasilitas-detail')
            ->layout('components.layouts.guest');
    }
}
