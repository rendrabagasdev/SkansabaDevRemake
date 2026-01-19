<?php

namespace App\Livewire;

use Livewire\Component;

class UnitProduksi extends Component
{
    public function render()
    {
        return view('livewire.unit-produksi')
            ->layout('components.layouts.guest');
    }
}
