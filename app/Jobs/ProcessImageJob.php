<?php

namespace App\Jobs;

use App\Models\ImageProcessingJob;
use App\Services\ImageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     */
    public int $timeout = 120;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $tempFilePath,
        public string $storagePath,
        public array $options = [],
        public ?int $jobRecordId = null,
        public ?string $originalFilename = null
    ) {}

    /**
     * Execute the job.
     */
    public function handle(ImageService $imageService): void
    {
        $jobRecord = $this->getJobRecord();

        try {
            // Update status to processing
            $this->updateJobStatus($jobRecord, 'processing');

            // Validate file exists
            if (!file_exists($this->tempFilePath)) {
                throw new \Exception('Temporary file not found: ' . $this->tempFilePath);
            }

            // Check max resolution safety limits
            $imageInfo = getimagesize($this->tempFilePath);
            if ($imageInfo === false) {
                throw new \Exception('Invalid image file');
            }

            [$width, $height] = $imageInfo;
            $maxResolution = config('image.max_resolution', 8192); // 8K max
            
            if ($width > $maxResolution || $height > $maxResolution) {
                throw new \Exception("Image resolution exceeds maximum allowed ({$maxResolution}px)");
            }

            // Load image
            $image = $imageService->getManager()->read($this->tempFilePath);

            // Step 1: Apply edits (crop, resize, rotate)
            $image = $imageService->applyEditsPublic($image, $this->options);

            // Step 2: Compress (automatic quality control)
            $image = $imageService->compress($image);

            // Step 3: Convert to WebP and store
            $filename = $this->options['filename'] ?? $this->originalFilename ?? pathinfo($this->tempFilePath, PATHINFO_FILENAME);
            $storedPath = $imageService->convertAndStore($image, $this->storagePath, $filename);

            // Update job record with success
            $this->updateJobStatus($jobRecord, 'completed', [
                'output_path' => $storedPath,
                'completed_at' => now()
            ]);

            // Clean up temporary file
            if (file_exists($this->tempFilePath)) {
                @unlink($this->tempFilePath);
            }

            Log::info('Image processing completed', [
                'job_id' => $this->jobRecordId,
                'output_path' => $storedPath
            ]);

        } catch (\Exception $e) {
            Log::error('Image processing failed', [
                'job_id' => $this->jobRecordId,
                'error' => $e->getMessage(),
                'file' => $this->tempFilePath
            ]);

            // Update job record with failure
            $this->updateJobStatus($jobRecord, 'failed', [
                'error_message' => $e->getMessage(),
                'failed_at' => now()
            ]);

            // Handle fallback - copy original image if exists
            $this->handleFallback($jobRecord);

            // Re-throw to mark job as failed in queue
            throw $e;
        }
    }

    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception): void
    {
        $jobRecord = $this->getJobRecord();

        if ($jobRecord) {
            $jobRecord->update([
                'status' => 'failed',
                'error_message' => $exception->getMessage(),
                'failed_at' => now()
            ]);
        }

        // Handle fallback
        $this->handleFallback($jobRecord);

        Log::error('Image processing job failed permanently', [
            'job_id' => $this->jobRecordId,
            'exception' => $exception->getMessage()
        ]);
    }

    /**
     * Get job record from database
     */
    protected function getJobRecord(): ?ImageProcessingJob
    {
        if (!$this->jobRecordId) {
            return null;
        }

        return ImageProcessingJob::find($this->jobRecordId);
    }

    /**
     * Update job status
     */
    protected function updateJobStatus(?ImageProcessingJob $jobRecord, string $status, array $data = []): void
    {
        if (!$jobRecord) {
            return;
        }

        $jobRecord->update(array_merge(['status' => $status], $data));
    }

    /**
     * Handle fallback - store original image if processing fails
     */
    protected function handleFallback(?ImageProcessingJob $jobRecord): void
    {
        if (!$jobRecord || !file_exists($this->tempFilePath)) {
            return;
        }

        try {
            // Copy original file to storage as fallback
            $filename = $this->originalFilename ?? basename($this->tempFilePath);
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            $fallbackFilename = pathinfo($filename, PATHINFO_FILENAME) . '_original.' . $extension;
            $fallbackPath = $this->storagePath . '/' . $fallbackFilename;

            $content = file_get_contents($this->tempFilePath);
            Storage::disk('public')->put($fallbackPath, $content);

            $jobRecord->update([
                'fallback_path' => $fallbackPath,
                'fallback_used' => true
            ]);

            Log::info('Fallback image stored', [
                'job_id' => $this->jobRecordId,
                'fallback_path' => $fallbackPath
            ]);

        } catch (\Exception $e) {
            Log::error('Fallback storage failed', [
                'job_id' => $this->jobRecordId,
                'error' => $e->getMessage()
            ]);
        }
    }
}
