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

        // Get related members with smart algorithm:
        // 1. Prioritize members from same unit (if unit field exists)
        // 2. Mix with random members from other units
        // 3. Exclude current member
        $sameUnitMembers = StrukturOrganisasiRpl::where('status', 'published')
            ->where('id', '!=', $this->member->id)
            ->when($this->member->unit ?? null, function ($query, $unit) {
                return $query->where('unit', $unit);
            })
            ->orderBy('order')
            ->limit(2)
            ->get();

        // If we need more members, get random ones from other units
        $remainingCount = 3 - $sameUnitMembers->count();
        
        if ($remainingCount > 0) {
            $otherMembers = StrukturOrganisasiRpl::where('status', 'published')
                ->where('id', '!=', $this->member->id)
                ->when($this->member->unit ?? null, function ($query, $unit) {
                    return $query->where('unit', '!=', $unit);
                })
                ->inRandomOrder()
                ->limit($remainingCount)
                ->get();
            
            $this->relatedMembers = $sameUnitMembers->merge($otherMembers);
        } else {
            $this->relatedMembers = $sameUnitMembers;
        }
    }

    public function render()
    {
        return view('livewire.struktur-organisasi-detail')
            ->layout('components.layouts.guest');
    }
}
