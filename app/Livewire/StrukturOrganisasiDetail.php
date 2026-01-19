<?php

namespace App\Livewire;

use App\Models\StrukturOrganisasiRpl;
use Livewire\Component;

class StrukturOrganisasiDetail extends Component
{
    public $member;
    public $relatedMembers;

    public function mount($id)
    {
        $this->member = StrukturOrganisasiRpl::where('status', 'published')
            ->findOrFail($id);

        // Get other members
        $this->relatedMembers = StrukturOrganisasiRpl::where('status', 'published')
            ->where('id', '!=', $this->member->id)
            ->orderBy('order')
            ->limit(3)
            ->get();
    }

    public function render()
    {
        return view('livewire.struktur-organisasi-detail')
            ->layout('components.layouts.guest');
    }
}
