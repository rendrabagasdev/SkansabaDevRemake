# Image Cropper Component & ImageService - Panduan Penggunaan

## 📋 Overview

Dokumentasi ini menjelaskan cara menggunakan komponen `x-image-cropper` dan `ImageService` untuk upload dan processing gambar di Laravel Livewire project. Sistem ini menggunakan Croppie.js untuk crop interaktif dan otomatis convert ke WebP format.

---

## 🎯 Lokasi File

### Component & Service Files

-   **Blade Component**: `/resources/views/components/image-cropper.blade.php`
-   **Livewire Class**: `/app/View/Components/ImageCropper.php`
-   **Image Service**: `/app/Services/ImageService.php`
-   **Config**: `/config/image.php`

### Contoh Penggunaan di Project

-   `prestasi-manager.blade.php` + `PrestasiManager.php` - Upload gambar prestasi (1:1 ratio)
-   `struktur-organisasi-rpl-manager.blade.php` + `StrukturOrganisasiRplManager.php` - Upload foto profil (1:1 ratio)

---

## 🚀 Cara Penggunaan

### 1. Basic Usage di Blade

```blade
<x-image-cropper
    modelName="gambar"
    label="Gambar Prestasi"
    aspectRatio="1"
    aspectRatioLabel="1:1"
    maxWidth="1200"
    maxHeight="1200"
    :currentImage="$currentGambar"
    previewSize="200"
/>
```

### 2. Advanced Usage dengan Crop Data

```blade
<x-image-cropper
    modelName="new_foto"
    cropDataName="foto_crop_data"
    label="Foto Profil"
    :aspectRatio="1"
    aspectRatioLabel="1:1"
    :maxWidth="1200"
    :maxHeight="1200"
    :required="true"
    :currentImage="$foto"
    :previewSize="200"
/>
```

---

## ⚙️ Component Properties

| Property           | Type          | Required | Default | Deskripsi                                              |
| ------------------ | ------------- | -------- | ------- | ------------------------------------------------------ |
| `modelName`        | string        | ✅ Yes   | -       | Nama property Livewire untuk wire:model                |
| `label`            | string        | ✅ Yes   | -       | Label untuk field upload                               |
| `aspectRatio`      | string/number | ✅ Yes   | -       | Aspect ratio crop (e.g., "1", "16/9", "4/3")           |
| `aspectRatioLabel` | string        | ❌ No    | ""      | Label aspect ratio untuk display (e.g., "1:1", "16:9") |
| `maxWidth`         | integer       | ✅ Yes   | -       | Max width hasil crop (px)                              |
| `maxHeight`        | integer       | ✅ Yes   | -       | Max height hasil crop (px)                             |
| `currentImage`     | string        | ❌ No    | null    | Path gambar existing untuk edit mode                   |
| `previewSize`      | integer       | ❌ No    | 150     | Ukuran preview gambar (px)                             |
| `required`         | boolean       | ❌ No    | false   | Apakah field wajib diisi                               |
| `cropDataName`     | string        | ❌ No    | null    | Nama property untuk menyimpan crop coordinates         |

---

## 🎨 Flow Kerja Image Cropper

```
User Upload File
    ↓
Croppie.js (Client-side Crop)
    ↓
Convert to Base64
    ↓
Send via wire:model ke Livewire
    ↓
Backend: Decode Base64 → Temp File
    ↓
Create UploadedFile Instance
    ↓
ImageService::processAndStore()
    ↓ (compress → convert WebP)
    ↓
Save to storage/app/public/
```

---

## 🔧 Setup di Livewire Component

### 1. Deklarasi Properties

```php
use Livewire\Component;
use Livewire\WithFileUploads;

class PrestasiManager extends Component
{
    use WithFileUploads;

    public $gambar = null;           // Base64 string from cropper
    public $currentGambar = null;    // Path to existing image (for edit)

    protected function rules()
    {
        return [
            'gambar' => 'nullable|string', // Base64 string
        ];
    }
}
```

### 2. Form Submission dengan Image Cropper

```blade
<form
    @submit.prevent="
        if (window.imageCropper && window.imageCropper.sendToLivewire) window.imageCropper.sendToLivewire();
        $wire.save();
    "
>
    <!-- Form fields -->

    <x-image-cropper
        modelName="gambar"
        label="Gambar"
        aspectRatio="1"
        maxWidth="1200"
        maxHeight="1200"
        :currentImage="$currentGambar"
    />

    <button type="submit">Simpan</button>
</form>
```

**⚠️ PENTING:**

-   Form HARUS menggunakan `@submit.prevent` dengan `window.imageCropper.sendToLivewire()`
-   JANGAN gunakan `wire:click="save"` di tombol submit
-   JANGAN gunakan `wire:submit.prevent="save"` tanpa memanggil imageCropper

