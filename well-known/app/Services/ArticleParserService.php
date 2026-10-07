<?php
namespace App\Services;

class ArticleParserService
{
    /**
     * Converts raw text (markdown-like) to HTML
     *
     * @param string $text
     * @return string
     */
    public static function parse(string $text): string
    {
        // Normalize line endings
        $text = str_replace("\r\n", "\n", $text);
        $text = str_replace("\r", "\n", $text);

        // Remove more than 2 consecutive newlines
        $text = preg_replace("/\n{3,}/", "\n\n", $text);

        $lines = explode("\n", $text);
        $html = '';
        
        $inList = false;
        $listType = '';
        
        $specialHeaders = [
            'sonuç',
            'özet',
            'avantajlar',
            'neden tercih edilmeli?',
            'dikkat edilmesi gerekenler'
        ];

        $buffer = [];

        $flushBuffer = function() use (&$buffer, &$html) {
            if (!empty($buffer)) {
                $p = trim(implode("<br>\n", $buffer));
                if ($p !== '') {
                    $html .= "<p>{$p}</p>\n";
                }
                $buffer = [];
            }
        };

        foreach ($lines as $line) {
            $trimmed = trim($line);
            
            // Eğer boş satırsa
            if ($trimmed === '') {
                if ($inList) {
                    $html .= "</{$listType}>\n";
                    $inList = false;
                }
                $flushBuffer();
                continue;
            }

            // Shortcodes check (e.g. [info]...[/info], [image ...])
            // Shortcodes can be handled by ArticleRendererService later, 
            // but we must not wrap block shortcodes in <p> if they are standalone.
            // For now, if a line starts with [, we can just add it to HTML.
            if (preg_match('/^\[(info|warning|cta|package|service|image).*?\]/', $trimmed)) {
                $flushBuffer();
                if ($inList) { $html .= "</{$listType}>\n"; $inList = false; }
                
                // Parse inline formatting inside the shortcode text if it's block like [info]text[/info]
                $trimmed = self::parseInline($trimmed);
                $html .= $trimmed . "\n";
                continue;
            }

            // Headers
            if (preg_match('/^(#{1,6})\s+(.*)$/', $trimmed, $matches)) {
                $flushBuffer();
                if ($inList) { $html .= "</{$listType}>\n"; $inList = false; }
                $level = strlen($matches[1]);
                if ($level === 1) {
                    $level = 2; // Prevent duplicate H1 in the article body
                }
                $content = self::parseInline($matches[2]);
                $html .= "<h{$level}>{$content}</h{$level}>\n";
                continue;
            }
            
            // Special Headings
            if (in_array(mb_strtolower($trimmed, 'UTF-8'), $specialHeaders)) {
                $flushBuffer();
                if ($inList) { $html .= "</{$listType}>\n"; $inList = false; }
                $content = self::parseInline($trimmed);
                $html .= "<h2>{$content}</h2>\n";
                continue;
            }

            // Lists
            if (preg_match('/^([\*\-])\s+(.*)$/', $trimmed, $matches)) {
                $flushBuffer();
                if (!$inList || $listType !== 'ul') {
                    if ($inList) $html .= "</{$listType}>\n";
                    $listType = 'ul';
                    $html .= "<ul>\n";
                    $inList = true;
                }
                $content = self::parseInline($matches[2]);
                $html .= "<li>{$content}</li>\n";
                continue;
            }

            if (preg_match('/^(\d+)\.\s+(.*)$/', $trimmed, $matches)) {
                $flushBuffer();
                if (!$inList || $listType !== 'ol') {
                    if ($inList) $html .= "</{$listType}>\n";
                    $listType = 'ol';
                    $html .= "<ol>\n";
                    $inList = true;
                }
                $content = self::parseInline($matches[2]);
                $html .= "<li>{$content}</li>\n";
                continue;
            }

            // HTML content passed directly?
            if (preg_match('/^<(p|div|figure|blockquote|table|h1|h2|h3|h4|h5|h6|ul|ol|li).*?>/i', $trimmed)) {
                $flushBuffer();
                if ($inList) { $html .= "</{$listType}>\n"; $inList = false; }
                // We'll trust SanitizerService to clean it up later. Just output.
                $html .= self::parseInline($trimmed) . "\n";
                continue;
            }

            // Düz metin ise
            if ($inList) {
                $html .= "</{$listType}>\n";
                $inList = false;
            }
            $buffer[] = self::parseInline($trimmed);
        }

        $flushBuffer();
        if ($inList) {
            $html .= "</{$listType}>\n";
        }

        return trim($html);
    }

    private static function parseInline(string $text): string
    {
        // Bold: **text** or __text__
        $text = preg_replace('/(\*\*|__)(.*?)\1/', '<strong>$2</strong>', $text);
        
        // Italic: *text* or _text_
        // $text = preg_replace('/(\*|_)(.*?)\1/', '<em>$2</em>', $text);
        
        // Links: [title](url)
        $text = preg_replace('/\[([^\]]+)\]\(([^)]+)\)/', '<a href="$2">$1</a>', $text);

        return $text;
    }
}
