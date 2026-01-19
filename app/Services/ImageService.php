<?php

namespace App\Services;

use App\Jobs\ProcessImageJob;
use App\Models\ImageProcessingJob;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Interfaces\ImageInterface;

class ImageService
{
    protected ImageManager $manager;
    protected array $supportedFormats = ['jpg', 'jpeg', 'png', 'webp'];
    protected int $compressionQuality = 80;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver());
    }

    /**
     * Process uploaded image: edit, compress, and convert to WebP
     *
     * @param UploadedFile $file
     * @param string $path Storage path (e.g., 'images/content')
     * @param array $options ['width' => int, 'height' => int, 'crop' => bool, 'rotate' => int]
     * @return string|null Stored file path or null on failure
     */
    public function processAndStore(UploadedFile $file, string $path = 'images', array $options = []): ?string
    {
        // Validate file format
        if (!$this->isSupported($file)) {
            return null;
        }

        try {
            // Step 1: Load image
            $image = $this->manager->read($file->getPathname());

            // Step 2: Edit (resize, crop, rotate)
            $image = $this->applyEdits($image, $options);

            // Step 3: Compress (automatic quality control)
            $image = $this->compress($image);

            // Step 4: Convert to WebP and store
            return $this->convertAndStore($image, $path, $options['filename'] ?? null);

        } catch (\Exception $e) {
            \Log::error('Image processing failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Process base64 image (from cropper) with transparency support
     *
     * @param string $base64Data Base64 encoded image data
     * @param string $path Storage path (e.g., 'images/content')
     * @param array $options
     * @return string|null Stored file path or null on failure
     */
    public function processBase64AndStore(string $base64Data, string $path = 'images', array $options = []): ?string
    {
        try {
            // Remove data:image/png;base64, prefix if present
            if (str_contains($base64Data, 'base64,')) {
                $base64Data = explode('base64,', $base64Data)[1];
            }

            $imageData = base64_decode($base64Data);
            if (!$imageData) {
                return null;
            }

            // Load image from decoded data
            $image = $this->manager->read($imageData);

            // Apply any additional edits (usually not needed for pre-cropped images)
            if (!empty($options)) {
                $image = $this->applyEdits($image, $options);
            }

            // Convert to WebP with transparency support
            return $this->convertAndStore($image, $path, $options['filename'] ?? null);

        } catch (\Exception $e) {
            \Log::error('Base64 image processing failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Process uploaded image asynchronously using queue
     *
     * @param UploadedFile $file
     * @param string $path Storage path (e.g., 'images/content')
     * @param array $options ['width' => int, 'height' => int, 'crop' => bool, 'rotate' => int]
     * @return ImageProcessingJob|null Job tracking record
     */
    public function processAndStoreAsync(UploadedFile $file, string $path = 'images', array $options = []): ?ImageProcessingJob
    {
        // Validate file format
        if (!$this->isSupported($file)) {
            return null;
        }

        try {
            // Check max resolution before queuing
            $imageInfo = getimagesize($file->getPathname());
            if ($imageInfo !== false) {
                [$width, $height] = $imageInfo;
                $maxResolution = config('image.max_resolution', 8192);
                
                if ($width > $maxResolution || $height > $maxResolution) {
                    \Log::warning('Image exceeds max resolution', [
                        'width' => $width,
                        'height' => $height,
                        'max' => $maxResolution
                    ]);
                    return null;
                }
            }

            // Store temporary file
            $tempPath = storage_path('app/temp');
            if (!file_exists($tempPath)) {
                mkdir($tempPath, 0755, true);
            }

            $tempFilename = uniqid('img_') . '_' . time() . '.' . $file->getClientOriginalExtension();
            $tempFilePath = $tempPath . '/' . $tempFilename;
            $file->move($tempPath, $tempFilename);

            // Create job tracking record
            $jobRecord = ImageProcessingJob::create([
                'original_filename' => $file->getClientOriginalName(),
                'temp_path' => $tempFilePath,
                'storage_path' => $path,
                'options' => $options,
                'status' => 'pending',
                'queued_at' => now()
            ]);

            // Dispatch job to queue
            ProcessImageJob::dispatch(
                $tempFilePath,
                $path,
                $options,
                $jobRecord->id,
                $file->getClientOriginalName()
            );

            return $jobRecord;

        } catch (\Exception $e) {
            \Log::error('Image queue dispatch failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get image manager instance
     *
     * @return ImageManager
     */
    public function getManager(): ImageManager
    {
        return $this->manager;
    }

    /**
     * Apply editing operations (public for job access)
     *
     * @param ImageInterface $image
     * @param array $options
     * @return ImageInterface
     */
    public function applyEditsPublic(ImageInterface $image, array $options): ImageInterface
    {
        return $this->applyEdits($image, $options);
    }

    /**
     * Resize image to specific dimensions
     *
     * @param ImageInterface $image
     * @param int $width
     * @param int|null $height
     * @param bool $maintainAspectRatio
     * @return ImageInterface
     */
    public function resize(ImageInterface $image, int $width, ?int $height = null, bool $maintainAspectRatio = true): ImageInterface
    {
        if ($maintainAspectRatio) {
            return $image->scale(width: $width, height: $height);
        }

        return $image->resize($width, $height);
    }

    /**
     * Crop image to specific dimensions with predefined aspect ratio
     * 
     * RECOMMENDED FRONTEND APPROACH:
     * Use Cropper.js or similar library in blade templates to provide interactive crop experience:
     * - Ratio locked (1:1, 16:9, etc)
     * - User can zoom in/out
     * - User can drag/pan to select crop area
     * - Send crop coordinates (x, y, width, height) from frontend to backend
     * 
     * This method handles backend processing after user selects crop area
     *
     * @param ImageInterface $image
     * @param int $width
     * @param int $height
     * @param string $position center|top-left|top|top-right|left|right|bottom-left|bottom|bottom-right
     * @return ImageInterface
     */
    public function crop(ImageInterface $image, int $width, int $height, string $position = 'center'): ImageInterface
    {
        return $image->cover($width, $height, $position);
    }

    /**
     * Rotate image by degrees
     *
     * @param ImageInterface $image
     * @param int $degrees Rotation angle (0, 90, 180, 270)
     * @return ImageInterface
     */
    public function rotate(ImageInterface $image, int $degrees): ImageInterface
    {
        return $image->rotate($degrees);
    }

    /**
     * Compress image with automatic quality control
     *
     * @param ImageInterface $image
     * @param int|null $quality Quality percentage (1-100), defaults to 80
     * @return ImageInterface
     */
    public function compress(ImageInterface $image, ?int $quality = null): ImageInterface
    {
        // Automatic quality control - no admin interaction needed
        $quality = $quality ?? $this->compressionQuality;
        
        // Image is already loaded, quality will be applied during encoding
        return $image;
    }

    /**
     * Convert image to WebP and store
     *
     * @param ImageInterface $image
     * @param string $path
     * @param string|null $filename
     * @return string Stored file path
     */
    public function convertAndStore(ImageInterface $image, string $path, ?string $filename = null): string
    {
        // Generate unique filename if not provided
        $filename = $filename ?? uniqid() . '_' . time();
        $filename = pathinfo($filename, PATHINFO_FILENAME) . '.webp';
        
        $fullPath = $path . '/' . $filename;

        // Encode to WebP with compression quality
        // Note: Intervention Image with GD driver preserves transparency automatically
        // If transparency is lost, it means the source image doesn't have alpha channel
        $encoded = $image->toWebp($this->compressionQuality);

        // Store the optimized WebP image
        Storage::disk('public')->put($fullPath, (string) $encoded);

        return $fullPath;
    }

    /**
     * Apply editing operations to image
     *
     * @param ImageInterface $image
     * @param array $options
     * @return ImageInterface
     */
    protected function applyEdits(ImageInterface $image, array $options): ImageInterface
    {
        // Apply rotation if specified
        if (isset($options['rotate']) && in_array($options['rotate'], [90, 180, 270])) {
            $image = $this->rotate($image, $options['rotate']);
        }

        // Apply crop with user-selected coordinates from Cropper.js (priority)
        if (!empty($options['crop_coordinates']) && is_array($options['crop_coordinates'])) {
            $coords = $options['crop_coordinates'];
            
            // Crop to exact user selection
            $image = $image->crop(
                width: (int) $coords['width'],
                height: (int) $coords['height'],
                offset_x: (int) $coords['x'],
                offset_y: (int) $coords['y']
            );
            
            // Then resize to final output dimensions if specified
            if (isset($options['resize']) && is_array($options['resize'])) {
                $image = $this->resize(
                    $image, 
                    $options['resize'][0], 
                    $options['resize'][1] ?? null, 
                    false // Don't maintain aspect ratio - we want exact dimensions
                );
            }
        }
        // Apply crop with predefined position (fallback)
        elseif (!empty($options['crop']) && isset($options['width']) && isset($options['height'])) {
            $position = $options['crop_position'] ?? 'center';
            $image = $this->crop($image, $options['width'], $options['height'], $position);
        }
        // Apply resize if crop is not enabled but dimensions are specified
        elseif (isset($options['width']) || isset($options['height'])) {
            $width = $options['width'] ?? null;
            $height = $options['height'] ?? null;
            $maintainAspectRatio = $options['maintain_aspect_ratio'] ?? true;
            
            $image = $this->resize($image, $width ?? $image->width(), $height, $maintainAspectRatio);
        }

        return $image;
    }

    /**
     * Check if file format is supported
     *
     * @param UploadedFile $file
     * @return bool
     */
    public function isSupported(UploadedFile $file): bool
    {
        $extension = strtolower($file->getClientOriginalExtension());
        return in_array($extension, $this->supportedFormats);
    }

    /**
     * Get predefined aspect ratios for CMS
     *
     * @return array
     */
    public function getAspectRatios(): array
    {
        return [
            '16:9' => ['width' => 1920, 'height' => 1080, 'label' => 'Banner (16:9)'],
            '4:3' => ['width' => 1200, 'height' => 900, 'label' => 'Standard (4:3)'],
            '1:1' => ['width' => 1000, 'height' => 1000, 'label' => 'Square (1:1)'],
            '3:2' => ['width' => 1200, 'height' => 800, 'label' => 'Photo (3:2)'],
            '21:9' => ['width' => 2560, 'height' => 1080, 'label' => 'Ultra Wide (21:9)'],
        ];
    }

    /**
     * Set compression quality
     *
     * @param int $quality Quality percentage (1-100)
     * @return self
     */
    public function setQuality(int $quality): self
    {
        $this->compressionQuality = max(1, min(100, $quality));
        return $this;
    }

    /**
     * Process image from path (for reprocessing existing images)
     *
     * @param string $imagePath Storage path
     * @param array $options
     * @return string|null
     */
    public function processFromPath(string $imagePath, array $options = []): ?string
    {
        try {
            $fullPath = Storage::disk('public')->path($imagePath);
            
            if (!file_exists($fullPath)) {
                return null;
            }

            $image = $this->manager->read($fullPath);
            $image = $this->applyEdits($image, $options);
            $image = $this->compress($image);

            $path = dirname($imagePath);
            $filename = $options['filename'] ?? basename($imagePath);

            return $this->convertAndStore($image, $path, $filename);

        } catch (\Exception $e) {
            \Log::error('Image reprocessing failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Delete image from storage
     * MANDATORY: Use this method to delete images from all CRUD forms
     *
     * @param string|null $imagePath Storage path relative to public disk
     * @return bool True if deleted or path is null, false on failure
     */
    public function delete(?string $imagePath): bool
    {
        if (empty($imagePath)) {
            return true;
        }

        try {
            if (Storage::disk('public')->exists($imagePath)) {
                return Storage::disk('public')->delete($imagePath);
            }
            return true; // Path doesn't exist, consider it deleted
        } catch (\Exception $e) {
            \Log::error('Image deletion failed: ' . $e->getMessage(), ['path' => $imagePath]);
            return false;
        }
    }
}