### 3. Processing di Backend (CORRECT PATTERN)

```php
public function save()
{
    $this->validate();

    $data = [
        'judul' => $this->judul,
        // ... other fields
    ];

    // Handle image upload via ImageService
    if ($this->gambar && str_starts_with($this->gambar, 'data:image')) {
        // Delete old image if editing
        if ($this->isEditMode && $this->currentGambar) {
            $oldPrestasi = Prestasi::find($this->prestasiId);
            if ($oldPrestasi && $oldPrestasi->gambar) {
                $imageService = app(ImageService::class);
                $imageService->delete($oldPrestasi->gambar);
            }
        }

        // Decode base64 to image
        $base64 = preg_replace('/^data:image\/\w+;base64,/', '', $this->gambar);
        $imageData = base64_decode($base64);

        // Create temporary file
        $tempPath = storage_path('app/temp');
        if (!file_exists($tempPath)) {
            mkdir($tempPath, 0755, true);
        }

        $tempFile = $tempPath . '/' . uniqid('crop_') . '.jpg';
        file_put_contents($tempFile, $imageData);

        // Create UploadedFile instance
        $uploadedFile = new \Illuminate\Http\UploadedFile(
            $tempFile,
            uniqid() . '.jpg',
            'image/jpeg',
            null,
            true
        );

        // Process: compress → WebP (already cropped by Croppie)
        $imageService = app(ImageService::class);
        $data['gambar'] = $imageService->processAndStore(
            $uploadedFile,
            'prestasi' // folder name
        );

        // Cleanup temp file
        @unlink($tempFile);

        $this->gambar = null;
    }

    // Save to database
    if ($this->isEditMode) {
        Prestasi::find($this->prestasiId)->update($data);
    } else {
        Prestasi::create($data);
    }
}
```

---

## 🔍 ImageService Methods

### 1. `processAndStore($file, $folder, $maxWidth = null, $maxHeight = null)`

**Deskripsi:** Process dan simpan gambar dengan kompresi dan konversi ke WebP

**Parameters:**

-   `$file` (UploadedFile): File yang di-upload
-   `$folder` (string): Nama folder tujuan di storage
-   `$maxWidth` (int|null): Max width (optional, default dari config)
-   `$maxHeight` (int|null): Max height (optional, default dari config)

**Returns:** `string` - Path relatif ke file (e.g., "prestasi/abc123.webp")

**Usage:**

```php
$imageService = app(ImageService::class);
$path = $imageService->processAndStore($uploadedFile, 'prestasi');
```

### 2. `delete($path)`

**Deskripsi:** Hapus gambar dari storage

**Parameters:**

-   `$path` (string): Path relatif ke file

**Returns:** `bool` - True jika berhasil

**Usage:**

```php
$imageService = app(ImageService::class);
$imageService->delete($oldPrestasi->gambar);
```

### 3. `compressImage($imagePath, $quality = null)`

**Deskripsi:** Kompres gambar (internal method, biasanya dipanggil oleh processAndStore)

### 4. `convertToWebP($imagePath)`

**Deskripsi:** Convert gambar ke WebP format (internal method)

---

## 📝 Pattern Comparison

### ❌ WRONG Pattern (PrestasiManager - OLD)

```php
// JANGAN GUNAKAN INI
if ($this->gambar) {
    $imageService = app(ImageService::class);
    $data['gambar'] = $imageService->storeBase64Image( // ❌ Method tidak ada
        $this->gambar,
        'prestasi',
        1200,
        1200
    );

    if ($this->isEditMode && $this->currentGambar) {
        $imageService->deleteImage($this->currentGambar); // ❌ Method salah
    }
}
```

### ✅ CORRECT Pattern (StrukturOrganisasi & Prestasi - FIXED)

```php
// GUNAKAN PATTERN INI
if ($this->gambar && str_starts_with($this->gambar, 'data:image')) {
    // 1. Delete old image first
    if ($this->isEditMode && $this->currentGambar) {
        $old = Prestasi::find($this->prestasiId);
        if ($old && $old->gambar) {
            app(ImageService::class)->delete($old->gambar);
        }
    }

    // 2. Decode base64 → temp file
    $base64 = preg_replace('/^data:image\/\w+;base64,/', '', $this->gambar);
    $imageData = base64_decode($base64);

    $tempPath = storage_path('app/temp');
    if (!file_exists($tempPath)) mkdir($tempPath, 0755, true);

    $tempFile = $tempPath . '/' . uniqid('crop_') . '.jpg';
    file_put_contents($tempFile, $imageData);

    // 3. Create UploadedFile
    $uploadedFile = new \Illuminate\Http\UploadedFile(
        $tempFile,
        uniqid() . '.jpg',
        'image/jpeg',
        null,
        true
    );

    // 4. Process with ImageService
    $data['gambar'] = app(ImageService::class)->processAndStore(
        $uploadedFile,
        'prestasi'
    );

    // 5. Cleanup
    @unlink($tempFile);
    $this->gambar = null;
}
```

