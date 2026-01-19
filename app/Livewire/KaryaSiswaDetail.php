<?php

namespace App\Livewire;

use App\Models\KaryaSiswa;
use Livewire\Component;

class KaryaSiswaDetail extends Component
{
    public $karya;
    public $relatedKaryas;

    public function mount($id)
    {
        $this->karya = KaryaSiswa::where('status', 'published')
            ->findOrFail($id);

        // Get related karya by kategori or tahun
        $this->relatedKaryas = KaryaSiswa::where('status', 'published')
            ->where('id', '!=', $this->karya->id)
            ->where(function($query) {
                $query->where('kategori', $this->karya->kategori)
                      ->orWhere('tahun', $this->karya->tahun);
            })
            ->latest()
            ->limit(3)
            ->get();
    }

    public function render()
    {
        return view('livewire.karya-siswa-detail')
            ->layout('components.layouts.guest');
    }
}
