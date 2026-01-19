<?php

namespace App\Livewire\Components;

use App\Models\GlobalSetting;
use Livewire\Component;

class Footer extends Component
{
    public function render()
    {
        $globalSettings = GlobalSetting::instance();
        
        return view('livewire.components.footer', [
            'globalSettings' => $globalSettings,
        ]);
    }
}