---

## 🛠️ Troubleshooting

### Issue 1: "Call to undefined method storeBase64Image()"

**Penyebab:** Menggunakan method yang tidak ada di ImageService

**Solusi:** Gunakan pattern correct di atas dengan `processAndStore()`

### Issue 2: Image Cropper Tidak Mengirim Data

**Penyebab:** Form tidak memanggil `window.imageCropper.sendToLivewire()`

**Solusi:**

```blade
<form @submit.prevent="
    if (window.imageCropper && window.imageCropper.sendToLivewire)
        window.imageCropper.sendToLivewire();
    $wire.save();
">
```

### Issue 3: Tombol Submit Tidak Berfungsi

**Penyebab:** Tombol submit berada di luar tag `</form>`

**Solusi:** Pastikan tombol submit ada DALAM tag form:

```blade
<form @submit.prevent="...">
    <!-- fields -->

    <button type="submit">Simpan</button>  <!-- ✅ DALAM form -->
</form>
<!-- ❌ JANGAN taruh button di sini -->
```

### Issue 4: "Call to undefined method deleteImage()"

**Penyebab:** Method ImageService adalah `delete()` bukan `deleteImage()`

**Solusi:**

```php
// ❌ SALAH
$imageService->deleteImage($path);

// ✅ BENAR
$imageService->delete($path);
```

### Issue 5: Image Tidak Ter-crop

**Penyebab:** Croppie tidak dipanggil sebelum submit

**Solusi:** Selalu panggil `window.imageCropper.sendToLivewire()` di form submit

---

## 📚 Best Practices

### 1. ✅ Selalu Gunakan ImageService untuk Processing

```php
// ✅ GOOD
$imageService = app(ImageService::class);
$path = $imageService->processAndStore($uploadedFile, 'folder');

// ❌ BAD - Jangan simpan langsung tanpa processing
$path = $uploadedFile->store('folder');
```

### 2. ✅ Validasi Base64 String

```php
if ($this->gambar && str_starts_with($this->gambar, 'data:image')) {
    // Process image
}
```

### 3. ✅ Cleanup Temporary Files

```php
@unlink($tempFile);  // Always cleanup after processing
```

### 4. ✅ Delete Old Image Before Upload New One

```php
if ($this->isEditMode && $this->currentGambar) {
    $old = Model::find($this->id);
    if ($old && $old->image) {
        app(ImageService::class)->delete($old->image);
    }
}
```

### 5. ✅ Reset Property After Processing

```php
$this->gambar = null;  // Prevent re-processing
```

### 6. ✅ Handle Errors Gracefully

```php
try {
    $imageService = app(ImageService::class);
    $path = $imageService->processAndStore($file, 'folder');
} catch (\Exception $e) {
    $this->addError('gambar', 'Gagal upload gambar: ' . $e->getMessage());
    return;
}
```

---

## 🎯 Aspect Ratio Guidelines

| Ratio | Label  | Use Case                            | Dimensions Example |
| ----- | ------ | ----------------------------------- | ------------------ |
| 1:1   | "1:1"  | Profile photos, avatars, thumbnails | 1200x1200          |
| 16:9  | "16:9" | Banner images, hero sections        | 1920x1080          |
| 4:3   | "4:3"  | Standard images, gallery            | 1600x1200          |
| 3:2   | "3:2"  | Photography, artwork                | 1800x1200          |
| 21:9  | "21:9" | Ultrawide banners                   | 2560x1080          |

**Recommended sizes:**

-   **Profile photos:** 1:1 ratio, 1200x1200px
-   **Banners:** 16:9 ratio, 1920x1080px
-   **Gallery images:** 4:3 ratio, 1600x1200px

---

## 📦 Dependencies

-   **Laravel 11**: Framework
-   **Livewire 3**: Real-time components
-   **Alpine.js**: Client-side interactivity
-   **Croppie.js**: Image cropping library
-   **Intervention Image**: Server-side image processing (via ImageService)
-   **WebP Support**: PHP GD/Imagick extension

---

## 🎓 Complete Example

### Blade View

