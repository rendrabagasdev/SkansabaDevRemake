<?php

namespace App\Services;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\Extension\Strikethrough\StrikethroughExtension;
use League\CommonMark\Extension\TaskList\TaskListExtension;
use League\CommonMark\MarkdownConverter;

class MarkdownService
{
    protected MarkdownConverter $converter;
    protected array $allowedTags = [
        'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
        'p', 'br',
        'strong', 'em', 'u', 's', 'del',
        'ul', 'ol', 'li',
        'blockquote',
        'a',
        'table', 'thead', 'tbody', 'tr', 'th', 'td',
        'hr',
        'code', 'pre',
    ];

    public function __construct()
    {
        $config = [
            'html_input' => 'strip',           // Strip all raw HTML
            'allow_unsafe_links' => false,     // Block javascript: and data: URLs
            'max_nesting_level' => 10,         // Prevent deeply nested structures
        ];

        $environment = new Environment($config);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new TableExtension());
        $environment->addExtension(new StrikethroughExtension());
        $environment->addExtension(new TaskListExtension());

        $this->converter = new MarkdownConverter($environment);
    }

    /**
     * Parse markdown to safe HTML for institutional content
     *
     * @param string|null $markdown
     * @param array $options
     * @return string
     */
    public function parse(?string $markdown, array $options = []): string
    {
        if (empty($markdown)) {
            return '';
        }

        // Convert markdown to HTML
        $html = (string) $this->converter->convert($markdown);

        // Additional sanitization
        $html = $this->sanitize($html);

        // Apply institutional styling classes if requested
        if ($options['style'] ?? true) {
            $html = $this->applyInstitutionalStyling($html);
        }

        return $html;
    }

    /**
     * Parse markdown for kurikulum (curriculum) content
     *
     * @param string|null $markdown
     * @return string
     */
    public function parseKurikulum(?string $markdown): string
    {
        return $this->parse($markdown, ['style' => true]);
    }

    /**
     * Parse markdown for dokumen (document) content
     *
     * @param string|null $markdown
     * @return string
     */
    public function parseDokumen(?string $markdown): string
    {
        return $this->parse($markdown, ['style' => true]);
    }

    /**
     * Parse markdown for deskripsi (description) content
     *
     * @param string|null $markdown
     * @return string
     */
    public function parseDeskripsi(?string $markdown): string
    {
        return $this->parse($markdown, ['style' => true]);
    }

    /**
     * Sanitize HTML output - remove scripts, dangerous attributes
     *
     * @param string $html
     * @return string
     */
    protected function sanitize(string $html): string
    {
        // Remove any script tags that might have slipped through
        $html = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $html);
        
        // Remove event handlers (onclick, onerror, etc.)
        $html = preg_replace('/\s*on\w+\s*=\s*["\'][^"\']*["\']/i', '', $html);
        
        // Remove style attributes (inline styles not allowed)
        $html = preg_replace('/\s*style\s*=\s*["\'][^"\']*["\']/i', '', $html);
        
        // Remove javascript: and data: protocols from links
        $html = preg_replace('/href\s*=\s*["\'](?:javascript|data):[^"\']*["\']/i', 'href="#"', $html);
        
        // Strip tags not in allowed list
        $html = strip_tags($html, '<' . implode('><', $this->allowedTags) . '>');

        return $html;
    }

    /**
     * Apply institutional styling classes to HTML elements
     *
     * @param string $html
     * @return string
     */
    protected function applyInstitutionalStyling(string $html): string
    {
        // Add Tailwind classes for institutional formal styling
        $replacements = [
            // Headings
            '<h1>' => '<h1 class="text-3xl font-bold text-gray-900 mb-4 mt-6">',
            '<h2>' => '<h2 class="text-2xl font-bold text-gray-800 mb-3 mt-5">',
            '<h3>' => '<h3 class="text-xl font-semibold text-gray-800 mb-3 mt-4">',
            '<h4>' => '<h4 class="text-lg font-semibold text-gray-700 mb-2 mt-3">',
            '<h5>' => '<h5 class="text-base font-semibold text-gray-700 mb-2 mt-3">',
            '<h6>' => '<h6 class="text-sm font-semibold text-gray-600 mb-2 mt-2">',
            
            // Paragraphs
            '<p>' => '<p class="text-gray-700 leading-relaxed mb-4">',
            
            // Lists
            '<ul>' => '<ul class="list-disc list-inside space-y-2 mb-4 text-gray-700">',
            '<ol>' => '<ol class="list-decimal list-inside space-y-2 mb-4 text-gray-700">',
            '<li>' => '<li class="ml-4">',
            
            // Blockquote
            '<blockquote>' => '<blockquote class="border-l-4 border-blue-600 pl-4 py-2 mb-4 italic text-gray-600 bg-gray-50">',
            
            // Links
            '<a ' => '<a class="text-blue-700 hover:text-blue-900 underline font-medium" ',
            
            // Tables
            '<table>' => '<table class="min-w-full border-collapse border border-gray-300 mb-4">',
            '<thead>' => '<thead class="bg-gray-100">',
            '<th>' => '<th class="border border-gray-300 px-4 py-2 text-left font-semibold text-gray-800">',
            '<td>' => '<td class="border border-gray-300 px-4 py-2 text-gray-700">',
            
            // Code
            '<code>' => '<code class="bg-gray-100 text-red-600 px-1 rounded text-sm font-mono">',
            '<pre>' => '<pre class="bg-gray-100 p-4 rounded-lg overflow-x-auto mb-4"><code class="text-sm font-mono text-gray-800">',
            '</pre>' => '</code></pre>',
            
            // Horizontal rule
            '<hr>' => '<hr class="my-6 border-gray-300">',
            '<hr/>' => '<hr class="my-6 border-gray-300"/>',
        ];

        foreach ($replacements as $search => $replace) {
            $html = str_replace($search, $replace, $html);
        }

        return $html;
    }

    /**
     * Convert HTML back to markdown (for editing)
     *
     * @param string $html
     * @return string
     */
    public function toMarkdown(string $html): string
    {
        // Strip styling classes before conversion
        $html = preg_replace('/\s*class\s*=\s*["\'][^"\']*["\']/i', '', $html);
        
        // Basic HTML to markdown conversion
        $markdown = $html;
        
        // This is a simple implementation
        // For complex HTML to Markdown, consider using league/html-to-markdown
        return $markdown;
    }

    /**
     * Validate markdown content
     *
     * @param string|null $markdown
     * @return bool
     */
    public function isValid(?string $markdown): bool
    {
        if (empty($markdown)) {
            return true;
        }

        try {
            $this->converter->convert($markdown);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get allowed HTML tags
     *
     * @return array
     */
    public function getAllowedTags(): array
    {
        return $this->allowedTags;
    }

    /**
     * Normalize markdown content before saving
     * MANDATORY: Call this method before saving markdown to database in all CRUD forms
     *
     * @param string|null $markdown
     * @return string Normalized markdown
     */
    public function normalize(?string $markdown): string
    {
        if (empty($markdown)) {
            return '';
        }

        $normalized = $markdown;

        // 1. Remove empty bold/italic markers
        $normalized = preg_replace('/\*\*\s*\*\*/', '', $normalized);
        $normalized = preg_replace('/__\s*__/', '', $normalized);
        $normalized = preg_replace('/\*\s*\*(?!\*)/', '', $normalized);
        $normalized = preg_replace('/_\s*_(?!_)/', '', $normalized);

        // 2. Fix broken image syntax ![image](url) - remove if incomplete
        $normalized = preg_replace('/!\[image\]\(url\)/', '', $normalized);
        $normalized = preg_replace('/!\[\]\([^)]*\)/', '', $normalized);
        $normalized = preg_replace('/!\[[^\]]*\]\(\s*\)/', '', $normalized);

        // 3. Normalize heading spacing (# heading)
        $normalized = preg_replace('/#+(\S)/', '# $1', $normalized); // Add space after #
        $normalized = preg_replace('/^(#{1,6})\s+/m', '$1 ', $normalized); // Single space only

        // 4. Normalize list spacing
        $normalized = preg_replace('/^([*+-])([^\s])/', '$1 $2', $normalized); // Add space after bullet
        $normalized = preg_replace('/^(\d+\.)([^\s])/', '$1 $2', $normalized); // Add space after number

        // 5. Remove excessive blank lines (max 2 consecutive)
        $normalized = preg_replace('/\n{3,}/', "\n\n", $normalized);

        // 6. Trim each line
        $lines = explode("\n", $normalized);
        $lines = array_map('rtrim', $lines);
        $normalized = implode("\n", $lines);

        // 7. Remove trailing whitespace at end of document
        $normalized = rtrim($normalized);

        // 8. Ensure single newline at end of document
        $normalized = $normalized . "\n";

        return $normalized;
    }

    /**
     * Extract plain text from markdown (no HTML)
     *
     * @param string|null $markdown
     * @param int|null $limit Character limit
     * @return string
     */
    public function toPlainText(?string $markdown, ?int $limit = null): string
    {
        if (empty($markdown)) {
            return '';
        }

        // Convert to HTML first
        $html = (string) $this->converter->convert($markdown);
        
        // Strip all tags
        $text = strip_tags($html);
        
        // Remove extra whitespace
        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);

        // Apply character limit if specified
        if ($limit && strlen($text) > $limit) {
            $text = substr($text, 0, $limit) . '...';
        }

        return $text;
    }

    /**
     * Generate excerpt from markdown
     *
     * @param string|null $markdown
     * @param int $words Word limit (default: 30)
     * @return string
     */
    public function excerpt(?string $markdown, int $words = 30): string
    {
        if (empty($markdown)) {
            return '';
        }

        $plainText = $this->toPlainText($markdown);
        $wordsArray = explode(' ', $plainText);

        if (count($wordsArray) <= $words) {
            return $plainText;
        }

        return implode(' ', array_slice($wordsArray, 0, $words)) . '...';
    }
}
