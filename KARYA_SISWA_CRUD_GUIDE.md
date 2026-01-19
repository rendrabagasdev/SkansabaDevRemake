# Karya Siswa - CRUD Implementation Guide

## 📋 Overview

This document describes the complete implementation of the **Karya Siswa (Student Work)** CRUD module in the Skansaba project. The module allows admin and guru to manage student projects with support for markdown descriptions, image cropping (16:9 ratio), and public API access for published works.

---

## 🎯 Lokasi File

### Livewire Components

- **Manager Component**: `/app/Livewire/KaryaSiswaManager.php`
- **Blade View**: `/resources/views/livewire/karya-siswa-manager.blade.php`

### Model & Database

- **Model**: `/app/Models/KaryaSiswa.php`
- **Migration**: `/database/migrations/2026_01_15_123612_create_karya_siswas_table.php`

### API

- **Controller**: `/app/Http/Controllers/KaryaSiswaController.php`
- **Routes**: `/routes/api.php`
- **Requests**:
    - `/app/Http/Requests/StoreKaryaSiswaRequest.php`
    - `/app/Http/Requests/UpdateKaryaSiswaRequest.php`

### Web Routes

- Route file: `/routes/web.php` (line ~47)
- Route name: `karya-siswa.index`
- Route path: `/karya-siswa`

---

## 🗄️ Database Schema

### Table: `karya_siswas`

```sql
CREATE TABLE karya_siswas (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    judul VARCHAR(255) NOT NULL,
    deskripsi LONGTEXT NULL,                              -- Markdown format
    kategori VARCHAR(255) NULL,                           -- web, mobile, desktop, game, iot, lainnya
    teknologi VARCHAR(255) NULL,                          -- React, Laravel, etc
    nama_siswa VARCHAR(255) NOT NULL,
    kelas VARCHAR(255) NOT NULL,                          -- XII RPL 1, etc
    tahun YEAR NOT NULL,
    gambar VARCHAR(255) NULL,                             -- Path to WebP image (16:9 ratio)
    url_demo VARCHAR(255) NULL,
    url_repo VARCHAR(255) NULL,
    status ENUM('draft','review','published','archived') DEFAULT 'draft',
    published_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,                            -- Soft deletes
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_status_published (status, published_at),
    INDEX idx_tahun (tahun)
);
```

---

## 📊 Architecture & Flow

### CRUD Operations Flow

```
┌─────────────────────────────────────────────────────────────┐
│                    KaryaSiswaManager.php                     │
│            (Livewire Component - Real-time CRUD)            │
└─────────────────────────────────────────────────────────────┘
                            │
        ┌───────────────────┼───────────────────┐
        │                   │                   │
        ▼                   ▼                   ▼
   [CREATE]            [READ]               [UPDATE]
   openCreateModal()   loadKaryaSiswas()   openEditModal()
   save()              with filters,        save()
                       sorting              delete()
                       search               restore()
                                           forceDelete()
        │                   │                   │
        └───────────────────┼───────────────────┘
                            │
        ┌───────────────────┼───────────────────┐
        │                   │                   │
        ▼                   ▼                   ▼
   ImageService      MarkdownService      KaryaSiswa Model
   - Crop            - Parse              - Relationships
   - Compress        - Sanitize           - Accessors
   - WebP Convert    - Excerpt            - Timestamps
```

### Image Processing (16:9 Ratio)

```
User selects file
        │
        ▼
Croppie.js (Client-side)
- Aspect ratio lock: 16:9
- User drag/zoom to crop
        │
        ▼
Base64 encode crop result
        │
        ▼
Form submit → window.imageCropper.sendToLivewire()
        │
        ▼
Backend receives base64 string in $gambar property
        │
        ├─ Decode base64 to binary
        │
        ├─ Create temporary file
        │
        ├─ Create UploadedFile instance
        │
        ▼
ImageService::processAndStore()
        │
        ├─ Validate dimensions
        │
        ├─ Compress with quality settings
        │
        ├─ Convert to WebP format
        │
        ├─ Store to storage/karya-siswa/
        │
        └─ Return path string

Delete old image if editing
```