```blade
<form
    @submit.prevent="
        if (window.imageCropper && window.imageCropper.sendToLivewire)
            window.imageCropper.sendToLivewire();
        $wire.save();
    "
>
    <div>
        <label>Judul</label>
        <input type="text" wire:model="judul">
        @error('judul') <span>{{ $message }}</span> @enderror
    </div>

    <div>
        <x-image-cropper
            modelName="gambar"
            label="Gambar Prestasi"
            aspectRatio="1"
            aspectRatioLabel="1:1"
            maxWidth="1200"
            maxHeight="1200"
            :currentImage="$currentGambar"
            previewSize="200"
        />
        @error('gambar') <span>{{ $message }}</span> @enderror
    </div>

    <button type="submit">Simpan</button>
</form>
```

### Livewire Component

```php
<?php

namespace App\Livewire;

use App\Services\ImageService;
use Livewire\Component;
use Livewire\WithFileUploads;

class PrestasiManager extends Component
{
    use WithFileUploads;

    public $judul;
    public $gambar = null;
    public $currentGambar = null;
    public $isEditMode = false;
    public $prestasiId = null;

    protected function rules()
    {
        return [
            'judul' => 'required|string|max:255',
            'gambar' => 'nullable|string',
        ];
    }

    public function openEditModal($id)
    {
        $prestasi = Prestasi::findOrFail($id);
        $this->prestasiId = $prestasi->id;
        $this->judul = $prestasi->judul;
        $this->currentGambar = $prestasi->gambar;
        $this->isEditMode = true;
    }

    public function save()
    {
        $this->validate();

        $data = ['judul' => $this->judul];

        // Process image
        if ($this->gambar && str_starts_with($this->gambar, 'data:image')) {
            // Delete old image
            if ($this->isEditMode && $this->currentGambar) {
                $old = Prestasi::find($this->prestasiId);
                if ($old && $old->gambar) {
                    app(ImageService::class)->delete($old->gambar);
                }
            }

            // Decode base64
            $base64 = preg_replace('/^data:image\/\w+;base64,/', '', $this->gambar);
            $imageData = base64_decode($base64);

            // Create temp file
            $tempPath = storage_path('app/temp');
            if (!file_exists($tempPath)) mkdir($tempPath, 0755, true);

            $tempFile = $tempPath . '/' . uniqid('crop_') . '.jpg';
            file_put_contents($tempFile, $imageData);

            // Create UploadedFile
            $uploadedFile = new \Illuminate\Http\UploadedFile(
                $tempFile, uniqid() . '.jpg', 'image/jpeg', null, true
            );

            // Process
            $data['gambar'] = app(ImageService::class)->processAndStore(
                $uploadedFile, 'prestasi'
            );

            // Cleanup
            @unlink($tempFile);
            $this->gambar = null;
        }

        // Save
        if ($this->isEditMode) {
            Prestasi::find($this->prestasiId)->update($data);
        } else {
            Prestasi::create($data);
        }

        session()->flash('message', 'Data berhasil disimpan!');
    }

    public function render()
    {
        return view('livewire.prestasi-manager');
    }
}
```

---

## 🚨 Aturan Wajib

### ❌ JANGAN:

1. **JANGAN** gunakan method `storeBase64Image()` (tidak ada)
2. **JANGAN** gunakan method `deleteImage()` (gunakan `delete()`)
3. **JANGAN** simpan gambar tanpa ImageService
4. **JANGAN** lupa cleanup temporary files
5. **JANGAN** taruh tombol submit di luar form
6. **JANGAN** gunakan `wire:submit` tanpa memanggil imageCropper

### ✅ LAKUKAN:

1. **GUNAKAN** `processAndStore()` untuk simpan gambar
2. **GUNAKAN** `delete()` untuk hapus gambar
3. **GUNAKAN** form `@submit.prevent` dengan `window.imageCropper.sendToLivewire()`
4. **VALIDASI** base64 string dengan `str_starts_with($this->gambar, 'data:image')`
5. **DELETE** old image sebelum upload new one
6. **CLEANUP** temporary files dengan `@unlink($tempFile)`
7. **RESET** property setelah processing: `$this->gambar = null`
8. **IKUTI** pattern dari StrukturOrganisasiRplManager.php

---

## 📖 Reference Files

**✅ Correct Implementation:**

-   `/app/Livewire/StrukturOrganisasiRplManager.php` - Lines 115-175
-   `/app/Livewire/PrestasiManager.php` - Lines 178-220 (FIXED)

**❌ Wrong Implementation (Fixed):**

-   PrestasiManager.php OLD version (before fix) - used wrong methods

**Core Files:**

-   `/app/Services/ImageService.php` - Image processing service
-   `/resources/views/components/image-cropper.blade.php` - Cropper component
-   `/config/image.php` - Image config

---

**Last Updated**: 16 Januari 2026  
**Version**: 1.0  
**Maintainer**: Development Team  
**Related Docs**: MARKDOWN_EDITOR_COMPONENT_USAGE.md, IMAGE_HELPER_USAGE.md
