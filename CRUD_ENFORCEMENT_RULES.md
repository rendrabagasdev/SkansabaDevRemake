# CRUD Form Enforcement Rules

## 🔒 MANDATORY RULES - CMS Jurusan RPL

**Tanggal Penerapan**: 15 Januari 2026  
**Status**: WAJIB untuk semua modul CRUD CMS

---

## 1. Image Handling - MANDATORY

### 📋 Aturan Wajib

**SEMUA** form CRUD yang memiliki field gambar **WAJIB** menggunakan `ImageService`.

```php
use App\Services\ImageService;

// ❌ SALAH - Direct Storage (DILARANG)
$path = $request->file('image')->store('images');

// ✅ BENAR - Wajib pakai ImageService
$imageService = app(ImageService::class);
$path = $imageService->processAndStore(
    $this->new_image,
    'folder-name',
    [
        'resize' => [800, 600],     // WAJIB
        'compress' => true,          // WAJIB
        'webp' => true,              // WAJIB
    ]
);
```

### 🎯 Operasi Wajib

1. **Resize** - Sesuaikan dimensi gambar dengan kebutuhan
2. **Compress** - Kurangi ukuran file untuk performa
3. **WebP Conversion** - Format modern untuk efisiensi bandwidth
4. **Queue for Heavy Processing** - Proses berat dijalankan di background

### 📦 Implementasi Status

| Module                | Image Field                           | ImageService | Resize            | Compress | WebP | Status           |
| --------------------- | ------------------------------------- | ------------ | ----------------- | -------- | ---- | ---------------- |
| **GlobalSettings**    | logo_primary, logo_secondary, favicon | ✅           | ✅ 400x400, 64x64 | ✅       | ✅   | ✅ **COMPLIANT** |
| **LandingPageSlider** | image                                 | ✅           | ✅ 1920x1080      | ✅       | ✅   | ✅ **COMPLIANT** |
| **ProspekKarir**      | image                                 | ✅           | ✅ 800x600        | ✅       | ✅   | ✅ **COMPLIANT** |

---

## 2. Markdown Handling - MANDATORY

### 📋 Aturan Wajib

**SEMUA** field yang berisi konten panjang (description, deskripsi, content, body) **WAJIB** menggunakan `MarkdownService`.

```php
use App\Services\MarkdownService;

// ❌ SALAH - Direct HTML (DILARANG)
$html = $this->description; // Raw HTML berbahaya

// ✅ BENAR - Wajib pakai MarkdownService
$markdownService = app(MarkdownService::class);

// Validasi sebelum menyimpan
if (!$markdownService->isValid($this->description)) {
    $this->addError('description', 'Format markdown tidak valid.');
    return;
}

// Store raw markdown di database
$data['description'] = $this->description;
```

### 🎯 Fitur Keamanan

1. **HTML Sanitization** - Strip semua raw HTML berbahaya
2. **XSS Prevention** - Block javascript: dan data: URLs
3. **Server-Side Rendering** - Parse di backend, bukan frontend
4. **Safe Tag Whitelist** - Hanya tag HTML aman yang diizinkan

### 📦 Model Accessor (Rendering di Frontend)

```php
// app/Models/ProspekKarir.php
use App\Services\MarkdownService;

/**
 * Get rendered HTML from markdown description
 * MANDATORY: Use MarkdownService for safe server-side rendering
 */
public function getDescriptionHtmlAttribute()
{
    return app(MarkdownService::class)->parseDeskripsi($this->description);
}

/**
 * Get plain text excerpt from description
 */
public function getDescriptionExcerptAttribute()
{
    return app(MarkdownService::class)->excerpt($this->description, 100);
}
```

### 📦 Implementasi Status

| Module                | Markdown Field | MarkdownService | Validation | Server Render | Accessor | Status                              |
| --------------------- | -------------- | --------------- | ---------- | ------------- | -------- | ----------------------------------- |
| **ProspekKarir**      | description    | ✅              | ✅         | ✅            | ✅       | ✅ **COMPLIANT**                    |
| **LandingPageSlider** | subtitle       | ❌              | N/A        | N/A           | N/A      | ✅ **SKIP** (short text, 255 chars) |
| **GlobalSettings**    | N/A            | N/A             | N/A        | N/A           | N/A      | ✅ **N/A** (no long text)           |

---

## 3. Livewire CRUD Pattern