---

## 🔧 Component Properties

### KaryaSiswaManager.php Properties

#### List Management

```php
public $karyaSiswas = [];                // Loaded data
public $search = '';                     // Search query
public $filterStatus = '';               // Filter: draft/review/published/archived
public $filterKategori = '';             // Filter: web/mobile/desktop/game/iot/lainnya
public $filterTahun = '';                // Filter: year
public $sortField = 'created_at';        // Sort column
public $sortDirection = 'desc';          // Sort direction (asc/desc)
```

#### Form Management

```php
public $showModal = false;               // Modal visibility
public $isEditMode = false;              // Edit vs Create mode
public $karyaSiswaId = null;             // Current record ID (for edit)
```

#### Form Fields

```php
public $judul = '';                      // Title (required)
public $deskripsi = '';                  // Markdown description (required, min:10)
public $kategori = '';                   // web/mobile/desktop/game/iot/lainnya (required)
public $teknologi = '';                  // Tech stack, e.g., React, Laravel (required)
public $nama_siswa = '';                 // Student name (required)
public $kelas = '';                      // Class, e.g., XII RPL 1 (required)
public $tahun = '';                      // Year (required, 2000-current+1)
public $gambar = null;                   // Base64 from cropper
public $currentGambar = null;            // Existing image path (for edit display)
public $url_demo = '';                   // Demo URL (optional, url validation)
public $url_repo = '';                   // Repository URL (optional, url validation)
public $status = 'draft';                // draft/review/published/archived (required)
```

---

## 📝 Validation Rules

### StoreKaryaSiswaRequest & UpdateKaryaSiswaRequest

```php
[
    'judul' => ['required', 'string', 'max:255'],
    'deskripsi' => ['required', 'string', 'min:10'],
    'kategori' => ['required', 'string', 'max:255'],
    'teknologi' => ['required', 'string', 'max:255'],
    'nama_siswa' => ['required', 'string', 'max:255'],
    'kelas' => ['required', 'string', 'max:255'],
    'tahun' => ['required', 'integer', 'min:2000', 'max:' . (date('Y') + 1)],
    'gambar' => ['nullable', 'string'],                      // Base64 from cropper
    'url_demo' => ['nullable', 'url', 'max:255'],
    'url_repo' => ['nullable', 'url', 'max:255'],
    'status' => ['required', 'in:draft,review,published,archived'],
]
```

---

## 🚀 Usage Guide

### 1. Create New Karya Siswa

#### Via Livewire UI

```blade
<!-- Trigger modal -->
<button wire:click="openCreateModal">Tambah Karya</button>

<!-- Modal form automatically appears with image cropper & markdown editor -->
```

#### Via API (POST)

```bash
curl -X POST "https://your-domain/api/karya-siswa" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: multipart/form-data" \
  -F "judul=E-Commerce Platform" \
  -F "deskripsi=## Deskripsi Aplikasi\n\nAplikasi e-commerce dengan fitur utama..." \
  -F "kategori=web" \
  -F "teknologi=React, Laravel" \
  -F "nama_siswa=Budi Santoso" \
  -F "kelas=XII RPL 1" \
  -F "tahun=2026" \
  -F "gambar=@screenshot.jpg" \
  -F "url_demo=https://ecommerce.example.com" \
  -F "url_repo=https://github.com/user/ecommerce" \
  -F "status=published"
```

### 2. Read/List Karya Siswa

#### Via Livewire UI

- Automatic load on component mount
- Filter by status, kategori, tahun
- Search by judul, nama siswa, kelas, teknologi
- Sort by any column

#### Via API (GET)

