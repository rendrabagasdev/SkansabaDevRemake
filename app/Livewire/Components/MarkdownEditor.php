<?php

namespace App\Livewire\Components;

use Livewire\Component;
use App\Services\MarkdownService;
use Livewire\Attributes\Modelable;

class MarkdownEditor extends Component
{
    #[Modelable]
    public $value = '';
    
    public $label = 'Deskripsi (Markdown)';
    public $placeholder = "## Heading\n\nTulis konten Anda disini...\n\n**Bold text** atau *italic*\n\n- List item";
    public $rows = 12;
    public $required = false;
    public $showPreview = true;

    public function mount($value = '')
    {
        $this->value = $value;
    }

    public function togglePreview()
    {
        $this->showPreview = !$this->showPreview;
    }

    public function render()
    {
        return view('livewire.components.markdown-editor');
    }
}
