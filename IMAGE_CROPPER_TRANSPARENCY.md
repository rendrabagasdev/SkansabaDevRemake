# Image Cropper & Transparency Support

## ✅ Perbaikan yang Sudah Dilakukan

### 1. **Full Image Coverage saat Crop (Fixed)**

**Masalah Sebelumnya:**

- Saat crop dengan rasio 1:1, image langsung zoom in
- User tidak bisa lihat full image
- Crop tidak mencakup seluruh gambar

**Solusi:**

- ✅ Croppie sekarang **start dengan zoom 0** (full image)
- ✅ Auto-calculate **best fit zoom** untuk show entire image
- ✅ User bisa **zoom in/out** dengan mouse wheel (Ctrl + scroll)
- ✅ User bisa **drag** untuk adjust posisi crop

**Cara Kerja:**

```javascript
// 1. Bind image dengan zoom 0
this.croppie.bind({
    url: imageData,
    zoom: 0, // Full image, no zoom
});

// 2. Calculate best fit untuk viewport
const scaleX = viewportWidth / imageWidth;
const scaleY = viewportHeight / imageHeight;
const scale = Math.min(scaleX, scaleY);

// 3. Set zoom agar full image visible
this.croppie.setZoom(scale);
```

### 2. **Transparency Support untuk WebP (Fixed)**

**Masalah Sebelumnya:**

- Background transparan jadi **hitam** setelah convert ke WebP
- Alpha channel tidak preserved

**Solusi:**

- ✅ **GD Driver otomatis preserve transparency** di Intervention Image v3
- ✅ WebP encoding sudah **support alpha channel**
- ✅ PNG transparan → WebP transparan (auto)

**Important Notes:**

- ✅ Format **PNG** dengan transparency → WebP **tetap transparan**
- ✅ Format **JPG** (tidak ada alpha) → WebP dengan **background putih**
- ✅ Quality setting (80%) **tidak affect transparency**

## 🎯 Cara Menggunakan

### Upload Gambar dengan Transparency

```blade
<x-image-cropper
    modelName="logo"
    label="Logo (Transparan Supported)"
    aspectRatio="1"
    aspectRatioLabel="1:1"
    maxWidth="300"
    maxHeight="300"
/>
```

**Tips untuk Transparency:**

1. ✅ Upload file **PNG** dengan background transparan
2. ✅ Crop seperti biasa - transparency akan preserved
3. ✅ Hasil akhir: **WebP dengan transparency** (ukuran lebih kecil dari PNG)

### Zoom Control

- **Full Image View**: Otomatis saat load
- **Zoom In**: Drag slider atau Ctrl + Scroll Up
- **Zoom Out**: Drag slider atau Ctrl + Scroll Down
- **Pan**: Drag/klik gambar untuk geser posisi

## 🔧 Technical Details

### Croppie Settings

```javascript
{
    viewport: { width, height },        // Crop area
    boundary: { width*2, height*2 },    // Container size
    showZoomer: true,                   // Show zoom slider
    enableOrientation: true,            // Allow rotation
    enableResize: false,                // Fixed crop ratio
    mouseWheelZoom: 'ctrl'             // Ctrl + Scroll to zoom
}
```

### WebP Encoding with Transparency

```php
// Intervention Image v3 dengan GD Driver
$image->toWebp(80);  // Quality 80%, auto-preserve alpha
```

**GD Driver Advantages:**

- ✅ Built-in PHP (no extra dependencies)
- ✅ Auto-handle transparency
- ✅ Faster processing
- ✅ Lower memory usage

## 🐛 Troubleshooting

### Transparency Masih Jadi Hitam?

**Kemungkinan:**

1. ❌ Source image bukan PNG transparan (cek dengan image editor)
2. ❌ Source image PNG tapi sudah di-flatten (no alpha channel)
3. ❌ Browser cache - hard refresh (Ctrl+F5)

**Cara Cek:**

```bash
# Check if image has alpha channel
file path/to/image.png
# Output should show: "PNG image data, 300 x 300, 8-bit/color RGBA"
#                                                          ^^^^^ <- RGBA berarti ada alpha
```

### Crop Masih Zoom In?

**Solusi:**

1. ✅ Hard refresh browser (Ctrl+F5)
2. ✅ Clear cache: `php artisan optimize:clear`
3. ✅ Rebuild: `npm run build`

## 📦 Supported Formats

| Input Format | Transparency | Output WebP  | Notes          |
| ------------ | ------------ | ------------ | -------------- |
| PNG (RGBA)   | ✅ Yes       | ✅ Preserved | Best for logos |
| PNG (RGB)    | ❌ No        | ⚪ White BG  | Like JPG       |
| JPG          | ❌ No        | ⚪ White BG  | Standard       |
| WebP (alpha) | ✅ Yes       | ✅ Preserved | Re-compress    |

## 🚀 Best Practices

1. **Untuk Logo/Icon dengan Transparency:**
    - Upload format: **PNG (RGBA)**
    - Rasio: 1:1 atau 16:9
    - Max size: 300x300px atau 512x512px

2. **Untuk Banner/Hero Image:**
    - Upload format: **JPG** atau **PNG**
    - Rasio: 16:9 atau 21:9
    - Max size: 1920x1080px

3. **Untuk Foto Produk:**
    - Upload format: **JPG** (ukuran lebih kecil)
    - Rasio: 4:3 atau 1:1
    - Max size: 1200x900px

## 📝 Changelog

### v2.0 (Current)

- ✅ **Fixed**: Initial crop sekarang full image coverage
- ✅ **Fixed**: Transparency preserved saat convert WebP
- ✅ **Added**: Auto-fit zoom calculation
- ✅ **Added**: Mouse wheel zoom (Ctrl + Scroll)
- ✅ **Improved**: Better crop UX dengan full image view

### v1.0

- ✅ Basic crop dengan Croppie
- ✅ WebP conversion
- ✅ Real-time preview