```bash
# Get published works (limit 6)
curl -X GET "https://your-domain/api/karya-siswa/published?limit=6&kategori=web&tahun=2026"

# Response:
{
  "data": [
    {
      "id": 1,
      "judul": "E-Commerce Platform",
      "deskripsi_excerpt": "Aplikasi e-commerce dengan fitur utama...",
      "kategori": "web",
      "teknologi": "React, Laravel",
      "nama_siswa": "Budi Santoso",
      "kelas": "XII RPL 1",
      "tahun": 2026,
      "gambar": "karya-siswa/abc123.webp",
      "image_url": "/storage/karya-siswa/abc123.webp",
      "url_demo": "https://ecommerce.example.com",
      "url_repo": "https://github.com/user/ecommerce",
      "published_at": "2026-01-18T10:30:00Z"
    }
  ],
  "total": 15,
  "limit": 6
}
```

### 3. Update Karya Siswa

#### Via Livewire UI

```php
// Click Edit button → triggers openEditModal($id)
// Form loads with current data
// Modify fields
// Submit form → save() method handles update
// Image cropper only processes if new image selected
```

#### Via API

Not implemented yet (requires additional controller method)

### 4. Delete Karya Siswa

#### Via Livewire UI

```php
// Soft delete
wire:click="delete($id)"

// Restore (undo soft delete)
wire:click="restore($id)"

// Permanent delete
wire:click="forceDelete($id)"
```

#### Image Cleanup

- Automatically deletes associated image from storage when record is deleted
- Works for both soft and force delete

---

## 🎨 Components Used

### 1. Image Cropper (`x-image-cropper`)

**Location**: `/resources/views/components/image-cropper.blade.php`

```blade
<x-image-cropper
    modelName="gambar"
    label="Screenshot Karya"
    aspectRatio="16/9"                 <!-- Locked 16:9 ratio -->
    aspectRatioLabel="16:9"
    :maxWidth="1920"                   <!-- Output dimensions -->
    :maxHeight="1080"
    :currentImage="$currentGambar"     <!-- Edit mode display -->
    :previewSize="200"
/>
```

**Features**:

- Client-side cropping with Croppie.js
- Aspect ratio lock (16:9)
- Rotate and flip controls
- Real-time preview
- Base64 output to wire:model property

### 2. Markdown Editor (`x-markdown-editor`)

**Location**: `/resources/views/livewire/components/markdown-editor.blade.php`

```blade
<x-markdown-editor
    modelName="deskripsi"
    label="Deskripsi Karya"
    placeholder="## Deskripsi Karya\n\nJelaskan fitur..."
    :rows="12"
    :required="true"
/>
```

**Features**:

- Markdown toolbar (bold, italic, heading, list, link, code)
- Live preview toggle
- Syntax highlighting
- Wire:model.live binding

---

## 🔄 Methods Reference

### KaryaSiswaManager Methods

#### Data Loading

```php
public function loadKaryaSiswas()
{
    // Loads all karya_siswas with filters and sorting
    // Called on mount and after updates
}
```

#### CRUD Operations

```php
public function openCreateModal()      // Show create form
public function openEditModal($id)     // Load record for edit
public function save()                 // Create or update
public function delete($id)            // Soft delete
public function restore($id)           // Undo soft delete
public function forceDelete($id)       // Permanent delete
```

#### Helpers

```php
public function closeModal()           // Hide form modal
private function resetForm()           // Clear all form fields
```

#### Event Listeners

```php
public function updatedSearch()        // Reload on search change
public function updatedFilterStatus()  // Reload on status filter
public function updatedFilterKategori()// Reload on kategori filter
public function updatedFilterTahun()   // Reload on year filter
public function sortBy($field)         // Toggle sort direction
```

### KaryaSiswa Model Methods

#### Accessors

```php
// Get rendered HTML
$karya->deskripsi_html  // Calls MarkdownService::parseDeskripsi()

// Get excerpt
$karya->deskripsi_excerpt  // First 150 chars from markdown

// Get storage URL
$karya->image_url  // Asset URL to gambar
```

#### Relationships

```php
// Get creator
$karya->user()  // BelongsTo User
```

---

## 📱 Image Specifications

### Input Image

- **Format**: JPG, PNG, WebP
- **Max file size**: Not limited in cropper (browser constraint ~50MB)
- **Aspect ratio**: Can be any ratio, user crops to 16:9

