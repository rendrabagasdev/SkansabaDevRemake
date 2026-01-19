<?php

use App\Services\ImageService;
use App\Services\MarkdownService;
use App\Services\ContentStatusService;

if (!function_exists('imageService')) {
    /**
     * Get the ImageService instance
     *
     * @return ImageService
     */
    function imageService(): ImageService
    {
        return app(ImageService::class);
    }
}

if (!function_exists('markdown')) {
    /**
     * Parse markdown to safe HTML
     *
     * @param string|null $markdown
     * @param array $options
     * @return string
     */
    function markdown(?string $markdown, array $options = []): string
    {
        return app(MarkdownService::class)->parse($markdown, $options);
    }
}

if (!function_exists('markdownService')) {
    /**
     * Get the MarkdownService instance
     *
     * @return MarkdownService
     */
    function markdownService(): MarkdownService
    {
        return app(MarkdownService::class);
    }
}

if (!function_exists('contentStatus')) {
    /**
     * Get the ContentStatusService instance
     *
     * @return ContentStatusService
     */
    function contentStatus(): ContentStatusService
    {
        return app(ContentStatusService::class);
    }
}

if (!function_exists('statusBadge')) {
    /**
     * Generate status badge HTML
     *
     * @param string $status
     * @param string|null $label
     * @return string
     */
    function statusBadge(string $status, ?string $label = null): string
    {
        $label = $label ?? match($status) {
            'draft' => 'Draft',
            'review' => 'In Review',
            'published' => 'Published',
            'archived' => 'Archived',
            default => ucfirst($status)
        };

        $colorClasses = match($status) {
            'draft' => 'bg-gray-100 text-gray-800',
            'review' => 'bg-yellow-100 text-yellow-800',
            'published' => 'bg-green-100 text-green-800',
            'archived' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };

        return "<span class=\"inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {$colorClasses}\">{$label}</span>";
    }
}
