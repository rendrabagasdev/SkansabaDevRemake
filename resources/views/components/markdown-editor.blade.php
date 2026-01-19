@props([
    'modelName' => 'description',
    'label' => 'Deskripsi (Markdown)',
    'placeholder' => "## Heading\n\nTulis konten Anda disini...\n\n**Bold text** atau *italic*\n\n- List item",
    'rows' => 12,
    'required' => false,
    'value' => ''
])

<div x-data="{
    content: @entangle($modelName).live,
    
    insertMarkdown(syntax, selectStart, selectEnd) {
        const textarea = this.$refs.textarea;
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const text = textarea.value;
        const selectedText = text.substring(start, end);

        let newText;
        let newCursorPos;

        if (selectedText) {
            const beforeSyntax = syntax.substring(0, selectStart);
            const afterSyntax = syntax.substring(selectEnd);
            newText = text.substring(0, start) + beforeSyntax + selectedText + afterSyntax + text.substring(end);
            newCursorPos = start + beforeSyntax.length + selectedText.length;
        } else {
            newText = text.substring(0, start) + syntax + text.substring(end);
            newCursorPos = start + selectStart;
        }

        textarea.value = newText;
        this.content = newText;

        textarea.focus();
        textarea.setSelectionRange(newCursorPos, start + selectEnd);
    },

    insertBold() {
        this.insertMarkdown('**text**', 2, 6);
    },

    insertItalic() {
        this.insertMarkdown('*text*', 1, 5);
    },

    insertHeading() {
        this.insertMarkdown('\n## Heading\n', 4, 11);
    },

    insertList() {
        this.insertMarkdown('\n- Item 1\n- Item 2\n- Item 3\n', 3, 9);
    },

    insertLink() {
        this.insertMarkdown('[link text](https://example.com)', 1, 10);
    },

    insertCode() {
        this.insertMarkdown('`code`', 1, 5);
    },

    renderMarkdown(markdown) {
        if (!markdown) return '';
        
        let html = markdown
            .replace(/^### (.*$)/gim, '<h3 class=\'text-lg font-semibold text-gray-800 mb-2 mt-3\'>$1</h3>')
            .replace(/^## (.*$)/gim, '<h2 class=\'text-xl font-bold text-gray-800 mb-3 mt-4\'>$1</h2>')
            .replace(/^# (.*$)/gim, '<h1 class=\'text-2xl font-bold text-gray-900 mb-4 mt-5\'>$1</h1>')
            .replace(/\*\*(.*?)\*\*/gim, '<strong class=\'font-semibold\'>$1</strong>')
            .replace(/\*(.*?)\*/gim, '<em class=\'italic\'>$1</em>')
            .replace(/^- (.*$)/gim, '<li class=\'ml-4\'>$1</li>')
            .replace(/\n\n/gim, '</p><p class=\'text-gray-700 leading-relaxed mb-3\'>')
            .replace(/`(.*?)`/gim, '<code class=\'bg-gray-100 text-red-600 px-1 rounded text-sm font-mono\'>$1</code>');
        
        html = html.replace(/(<li.*?<\/li>)+/gim, '<ul class=\'list-disc list-inside space-y-1 mb-3 text-gray-700\'>$&</ul>');
        
        if (!html.startsWith('<h')) {
            html = '<p class=\'text-gray-700 leading-relaxed mb-3\'>' + html + '</p>';
        }
        
        return html;
    }
}"
    <label class="block text-sm font-medium text-gray-700 mb-2">
        {{ $label }}
        @if($required) <span class="text-red-500">*</span> @endif
    </label>

    {{-- Toolbar --}}
    <div class="flex items-center gap-1 p-2 bg-gray-50 border border-gray-300 rounded-t-lg">
        <button 
            type="button"
            @click="insertBold()"
            class="p-2 hover:bg-gray-200 rounded transition-colors"
            title="Bold (Ctrl+B)"
        >
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path d="M5 3h6a4 4 0 014 4 4 4 0 01-1.5 3.1A4.5 4.5 0 0115 14a4.5 4.5 0 01-4.5 4.5H5V3zm2 2v4h4a2 2 0 100-4H7zm0 6v4h3.5a2.5 2.5 0 100-5H7z"/>
            </svg>
        </button>

        <button 
            type="button"
            @click="insertItalic()"
            class="p-2 hover:bg-gray-200 rounded transition-colors"
            title="Italic (Ctrl+I)"
        >
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 3h4v2h-1.5l-3 10H11v2H7v-2h1.5l3-10H10V3z"/>
            </svg>
        </button>

        <div class="w-px h-6 bg-gray-300 mx-1"></div>

        <button 
            type="button"
            @click="insertHeading()"
            class="px-2 py-1 hover:bg-gray-200 rounded transition-colors text-sm font-semibold"
            title="Heading"
        >
            H
        </button>

        <button 
            type="button"
            @click="insertList()"
            class="p-2 hover:bg-gray-200 rounded transition-colors"
            title="List"
        >
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path d="M3 6h2v2H3V6zm0 4h2v2H3v-2zm0 4h2v2H3v-2zm4-8h10v2H7V6zm0 4h10v2H7v-2zm0 4h10v2H7v-2z"/>
            </svg>
        </button>

        <button 
            type="button"
            @click="insertLink()"
            class="p-2 hover:bg-gray-200 rounded transition-colors"
            title="Link"
        >
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10.5 12a2.5 2.5 0 01-4.33-1.67l.83.83a1 1 0 001.42 0l3.5-3.5a1 1 0 000-1.42l-.83-.83A2.5 2.5 0 0114.5 9.5l-3.5 3.5a2.5 2.5 0 01-3.54 0l-.83-.83A2.5 2.5 0 019.5 8.5l.83.83z"/>
            </svg>
        </button>

        <button 
            type="button"
            @click="insertCode()"
            class="p-2 hover:bg-gray-200 rounded transition-colors"
            title="Code"
        >
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path d="M6.3 5.7l-3.6 3.6a1 1 0 000 1.4l3.6 3.6 1.4-1.4L4.4 10l3.3-3.3-1.4-1.4zm7.4 0l3.6 3.6a1 1 0 010 1.4l-3.6 3.6-1.4-1.4L15.6 10l-3.3-3.3 1.4-1.4z"/>
            </svg>
        </button>
    </div>

    {{-- Editor Textarea --}}
    <textarea 
        x-ref="textarea"
        wire:model.live="{{ $modelName }}"
        rows="{{ $rows }}"
        class="w-full px-3 py-2 border border-gray-300 rounded-b-lg focus:ring-2 focus:ring-[#12B4E0] focus:border-transparent font-mono text-sm"
        placeholder="{{ $placeholder }}"
        @keydown.ctrl.b.prevent="insertBold()"
        @keydown.meta.b.prevent="insertBold()"
        @keydown.ctrl.i.prevent="insertItalic()"
        @keydown.meta.i.prevent="insertItalic()"
    ></textarea>

    @error($modelName) 
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
    @enderror

    {{-- Live Preview --}}
    <div x-show="content && content.length > 0">
        <div class="mt-3 p-4 bg-gray-50 rounded-lg border border-gray-200 max-h-64 overflow-y-auto">
            <p class="text-xs font-semibold text-gray-600 mb-3 uppercase tracking-wide flex items-center">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                </svg>
                Preview:
            </p>
            <div class="prose prose-sm max-w-none" x-html="renderMarkdown(content)"></div>
        </div>
    </div>
</div>
