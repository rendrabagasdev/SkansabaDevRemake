# Markdown Editor Component - Panduan Penggunaan

## 📋 Overview

Dokumentasi ini menjelaskan cara menggunakan komponen `x-markdown-editor` dalam project Laravel Livewire. Komponen ini menyediakan editor markdown interaktif dengan toolbar, preview, dan keyboard shortcuts.

---

## 🎯 Lokasi File

### Component Files

-   **Blade Component**: `/resources/views/livewire/components/markdown-editor.blade.php`
-   **Livewire Class**: `/app/Livewire/Components/MarkdownEditor.php`
-   **Service**: `/app/Services/MarkdownService.php`

### Contoh Penggunaan di Project

-   `prestasi-manager.blade.php` - Editor untuk deskripsi prestasi
-   `struktur-organisasi-rpl-manager.blade.php` - Editor untuk deskripsi profil anggota

---

## 🚀 Cara Penggunaan

### 1. Basic Usage

```blade
<x-markdown-editor
    modelName="deskripsi"
    label="Deskripsi"
    placeholder="Tuliskan deskripsi..."
    :rows="10"
    :required="true"
/>
```

### 2. Advanced Usage dengan Custom Properties

```blade
<x-markdown-editor
    modelName="deskripsi_md"
    label="Deskripsi (Markdown)"
    placeholder="## Profil Singkat&#10;&#10;Pengalaman 15 tahun di bidang pendidikan teknologi.&#10;&#10;**Kompetensi:**&#10;- Manajemen Pendidikan&#10;- Pengembangan Kurikulum"
    :rows="12"
    :required="false"
/>
```

---

## ⚙️ Component Properties

| Property      | Type    | Required | Default | Deskripsi                               |
| ------------- | ------- | -------- | ------- | --------------------------------------- |
| `modelName`   | string  | ✅ Yes   | -       | Nama property Livewire untuk wire:model |
| `label`       | string  | ✅ Yes   | -       | Label untuk field editor                |
| `placeholder` | string  | ❌ No    | ""      | Placeholder text dalam textarea         |
| `rows`        | integer | ❌ No    | 10      | Jumlah baris textarea                   |
| `required`    | boolean | ❌ No    | false   | Apakah field wajib diisi                |

---

## 🎨 Fitur Toolbar

Markdown editor dilengkapi dengan toolbar yang memiliki tombol-tombol berikut:

### Text Formatting

-   **Bold** - Membuat teks tebal: `**text**`
-   **Italic** - Membuat teks miring: `*text*`

### Structure

-   **Heading** - Menambahkan heading: `## Heading`
-   **List** - Membuat bullet list: `- Item 1`
-   **Link** - Membuat link: `[link text](https://example.com)`
-   **Code** - Membuat inline code: `` `code` ``

### Preview

-   **Toggle Preview** - Menampilkan/menyembunyikan preview markdown

---

## ⌨️ Keyboard Shortcuts

| Shortcut           | Action |
| ------------------ | ------ |
| `Ctrl+B` / `Cmd+B` | Bold   |
| `Ctrl+I` / `Cmd+I` | Italic |

---

## 🔧 Setup di Livewire Component

### 1. Deklarasi Property

```php
class PrestasiManager extends Component
{
    public string $deskripsi = '';

    public function rules()
    {
        return [
            'deskripsi' => 'required|string|min:10',
        ];
    }
}
```

### 2. Validasi

```php
public function save()
{
    $this->validate();

    // Deskripsi sudah dalam format markdown
    Prestasi::create([
        'deskripsi' => $this->deskripsi,
        // ... fields lainnya
    ]);
}
```

---

## 📝 Markdown Rendering

### 1. Menggunakan MarkdownService di Model

```php
use App\Services\MarkdownService;

class Prestasi extends Model
{
    // Accessor untuk render HTML
    public function getDeskripsiHtmlAttribute(): string
    {
        return app(MarkdownService::class)->parseToHtml($this->deskripsi);
    }

    // Accessor untuk excerpt
    public function getDeskripsiExcerptAttribute(): string
    {
        return app(MarkdownService::class)->getExcerpt($this->deskripsi, 150);
    }
}
```

### 2. Menampilkan di Blade View

```blade
{{-- Tampilkan HTML --}}
<div class="prose max-w-none">
    {!! $prestasi->deskripsi_html !!}
</div>

{{-- Tampilkan Excerpt --}}
<p class="text-gray-600">
    {{ $prestasi->deskripsi_excerpt }}
</p>
```

---

## 🎭 Preview di Component

Preview otomatis menggunakan `MarkdownService` untuk render:

```blade
@if($showPreview && $value)
    <div class="prose prose-sm max-w-none">
        {!! app(\App\Services\MarkdownService::class)->parseDeskripsi($value) !!}
    </div>
@endif
```

---

## 🔍 Integration dengan Form Modal

### Contoh di prestasi-manager.blade.php

```blade
<form wire:submit.prevent="save" id="prestasiForm">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Left Column: Fields lainnya --}}
        <div class="space-y-6">
            <!-- Input fields -->
        </div>

        {{-- Right Column: Markdown Editor --}}
        <div>
            <x-markdown-editor
                modelName="deskripsi"
                label="Deskripsi"
                placeholder="## Deskripsi Prestasi&#10;&#10;Tuliskan detail lengkap..."
                :rows="20"
                :required="true"
            />
        </div>
    </div>
</form>
```

---

## 🛠️ Troubleshooting

### Issue 1: Alpine.js Error - "showPreview is not defined"

**Penyebab**: Alpine.js script tidak terinisialisasi dengan benar