### Output Image

- **Format**: WebP (automatically converted)
- **Dimensions**: 1920×1080px
- **Aspect ratio**: 16:9 (locked in cropper)
- **Quality**: 90% (configurable in ImageService)
- **Storage path**: `/storage/app/public/karya-siswa/`
- **URL**: `/storage/karya-siswa/filename.webp`

### Compression

- Image is automatically compressed by ImageService
- WebP format reduces file size by ~30-40% vs JPEG
- Quality set to 90% for good balance

---

## 🎯 Markdown Support

### Allowed Syntax

````markdown
## Headings

### Subheading

**Bold text**
_Italic text_

- Bullet list
- Item 2
    - Nested item

1. Numbered list
2. Item 2

[Link text](https://example.com)

`inline code`

`code block`

> Blockquote
````

### Rendering

- Parsed on server using `League\CommonMark`
- Automatically sanitized (HTML tags removed)
- Displayed with safe HTML output

---

## 🔐 Authorization & Permissions

### Routes

- **Create**: Protected by `middleware('auth', 'role:admin,guru')`
- **Read**: Protected by `middleware('auth', 'role:admin,guru')`
- **Update**: Protected by `middleware('auth', 'role:admin,guru')`
- **Delete**: Protected by `middleware('auth', 'role:admin,guru')`

### API

- **POST /api/karya-siswa**: Protected by `middleware('auth:sanctum')`
- **GET /api/karya-siswa/published**: Public (no auth required)

---

## 🚨 Error Handling

### Validation Errors

- Displayed inline below form fields
- Flash messages for save/delete operations
- Livewire automatically validates on form submission

### Image Processing Errors

```php
try {
    $imageService->processAndStore($file, 'karya-siswa');
} catch (\Exception $e) {
    session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
}
```

### Soft Delete Handling

- Deleted records remain in database with `deleted_at` timestamp
- Not shown in normal queries
- Can be restored
- Can be permanently deleted with `forceDelete()`

---

## ✅ Best Practices

### 1. Always Use Image Cropper

```blade
<!-- ✅ CORRECT -->
<x-image-cropper modelName="gambar" aspectRatio="16/9" ... />

<!-- ❌ WRONG - Direct file upload without cropper -->
<input type="file" name="gambar">
```

### 2. Always Use Markdown Editor

```blade
<!-- ✅ CORRECT -->
<x-markdown-editor modelName="deskripsi" ... />

<!-- ❌ WRONG - Plain textarea without editor -->
<textarea name="deskripsi"></textarea>
```

### 3. Always Validate in Request

```php
// ✅ CORRECT - Use Form Request
public function save()
{
    $this->validate([...]);  // Uses component properties
}

// ❌ WRONG - Inline validation
if ($this->judul === '') { ... }
```

### 4. Always Call imageCropper.sendToLivewire()

```blade
<!-- ✅ CORRECT -->
<form @submit.prevent="
    if (window.imageCropper && window.imageCropper.sendToLivewire)
        window.imageCropper.sendToLivewire();
    $wire.save();
">

<!-- ❌ WRONG - Missing imageCropper call -->
<form @submit.prevent="$wire.save()">
```

### 5. Always Delete Old Image Before Upload New

```php
if ($this->isEditMode && $this->currentGambar) {
    $imageService->delete($this->currentGambar);
}
```

### 6. Always Set published_at When Status = published

```php
if ($this->status === 'published') {
    $data['published_at'] = now();
}
```

---

## 📚 Guest Usage (Public API)

### Display Published Works

```blade
<!-- In frontend page -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    @foreach($publishedWorks as $work)
        <div class="rounded-lg overflow-hidden shadow-lg">
            <img
                src="{{ $work['image_url'] }}"
                alt="{{ $work['judul'] }}"
                class="w-full h-48 object-cover"
            >
            <div class="p-4">
                <h3 class="font-bold">{{ $work['judul'] }}</h3>
                <p class="text-sm text-gray-600">{{ $work['nama_siswa'] }} - {{ $work['kelas'] }}</p>
                <p class="text-xs text-gray-500 mt-2">{{ $work['kategori'] }} • {{ $work['teknologi'] }}</p>
                <p class="text-sm mt-3 line-clamp-2">{{ $work['deskripsi_excerpt'] }}</p>
                <div class="mt-4 flex gap-2">
                    @if($work['url_demo'])
                        <a href="{{ $work['url_demo'] }}" target="_blank" class="text-blue-600">Demo</a>
                    @endif
                    @if($work['url_repo'])
                        <a href="{{ $work['url_repo'] }}" target="_blank" class="text-blue-600">Repository</a>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>
```

### JavaScript Integration

```javascript
// Fetch published works
fetch("/api/karya-siswa/published?limit=6&kategori=web")
    .then((res) => res.json())
    .then((data) => {
        console.log(data.data); // Array of 6 works
        // Render to DOM
    });
```

---

## 🔗 Related Documentation

- [IMAGE_CROPPER_COMPONENT_USAGE.md](IMAGE_CROPPER_COMPONENT_USAGE.md) - Image cropper detailed guide
- [MARKDOWN_EDITOR_COMPONENT_USAGE.md](MARKDOWN_EDITOR_COMPONENT_USAGE.md) - Markdown editor guide
- [CRUD_ENFORCEMENT_RULES.md](CRUD_ENFORCEMENT_RULES.md) - CRUD pattern standards
- [IMAGE_HELPER_USAGE.md](IMAGE_HELPER_USAGE.md) - ImageService documentation

---

## 🐛 Troubleshooting

### Issue: Image not saving

**Check**:

1. Cropper modal open and visible
2. `window.imageCropper.sendToLivewire()` called on form submit
3. Storage directory exists and is writable

### Issue: Markdown not rendering

**Check**:

1. Using `$karya->deskripsi_html` (not `$karya->deskripsi`)
2. MarkdownService is properly injected
3. Content is valid markdown

### Issue: Soft delete showing deleted records

**Check**:

1. Query includes `->withoutTrashed()` or doesn't use `->withTrashed()`
2. Component is reloading after delete

---

## 📊 Example Response

### GET /api/karya-siswa/published

```json
{
    "data": [
        {
            "id": 1,
            "judul": "E-Commerce Platform",
            "deskripsi_excerpt": "Aplikasi e-commerce modern dengan fitur pencarian, keranjang belanja, dan sistem pembayaran terintegrasi.",
            "kategori": "web",
            "teknologi": "React, Laravel, PostgreSQL",
            "nama_siswa": "Budi Santoso",
            "kelas": "XII RPL 1",
            "tahun": 2026,
            "gambar": "karya-siswa/abc123def456.webp",
            "image_url": "/storage/karya-siswa/abc123def456.webp",
            "url_demo": "https://ecommerce-demo.example.com",
            "url_repo": "https://github.com/budisantoso/ecommerce-platform",
            "published_at": "2026-01-18T10:30:00Z"
        },
        {
            "id": 2,
            "judul": "Mobile Learning App",
            "deskripsi_excerpt": "Aplikasi pembelajaran mobile untuk siswa dengan fitur quiz, video tutorial, dan progress tracking.",
            "kategori": "mobile",
            "teknologi": "Flutter, Firebase",
            "nama_siswa": "Siti Nurhaliza",
            "kelas": "XII RPL 1",
            "tahun": 2026,
            "gambar": "karya-siswa/xyz789uvw012.webp",
            "image_url": "/storage/karya-siswa/xyz789uvw012.webp",
            "url_demo": null,
            "url_repo": "https://github.com/sitinurhaliza/learning-app",
            "published_at": "2026-01-17T15:45:00Z"
        }
    ],
    "total": 15,
    "limit": 6
}
```

---

## 📞 Support

For issues or questions:

1. Check the related documentation files
2. Review the example implementation in the code
3. Check the validation rules in Request classes
4. Test API endpoints using the provided curl examples

---

**Last Updated**: 18 January 2026  
**Version**: 1.0  
**Maintainer**: Development Team
