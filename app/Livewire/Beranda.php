<?php

namespace App\Livewire;

use App\Models\LandingPageSlider;
use App\Models\Prestasi;
use App\Models\Berita;
use App\Models\ProspekKarir;
use App\Models\Mitra;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.guest')]
#[Title('Beranda - Jurusan RPL')]
class Beranda extends Component
{
    public function render()
    {
        $sliders = LandingPageSlider::where('is_active', true)
            ->orderBy('order')
            ->get();

        $prestasi = Prestasi::where('status', 'published')
            ->latest('tanggal_prestasi')
            ->take(3)
            ->get();

        $berita = Berita::where('status', 'published')
            ->latest('published_at')
            ->take(3)
            ->get();

        $prospekKarir = ProspekKarir::active()
            ->ordered()
            ->get();

        $mitras = Mitra::published()->get();

        return view('livewire.beranda', [
            'sliders' => $sliders,
            'prestasi' => $prestasi,
            'berita' => $berita,
            'prospekKarir' => $prospekKarir,
            'mitras' => $mitras,
        ]);
    }
}
