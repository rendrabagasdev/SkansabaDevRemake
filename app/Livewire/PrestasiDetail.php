<?php

namespace App\Livewire;

use App\Models\Prestasi;
use Livewire\Component;

class PrestasiDetail extends Component
{
    public $prestasi;

    public function mount($id)
    {
        $this->prestasi = Prestasi::where('status', 'published')
            ->findOrFail($id);
    }

    public function render()
    {
        $relatedPrestasis = Prestasi::where('status', 'published')
            ->where('id', '!=', $this->prestasi->id)
            ->where(function ($query) {
                $query->where('jenis', $this->prestasi->jenis)
                      ->orWhere('tingkat', $this->prestasi->tingkat);
            })
            ->latest('tanggal_prestasi')
            ->take(3)
            ->get();

        return view('livewire.prestasi-detail', [
            'relatedPrestasis' => $relatedPrestasis,
        ])->layout('components.layouts.guest');
    }
}
