<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Image Processing Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for image processing and conversion
    |
    */

    // Maximum image resolution (width or height in pixels)
    'max_resolution' => env('IMAGE_MAX_RESOLUTION', 8192),

    // Compression quality (1-100)
    'compression_quality' => env('IMAGE_COMPRESSION_QUALITY', 80),

    // Supported input formats
    'supported_formats' => ['jpg', 'jpeg', 'png', 'webp'],

    // Queue connection for async processing
    'queue_connection' => env('IMAGE_QUEUE_CONNECTION', 'default'),

    // Queue name
    'queue_name' => env('IMAGE_QUEUE_NAME', 'default'),

    // Enable fallback to original image on processing failure
    'enable_fallback' => env('IMAGE_ENABLE_FALLBACK', true),

    // Temporary storage path
    'temp_path' => storage_path('app/temp'),

    // Cleanup temporary files after processing
    'cleanup_temp_files' => env('IMAGE_CLEANUP_TEMP', true),

    // Job retry attempts
    'job_retry_attempts' => env('IMAGE_JOB_RETRY', 3),

    // Job timeout (seconds)
    'job_timeout' => env('IMAGE_JOB_TIMEOUT', 120),
];
