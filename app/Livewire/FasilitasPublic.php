<?php

namespace App\Livewire;

use App\Models\Fasilitas;
use Livewire\Component;

class FasilitasPublic extends Component
{
    public function render()
    {
        $fasilitas = Fasilitas::query()
            ->where('status', 'published')
            ->orderBy('published_at', 'desc')
            ->get();

        return view('livewire.fasilitas-public', [
            'fasilitas' => $fasilitas,
        ])->layout('components.layouts.guest');
    }
}