**Solusi**: Gunakan komponen `x-markdown-editor` (bukan inline Alpine.js)

```blade
{{-- ❌ JANGAN seperti ini --}}
<div x-data="markdownEditorComponent()">
    <!-- inline toolbar & textarea -->
</div>

{{-- ✅ LAKUKAN seperti ini --}}
<x-markdown-editor modelName="deskripsi" label="Deskripsi" />
```

### Issue 2: Livewire Nested Component Error

**Penyebab**: Menggunakan `livewire:components.markdown-editor` di dalam Livewire parent component

**Solusi**: Gunakan komponen Blade biasa `x-markdown-editor` dengan Alpine.js untuk interaktivitas

### Issue 3: Preview Tidak Muncul

**Penyebab**: Property `showPreview` tidak tersinkronisasi

**Solusi**: Pastikan menggunakan `wire:click="togglePreview"` di tombol preview

---

## 📚 Best Practices

### 1. ✅ Gunakan Component, Bukan Inline Code

```blade
{{-- ✅ GOOD --}}
<x-markdown-editor modelName="deskripsi" label="Deskripsi" />

{{-- ❌ BAD - Jangan copy-paste toolbar & textarea secara manual --}}
```

### 2. ✅ Gunakan MarkdownService untuk Rendering

```php
// ✅ GOOD
{!! app(\App\Services\MarkdownService::class)->parseDeskripsi($deskripsi) !!}

// ❌ BAD - Jangan render langsung
{!! $deskripsi !!}
```

### 3. ✅ Placeholder dengan Line Breaks

```blade
placeholder="## Judul&#10;&#10;Paragraf pertama.&#10;&#10;**Bold text**"
```

Gunakan `&#10;` untuk line break dalam HTML attribute.

### 4. ✅ Validasi Content

```php
protected $rules = [
    'deskripsi' => 'required|string|min:10|max:10000',
];
```

### 5. ✅ Sanitasi di Service

MarkdownService sudah handle sanitasi otomatis, tidak perlu sanitasi manual.

---

## 📦 Dependencies

-   **Laravel 11**: Framework utama
-   **Livewire 3**: Real-time component binding
-   **Alpine.js**: Interaktivitas toolbar
-   **Tailwind CSS**: Styling
-   **League\CommonMark**: Markdown parser (di MarkdownService)

---

## 🎯 Architecture Pattern

```
Parent Livewire Component (PrestasiManager)
    ↓
Blade Component (x-markdown-editor)
    ↓ (Alpine.js untuk toolbar interactivity)
    ↓ (wire:model.live untuk sync dengan parent)
    ↓
MarkdownService (untuk rendering preview)
```

**Key Points:**

-   Parent component menghandle data persistence
-   Blade component menghandle UI & interactivity
-   Alpine.js untuk toolbar actions (insert markdown syntax)
-   Livewire wire:model untuk two-way binding
-   MarkdownService untuk rendering safe HTML

---

## 🚨 Aturan Wajib

### ❌ JANGAN:

1. **JANGAN** gunakan nested Livewire component `<livewire:components.markdown-editor />`
2. **JANGAN** copy-paste inline Alpine.js code ke form modal
3. **JANGAN** render markdown tanpa MarkdownService
4. **JANGAN** hard-code script function di dalam form

### ✅ LAKUKAN:

1. **GUNAKAN** komponen Blade `<x-markdown-editor />`
2. **GUNAKAN** MarkdownService untuk semua markdown rendering
3. **GUNAKAN** wire:model.live untuk real-time sync
4. **IKUTI** pattern dari struktur-organisasi-rpl-manager.blade.php

---

## 📖 Contoh Lengkap

### File: app/Livewire/PrestasiManager.php

```php
<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Prestasi;

class PrestasiManager extends Component
{
    public $deskripsi = '';
    public $showModal = false;

    public function save()
    {
        $validated = $this->validate([
            'deskripsi' => 'required|string|min:10',
        ]);

        Prestasi::create($validated);

        $this->reset('deskripsi');
        $this->showModal = false;
    }

    public function render()
    {
        return view('livewire.prestasi-manager');
    }
}
```

### File: resources/views/livewire/prestasi-manager.blade.php

```blade
<div>
    <form wire:submit.prevent="save">
        <x-markdown-editor
            modelName="deskripsi"
            label="Deskripsi Prestasi"
            placeholder="## Detail Prestasi&#10;&#10;Jelaskan prestasi yang diraih..."
            :rows="15"
            :required="true"
        />

        <button type="submit">Simpan</button>
    </form>
</div>
```

### File: app/Models/Prestasi.php

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Services\MarkdownService;

class Prestasi extends Model
{
    protected $fillable = ['deskripsi'];

    public function getDeskripsiHtmlAttribute(): string
    {
        return app(MarkdownService::class)->parseToHtml($this->deskripsi);
    }
}
```

### Display: resources/views/prestasi/show.blade.php

```blade
<div class="prose max-w-none">
    {!! $prestasi->deskripsi_html !!}
</div>
```

---

## 🎓 Kesimpulan

Markdown Editor Component adalah solusi standar untuk semua input markdown di project ini. Selalu gunakan `<x-markdown-editor />` dan ikuti pattern yang sudah ada di `struktur-organisasi-rpl-manager.blade.php`.

**Reference Files:**

-   ✅ `struktur-organisasi-rpl-manager.blade.php` - Reference implementation
-   ✅ `markdown-editor.blade.php` - Component definition
-   ✅ `MarkdownService.php` - Rendering service

---

**Last Updated**: 16 Januari 2026  
**Version**: 1.0  
**Maintainer**: Development Team
