<?php

namespace App\Livewire;

use App\Models\StrukturOrganisasiRpl;
use Livewire\Component;

class StrukturOrganisasiPublic extends Component
{
    public function render()
    {
        $members = StrukturOrganisasiRpl::query()
            ->published()
            ->ordered()
            ->get();

        return view('livewire.struktur-organisasi-public', [
            'members' => $members,
        ])->layout('components.layouts.guest');
    }
}