### 📋 Framework Rules

-   **No Page Reload**: Livewire for SPA-like behavior
-   **Modal Forms**: Semua form CRUD dalam modal
-   **Alpine.js**: Client-side reactivity (drag & drop, toggle)

### ✅ Template Livewire Component

```php
<?php

namespace App\Livewire;

use App\Services\ImageService;
use App\Services\MarkdownService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app')]
#[Title('Module Name - CMS RPL')]
class ModuleManager extends Component
{
    use WithFileUploads;

    public function save()
    {
        $this->validate();

        // ✅ MANDATORY: Validate markdown if field exists
        if (isset($this->description)) {
            $markdownService = app(MarkdownService::class);
            if (!$markdownService->isValid($this->description)) {
                $this->addError('description', 'Format markdown tidak valid.');
                return;
            }
        }

        // ✅ MANDATORY: Process image with ImageService
        if ($this->new_image) {
            $imageService = app(ImageService::class);
            $imagePath = $imageService->processAndStore(
                $this->new_image,
                'folder-name',
                [
                    'resize' => [800, 600],
                    'compress' => true,
                    'webp' => true,
                ]
            );
        }

        // Save data...
    }
}
```

---

## 4. Auto-Detection Rules

### 🔍 Field Name Detection

| Field Name Pattern                            | Handler         | Required     |
| --------------------------------------------- | --------------- | ------------ |
| `*image*`, `*foto*`, `*gambar*`               | ImageService    | ✅ MANDATORY |
| `description`, `deskripsi`, `content`, `body` | MarkdownService | ✅ MANDATORY |
| Short text (< 255 chars)                      | Plain text      | ⚠️ Optional  |

---

## 5. Compliance Checklist

### ✅ Pre-Deployment Checklist

-   [ ] **Image Upload**: Semua image upload pakai `ImageService`
-   [ ] **Image Resize**: Dimensi sesuai kebutuhan (tidak oversized)
-   [ ] **Image Compress**: Compression enabled
-   [ ] **WebP Conversion**: Convert to WebP enabled
-   [ ] **Markdown Fields**: Field panjang pakai `MarkdownService`
-   [ ] **Markdown Validation**: Validasi `isValid()` sebelum save
-   [ ] **Model Accessor**: Ada accessor untuk render markdown
-   [ ] **No Direct Storage**: Tidak ada `Storage::put()` atau `store()` langsung
-   [ ] **No Raw HTML**: Tidak ada raw HTML di frontend

---

## 6. Enforcement History

### ✅ Completed Enforcements

**Date**: 15 Januari 2026

1. **ProspekKarirManager.php**

    - ✅ Added MarkdownService validation in `save()`
    - ✅ Added comments for ImageService enforcement
    - ✅ Confirmed resize/compress/webp options

2. **ProspekKarir.php (Model)**

    - ✅ Added `getDescriptionHtmlAttribute()` accessor
    - ✅ Added `getDescriptionExcerptAttribute()` accessor
    - ✅ Import MarkdownService

3. **LandingPageSliderManager.php**

    - ✅ Verified ImageService usage (1920x1080 resize)
    - ✅ Subtitle is short text - skip markdown (COMPLIANT)

4. **GlobalSettingsManager.php**
    - ✅ Verified ImageService for 3 image fields
    - ✅ Logos: 400x400, Favicon: 64x64 (resize + compress + webp)

---

## 7. Violation Penalties

⚠️ **WARNING**: Pelanggaran aturan ini akan menyebabkan:

1. **Security Risk**: XSS vulnerability dari raw HTML
2. **Performance Issues**: Gambar tidak terkompresi (slow page load)
3. **Storage Waste**: File size tidak optimal
4. **Code Rejection**: Code review akan reject PR yang melanggar

---

## 8. Helper Functions

### 🛠️ Available Helpers

```php
// Markdown parsing
$html = markdown($markdownText);
$service = markdownService();

// Image processing (use service directly in Livewire)
$imageService = app(ImageService::class);
```

---

## 📚 References

-   **ImageService Documentation**: `app/Services/ImageService.php`
-   **MarkdownService Documentation**: `app/Services/MarkdownService.php`
-   **Markdown Usage Guide**: `MARKDOWN_HELPER_USAGE.md`

---

**Last Updated**: 15 Januari 2026  
**Enforced By**: GitHub Copilot + Manual Code Review
